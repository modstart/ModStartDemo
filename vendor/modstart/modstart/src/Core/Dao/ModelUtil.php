<?php

namespace ModStart\Core\Dao;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Util\ArrayUtil;
use ModStart\Core\Util\PageHtmlUtil;
use ModStart\Core\Util\SerializeUtil;


class ModelUtil
{


    
    public static function isModel($tableOrModel)
    {
        if (!is_string($tableOrModel)) {
            return true;
        }
        return strtolower($tableOrModel) != $tableOrModel;
    }

    
    public static function autoModel($tableOrModel)
    {
        if (self::isModel($tableOrModel)) {
            if (is_string($tableOrModel)) {
                return new $tableOrModel();
            }
            return $tableOrModel;
        }
        return self::model($tableOrModel);
    }

    
    public static function model($model)
    {
        if (is_object($model)) {
            return $model;
        }
                if (preg_match('/\\\\Model\\\\(.*)$/', $model)) {
            if (class_exists($model)) {
                return new $model();
            }
        }
        $m = new DynamicModel();
        $m->setTable($model);
        return $m;
    }


    
    public static function insert($model, $data)
    {
        $m = self::model($model);
        foreach ($data as $k => $v) {
            $m->$k = $v;
        }
        $m->save();
        return $m->toArray();
    }

    private static function insertAllBuild($records, $updateTimestamp = true)
    {
        if ($updateTimestamp) {
            foreach ($records as $i => $data) {
                if (!isset($data['created_at'])) {
                    $records[$i]['created_at'] = date('Y-m-d H:i:s');
                }
                if (!isset($data['updated_at'])) {
                    $records[$i]['updated_at'] = date('Y-m-d H:i:s');
                }
            }
        }
        return $records;
    }

    
    public static function insertAll($model, $records, $updateTimestamp = true)
    {
        if (empty($records)) {
            return;
        }
        $records = self::insertAllBuild($records, $updateTimestamp);
        $m = self::model($model);
        DB::table($m->getTable())->insert($records);
    }

    
    public static function insertAllSafely($model, $records, $updateTimestamp = true)
    {
        if (empty($records)) {
            return;
        }
        $records = self::insertAllBuild($records, $updateTimestamp);
        $m = self::model($model);
        $batchFailed = false;
        try {
            DB::table($m->getTable())->insert($records);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            if (Str::contains($message, 'Duplicate entry')) {
                $batchFailed = true;
            } else {
                Log::info('ModelUtil.insertAllSafely.BatchError - ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
                throw $e;
            }
        }
        if ($batchFailed) {
            foreach ($records as $record) {
                try {
                    DB::table($m->getTable())->insert($record);
                } catch (\Exception $e) {
                    $message = $e->getMessage();
                    if (Str::contains($message, 'Duplicate entry')) {
                        continue;
                    } else {
                        Log::info('ModelUtil.insertAllSafely.OneError - ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
                        throw $e;
                    }
                }
            }
        }
    }

    
    public static function delete($model, $where)
    {
        if (is_string($where) || is_numeric($where)) {
            $where = ['id' => $where];
        }
        return self::model($model)->where($where)->delete();
    }

    
    public static function deleteOperator($model, $field, $operator, $value)
    {
        return self::model($model)->where($field, $operator, $value)->delete();
    }

    
    public static function deleteIn($model, $values, $field = 'id')
    {
        if (empty($values)) {
            return 0;
        }
        return self::model($model)->whereIn($field, $values)->delete();
    }

    
    public static function updateAll($model, $data, $where = ['id', '>', '0'])
    {
        self::model($model)->where($where[0], $where[1], $where[2])->update($data);
    }

    
    public static function update($model, $where, $data)
    {
        if (empty($where) || empty($data)) {
            return 0;
        }
        if (is_string($where) || is_numeric($where)) {
            $where = ['id' => $where];
        }
        return self::model($model)->where($where)->update($data);
    }

    public static function first($model, $where, $fields = ['*'], $order = null)
    {
        if (is_string($where) || is_numeric($where)) {
            $where = ['id' => $where];
        }
        if ($order) {
            $m = self::model($model)->where($where)->orderBy($order[0], $order[1])->first($fields);
        } else {
            $m = self::model($model)->where($where)->first($fields);
        }
        if (empty($m)) {
            return null;
        }
        return $m->toArray();
    }

    
    public static function get($model, $where, $fields = ['*'], $order = null)
    {
        if (null === $where) {
            return null;
        }
        if (is_string($where) || is_numeric($where)) {
            $where = ['id' => $where];
        }
        $model = self::model($model)->where($where);
        if (!empty($order)) {
            $model = $model->orderBy($order[0], $order[1]);
        }
        $m = $model->first($fields);
        if (empty($m)) {
            return null;
        }
        return $m->toArray();
    }

    public static function getOr404($model, $where, $fields = ['*'], $order = null)
    {
        $one = self::get($model, $where, $fields, $order);
        if (empty($one)) {
            abort(404, L('Page Not Found'));
        }
        return $one;
    }

    public static function getOrCreate($model, $where)
    {
        if (is_string($where) || is_numeric($where)) {
            $where = ['id' => $where];
        }
        $m = self::model($model)->where($where)->first();
        if (empty($m)) {
            self::insert($model, $where);
            $m = self::model($model)->where($where)->first();
        }
        return $m->toArray();
    }

    public static function getWithCache($model, $where)
    {
        static $map = [];
        $flag = serialize(['model' => $model, 'where' => $where]);
        if (!array_key_exists($flag, $map)) {
            $map[$flag] = self::get($model, $where);
        }
        return $map[$flag];
    }

    public static function getWithLock($model, $where, $fields = ['*'])
    {
        if (is_string($where) || is_numeric($where)) {
            $where = ['id' => $where];
        }
        $m = self::model($model)->where($where)->lockForUpdate()->first($fields);
        if (empty($m)) {
            return null;
        }
        return $m->toArray();
    }

    public static function count($model, $where = [])
    {
        if (is_string($where) || is_numeric($where)) {
            $where = ['id' => $where];
        }
        if (empty($where)) {
            return self::model($model)->count();
        }
        return self::model($model)->where($where)->count();
    }

    public static function exists($model, $where)
    {
        return !!self::get($model, $where);
    }

    public static function batch($model, $nextId, $batchSize = 1000, $where = [], $fields = ['*'], $idName = 'id', $idSort = 'asc')
    {
        $records = self::model($model)->where($where)->where($idName, $idSort == 'asc' ? '>' : '<', $nextId)->limit($batchSize)->orderBy($idName, $idSort)->get($fields)->toArray();
        foreach ($records as $item) {
            switch ($idSort) {
                case 'asc':
                    $nextId = max($nextId, $item[$idName]);
                    break;
                default:
                    $nextId = min($nextId, $item[$idName]);
                    break;
            }
        }
        return [
            'records' => $records,
            'nextId' => $nextId,
        ];
    }

    public static function allFirstGroupOrder($table, $whereRaw, $groupByField, $orderByField)
    {
        $table = ModelManageUtil::table($table);
        return ModelManageUtil::query("SELECT * FROM (SELECT * FROM $table WHERE $whereRaw ORDER BY $orderByField DESC) AS t GROUP BY $groupByField");
    }

    public static function all($model, $where = [], $fields = ['*'], $order = null)
    {
        if ($order) {
            return self::model($model)->where($where)->orderBy($order[0], $order[1])->get($fields)->toArray();
        }
        return self::model($model)->where($where)->get($fields)->toArray();
    }

    public static function allMap($model, $where = [], $fields = ['*'], $order = null, $mapId = 'id')
    {
        return array_build(self::all($model, $where, $fields, $order), function ($k, $v) use ($mapId) {
            return [$v[$mapId], $v];
        });
    }

    public static function allIn($model, $field, $in, $fields = ['*'])
    {
        if (empty($in)) {
            return [];
        }
        return self::model($model)->whereIn($field, $in)->get($fields)->toArray();
    }

    public static function allInWithOrder($model, $field, $in, $fields = ['*'])
    {
        if (empty($in)) {
            return [];
        }
        $map = array_build(self::allIn($model, $field, $in, $fields), function ($k, $v) use ($field) {
            return [$v[$field], $v];
        });
        $records = [];
        foreach ($in as $item) {
            if (isset($map[$item])) {
                $records[] = $map[$item];
            }
        }
        return $records;
    }

    public static function allInMap($model, $field, $in, $fields = ['*'], $mapId = 'id')
    {
        return array_build(self::allIn($model, $field, $in, $fields), function ($k, $v) use ($mapId) {
            return [$v[$mapId], $v];
        });
    }

    public static function allIds($model, $where, $limit = -1, $idKey = 'id')
    {
        $query = self::model($model)->where($where);
        if ($limit > 0) {
            $query = $query->limit($limit);
        }
        $records = $query->get([$idKey]);
        return ArrayUtil::flatItemsByKey($records, $idKey);
    }

    public static function values($model, $field, $where = [], $order = null)
    {
        $flat = false;
        if (!is_array($field)) {
            $fields = [$field];
            $flat = true;
        } else {
            $fields = $field;
        }
        $ms = self::model($model)->where($where);
        if (!empty($order)) {
            $ms = $ms->orderBy($order[0], $order[1]);
        }
        $ms = $ms->get($fields)->toArray();
        if ($flat) {
            return array_map(function ($item) use ($field) {
                return $item[$field];
            }, $ms);
        }
        return $ms;
    }

    public static function valueMap($model, $fieldKey, $fieldValue, $where = [])
    {
        $ms = self::model($model)->where($where)->get([$fieldKey, $fieldValue])->toArray();
        return array_build($ms, function ($k, $v) use ($fieldKey, $fieldValue) {
            return [$v[$fieldKey], $v[$fieldValue]];
        });
    }

    
    public static function relationList($model, $sourceField, $sourceValue, $filter = [], $extraFields = [], $idField = 'id')
    {
        return self::all($model, array_merge([$sourceField => $sourceValue], $filter), array_merge([$idField, $sourceField], $extraFields));
    }

    
    public static function relationClean($model, $field, $value)
    {
        return self::model($model)->where($field, $field)->delete();
    }

    
    public static function relationAssign($model, $sourceField, $sourceValue, $targetField, $targetValues, $filter = [], $idField = 'id')
    {
        if (empty($targetValues)) {
            $targetValues = [];
        }
        $relations = self::all($model, array_merge([$sourceField => $sourceValue], $filter), [$idField, $targetField]);
        $deletes = [];
        $inserts = [];
        $existsTargetMap = [];
        foreach ($relations as $relation) {
            $existsTargetMap[$relation[$targetField]] = true;
            if (!in_array($relation[$targetField], $targetValues)) {
                $deletes[] = $relation[$idField];
                continue;
            }
        }
        foreach ($targetValues as $targetValue) {
            if (!isset($existsTargetMap[$targetValue])) {
                $inserts[] = array_merge([
                    $sourceField => $sourceValue,
                    $targetField => $targetValue,
                ], $filter);
            }
        }
        $changed = false;
        if (!empty($deletes)) {
            self::deleteIn($model, $deletes, $idField);
            $changed = true;
        }
        if (!empty($inserts)) {
            self::insertAll($model, $inserts);
            $changed = true;
        }
        return $changed;
    }

    public static function next($model, $current, $where = [], $sortField = 'id', $fields = ['*'])
    {
        $record = self::model($model)
            ->where($where)
            ->where($sortField, '>', $current)
            ->orderBy($sortField, 'asc')
            ->first($fields);
        return $record ? $record->toArray() : null;
    }

    public static function prev($model, $current, $where = [], $sortField = 'id', $fields = ['*'])
    {
        $record = self::model($model)
            ->where($where)
            ->where($sortField, '<', $current)
            ->orderBy($sortField, 'desc')
            ->first($fields);
        return $record ? $record->toArray() : null;
    }

    public static function sortNext($model, $where = [], $sortField = 'sort')
    {
        return intval(self::model($model)->where($where)->max($sortField)) + 1;
    }

    public static function sortMove($model, $id, $direction = 'up|down|top|bottom', $filter = [], $idField = 'id', $sortField = 'sort')
    {
        if (!in_array($direction, ['up', 'down', 'top', 'bottom'])) {
            return false;
        }
        $exists = self::all($model, $filter, [$idField, $sortField], [$sortField, 'asc']);
        $existsIndex = -1;
        foreach ($exists as $index => $exist) {
            if ($exist[$idField] == $id) {
                $existsIndex = $index;
                break;
            }
        }
        if ($existsIndex < 0) {
            return false;
        }
        switch ($direction) {
            case 'up':
                if ($existsIndex > 0) {
                    self::update($model, $exists[$existsIndex][$idField], [$sortField => $exists[$existsIndex - 1][$sortField]]);
                    self::update($model, $exists[$existsIndex - 1][$idField], [$sortField => $exists[$existsIndex][$sortField]]);
                    return true;
                }
                break;
            case 'down':
                if ($existsIndex < count($exists) - 1) {
                    self::update($model, $exists[$existsIndex][$idField], [$sortField => $exists[$existsIndex + 1][$sortField]]);
                    self::update($model, $exists[$existsIndex + 1][$idField], [$sortField => $exists[$existsIndex][$sortField]]);
                    return true;
                }
                break;
            case 'top':
                if ($existsIndex > 0) {
                    $number = 2;
                    foreach ($exists as $index => $exist) {
                        if ($index == $existsIndex) {
                            self::update($model, $exist[$idField], [$sortField => 1]);
                        } else {
                            self::update($model, $exist[$idField], [$sortField => $number]);
                            $number++;
                        }
                    }
                    return true;
                }
                break;
            case 'bottom':
                if ($existsIndex < count($exists) - 1) {
                    $number = 1;
                    foreach ($exists as $index => $exist) {
                        if ($index == $existsIndex) {
                            continue;
                        }
                        self::update($model, $exist[$idField], [$sortField => $number]);
                        $number++;
                    }
                    self::update($model, $exists[$existsIndex][$idField], [$sortField => $number]);
                    return true;
                }
                break;
        }
        return false;
    }

    
    public static function join(&$records, $dataModelKey = 'userId', $dataMergedKey = '_user', $model = 'join_model', $modelPrimaryKey = 'id')
    {
        if (empty($records)) {
            return;
        }

        $ids = array_map(function ($item) use ($dataModelKey) {
            return $item[$dataModelKey];
        }, $records);

        $joinData = self::model($model)->whereIn($modelPrimaryKey, $ids)->get()->toArray();

        $joinDataMap = array_build($joinData, function ($k, $v) use ($modelPrimaryKey) {
            return [$v[$modelPrimaryKey], $v];
        });

        foreach ($records as &$item) {
            $key = $item[$dataModelKey];
            if (isset($joinDataMap[$key])) {
                $item[$dataMergedKey] = $joinDataMap[$key];
            } else {
                $item[$dataMergedKey] = null;
            }
        }
    }

    public static function joinItems(&$items, $dataModelKey = 'userId', $dataMergedKey = '_user', $model = 'join_model', $modelPrimaryKey = 'id')
    {
        if (empty($items)) {
            return;
        }

        $ids = [];
        foreach ($items as $item) {
            $ids[] = $item->{$dataModelKey};
        }
        $ids = array_unique($ids);

        $joinData = self::model($model)->whereIn($modelPrimaryKey, $ids)->get()->toArray();

        $joinDataMap = array_build($joinData, function ($k, $v) use ($modelPrimaryKey) {
            return [$v[$modelPrimaryKey], $v];
        });

        foreach ($items as &$item) {
            $key = $item->{$dataModelKey};
            if (isset($joinDataMap[$key])) {
                $item->{$dataMergedKey} = $joinDataMap[$key];
            } else {
                $item->{$dataMergedKey} = null;
            }
        }
    }


    public static function joinAll(&$data, $dataModelKey = 'userId', $dataMergedKey = '_user', $model = 'join_model', $modelPrimaryKey = 'id')
    {
        if (empty($data)) {
            return;
        }

        $ids = array_map(function ($item) use ($dataModelKey) {
            return $item[$dataModelKey];
        }, $data);

        $joinData = self::model($model)->whereIn($modelPrimaryKey, $ids)->get()->toArray();
        $joinDataMap = [];
        foreach ($joinData as $item) {
            if (array_key_exists($item[$modelPrimaryKey], $joinDataMap)) {
                $joinDataMap[$item[$modelPrimaryKey]][] = $item;
            } else {
                $joinDataMap[$item[$modelPrimaryKey]] = [$item];
            }
        }

        foreach ($data as &$item) {
            $key = $item[$dataModelKey];
            if (isset($joinDataMap[$key])) {
                $item[$dataMergedKey] = $joinDataMap[$key];
            } else {
                $item[$dataMergedKey] = [];
            }
        }
    }

    public static function paginateMergeConditionParam(&$o, $option)
    {

        if (!empty($option['whereIn'])) {
            if (is_array($option['whereIn'][0])) {
                foreach ($option['whereIn'] as &$whereIn) {
                    $o = $o->whereIn($whereIn[0], $whereIn[1]);
                }
            } else {
                $o = $o->whereIn($option['whereIn'][0], $option['whereIn'][1]);
            }
        }

        if (!empty($option['whereOperate'])) {
            if (is_array($option['whereOperate'][0])) {
                foreach ($option['whereOperate'] as &$whereOperate) {
                    $o = $o->where($whereOperate[0], $whereOperate[1], $whereOperate[2]);
                }
            } else {
                $o = $o->where($option['whereOperate'][0], $option['whereOperate'][1], $option['whereOperate'][2]);
            }
        }

        if (!empty($option['where'])) {
            if (is_array($option['where'])) {
                $o = $o->where($option['where']);
            } else {
                $o = $o->whereRaw($option['where']);
            }
        }

        if (!empty($option['whereRaw'])) {
            if (is_array($option['whereRaw'])) {
                $o = $o->whereRaw($option['whereRaw'][0], $option['whereRaw'][1]);
            } else {
                $o = $o->whereRaw($option['whereRaw']);
            }
        }

        if (!empty($option['search']) && is_array($option['search'])) {
            self::querySearchExecute($o, $option['search']);
        }

        if (!empty($option['filter']) && is_array($option['filter'])) {
            self::queryFilterExecute($o, $option['filter']);
        }
    }

    
    public static function querySearchExecute(&$query, $searches)
    {
        foreach ($searches as $searchItem) {

            if (!isset($searchItem['__exp'])) {
                $searchItem['__exp'] = 'and';
            } else {
                $searchItem['__exp'] = strtolower($searchItem['__exp']);
            }

            $whereExpFirst = true;
            $whereExp = 'where';
            if ($searchItem['__exp'] == 'or') {
                $whereExp = 'orWhere';
            }

            $query = $query->where(function ($queryBase) use (&$searchItem, $whereExpFirst, $whereExp) {

                foreach ($searchItem as $field => $searchInfo) {
                    if (in_array($field, ['__exp'])) {
                        continue;
                    }
                    if (!isset($searchInfo['exp'])) {
                        $searchInfo['exp'] = 'and';
                    }
                    $searchInfo['exp'] = strtolower($searchInfo['exp']);

                    if ($whereExpFirst) {
                        $where = 'where';
                        $whereExpFirst = false;
                    } else {
                        $where = $whereExp;
                    }

                    $queryBase = $queryBase->$where(function ($query) use (&$field, &$searchInfo) {
                        $first = true;
                        foreach ($searchInfo as $k => $v) {
                            switch ($k) {
                                case 'likes':
                                    $query->where(function ($q) use ($v, $field) {
                                        foreach ($v as $vv) {
                                            $q->where($field, 'like', '%' . $vv . '%');
                                        }
                                    });
                                    break;
                                case 'like':
                                    if ($first || $searchInfo['exp'] == 'and') {
                                        $first = false;
                                        $query->where($field, 'like', '%' . $v . '%');
                                    } else {
                                        $query->orWhere($field, 'like', '%' . $v . '%');
                                    }
                                    break;
                                case 'leftLike':
                                    if ($first || $searchInfo['exp'] == 'and') {
                                        $first = false;
                                        $query->where($field, 'like', $v . '%');
                                    } else {
                                        $query->orWhere($field, 'like', $v . '%');
                                    }
                                    break;
                                case 'rightLike':
                                    if ($first || $searchInfo['exp'] == 'and') {
                                        $first = false;
                                        $query->where($field, 'like', '%' . $v);
                                    } else {
                                        $query->orWhere($field, 'like', '%' . $v);
                                    }
                                    break;
                                case 'equal':
                                    if ($first || $searchInfo['exp'] == 'and') {
                                        $first = false;
                                        $query->where($field, '=', $v);
                                    } else {
                                        $query->orWhere($field, '=', $v);
                                    }
                                    break;
                                case 'eq':
                                case 'min':
                                    if ($first || $searchInfo['exp'] == 'and') {
                                        $first = false;
                                        $query->where($field, '>=', $v);
                                    } else {
                                        $query->orWhere($field, '>=', $v);
                                    }
                                    break;
                                case 'max':
                                    if ($first || $searchInfo['exp'] == 'and') {
                                        $first = false;
                                        $query->where($field, '<=', $v);
                                    } else {
                                        $query->orWhere($field, '<=', $v);
                                    }
                                    break;
                                case 'in':
                                    if ($first || $searchInfo['exp'] == 'and') {
                                        $first = false;
                                        $query->whereIn($field, $v);
                                    } else {
                                        $query->whereIn($field, $v, 'or');
                                    }
                                    break;
                                case 'is':
                                    if (null === $v) {
                                        if ($first || $searchInfo['exp'] == 'and') {
                                            $first = false;
                                            $query->whereNull($field);
                                        } else {
                                            $query->orWhereNull($field);
                                        }
                                    } else {
                                        exit('TODO');
                                    }
                                    break;
                                case 'raw':
                                    if ($first || $searchInfo['exp'] == 'and') {
                                        $first = false;
                                        $query->whereRaw($v);
                                    } else {
                                        $query->orWhereRaw($v);
                                    }
                                    break;
                                case 'exp':
                                    break;
                                default:
                                    BizException::throws('unknown search exp : ' . $k);
                            }
                        }
                    });
                }
            });
        }
    }

    
    public static function queryFilterExecute(&$query, $filters)
    {
        $query = $query->where(function ($q) use (&$filters) {
            foreach ($filters as $filter) {
                switch ($filter['condition']) {
                    case 'is':
                        $q = $q->where([$filter['field'] => $filter['value']]);
                        break;
                    case 'is_not':
                        $q = $q->where($filter['field'], '<>', $filter['value']);
                        break;
                    case 'contains':
                        $q = $q->where($filter['field'], 'like', '%' . $filter['value'] . '%');
                        break;
                    case 'not_contains':
                        $q = $q->where($filter['field'], 'not like', '%' . $filter['value'] . '%');
                        break;
                    case 'range':
                        if (!empty($filter['value'][0])) {
                            $q = $q->where($filter['field'], '>=', $filter['value'][0]);
                        }
                        if (!empty($filter['value'][1])) {
                            $q = $q->where($filter['field'], '<=', $filter['value'][1]);
                        }
                        break;
                    case 'is_empty':
                        $q = $q->where($filter['field'], '=', '');
                        break;
                    case 'is_not_empty':
                        $q = $q->where($filter['field'], '<>', '');
                        break;
                    case 'gt':
                        $q = $q->where($filter['field'], '>', $filter['value']);
                        break;
                    case 'egt':
                        $q = $q->where($filter['field'], '>=', $filter['value']);
                        break;
                    case 'lt':
                        $q = $q->where($filter['field'], '<', $filter['value']);
                        break;
                    case 'elt':
                        $q = $q->where($filter['field'], '<=', $filter['value']);
                        break;
                }
            }
        });
    }

    public static function paginateQuick($model, $page = null, $pageSize = null, $option = [], $pageUrl = '?page={page}')
    {
        if (null === $page) {
            $page = InputPackage::buildFromInput()->getPage();
        }
        if (null === $pageSize) {
            $pageSize = InputPackage::buildFromInput()->getPageSize();
        }
        $paginateData = self::paginate($model, $page, $pageSize, $option);

        return [
            'records' => $paginateData['records'],
            'total' => $paginateData['total'],
            'page' => $page,
            'pageSize' => $pageSize,
            'pageHtml' => PageHtmlUtil::render($paginateData['total'], $pageSize, $page, $pageUrl)
        ];
    }


    public static function paginate($model, $page, $pageSize, $option = [])
    {
        $m = self::model($model);
        if (!empty($option['joins'])) {
            $select = [];
            $select[] = $model . '.*';
            foreach ($option['joins'] as $join) {
                if (!empty($join['table'])) {
                    $m = $m->leftJoin($join['table'][0], $join['table'][1], $join['table'][2], $join['table'][3]);
                }
                if (!empty($join['fields'])) {
                    foreach ($join['fields'] as $fieldAlias => $fieldTable) {
                        array_push($select, "$fieldTable as $fieldAlias");
                    }
                }
            }
            $m = call_user_func_array(array($m, 'select'), $select);
        }

        self::paginateMergeConditionParam($m, $option);

        if (!empty($option['order'])) {
            if (is_array($option['order'][0])) {
                foreach ($option['order'] as &$order) {
                    $m = $m->orderBy($order[0], $order[1]);
                }
            } else {
                $m = $m->orderBy($option['order'][0], $option['order'][1]);
            }
        }

        if (!empty($option['fields'])) {
            $m = $m->select($option['fields']);
        }

        $m = $m->paginate($pageSize, ['*'], 'page', $page)->toArray();

        return [
            'total' => $m['total'],
            'records' => $m['data']
        ];
    }


    public static function transactionBegin()
    {
        DB::beginTransaction();
    }

    public static function transactionRollback()
    {
        DB::rollback();
    }

    public static function transactionCommit()
    {
        DB::commit();
    }

    public static function isFieldUniqueForInsertOrUpdate($model, $id, $field, $value, $where = [])
    {
        $exists = self::all($model, array_merge([$field => $value], $where));
        if (empty($exists)) {
            return true;
        }
        if (count($exists) == 1 && $id > 0 && $id == $exists[0]['id']) {
            return true;
        }
        return false;
    }

    public static function replaceConditionParamField(&$option, $fieldMap = [])
    {
        if (empty($fieldMap)) {
            return;
        }
        if (!empty($option['search']) && is_array($option['search'])) {
            foreach ($option['search'] as &$searchItem) {
                foreach ($searchItem as $field => $searchInfo) {
                    if (array_key_exists($field, $fieldMap)) {
                        unset($searchItem[$field]);
                        $searchItem[$fieldMap[$field]] = $searchInfo;
                    }
                }
            }
        }

        if (!empty($option['whereIn'])) {
            if (is_array($option['whereIn'][0])) {
                foreach ($option['whereIn'] as &$whereIn) {
                    if (array_key_exists($whereIn[0], $fieldMap)) {
                        $whereIn[0] = $fieldMap[$whereIn[0]];
                    }
                }
            } else {
                if (array_key_exists($option['whereIn'][0], $fieldMap)) {
                    $option['whereIn'][0] = $fieldMap[$option['whereIn'][0]];
                }
            }
        }

        if (!empty($option['whereOperate'])) {
            if (is_array($option['whereOperate'][0])) {
                foreach ($option['whereOperate'] as &$whereOperate) {
                    if (array_key_exists($whereOperate[0], $fieldMap)) {
                        $whereOperate[0] = $fieldMap[$whereOperate[0]];
                    }
                }
            } else {
                if (array_key_exists($option['whereOperate'][0], $fieldMap)) {
                    $option['whereOperate'][0] = $fieldMap[$option['whereOperate'][0]];
                }
            }
        }

        if (!empty($option['where'])) {
            foreach ($option['where'] as $k => $item) {
                if (array_key_exists($k, $fieldMap)) {
                    unset($option['where'][$k]);
                    $option['where'][$fieldMap[$k]] = $item;
                }
            }
        }
    }

    public static function decodeRecordBoolean(&$record, $keyArray)
    {
        if (empty($record)) {
            return;
        }
        if (is_string($keyArray)) {
            $keyArray = [$keyArray];
        }
        foreach ($keyArray as $key) {
            $record[$key] = $record[$key] ? true : false;
        }
    }

    public static function decodeRecordsBoolean(&$records, $keyArray)
    {
        if (empty($records)) {
            return;
        }
        if (is_string($keyArray)) {
            $keyArray = [$keyArray];
        }
        foreach ($records as &$record) {
            foreach ($keyArray as $key) {
                $record[$key] = $record[$key] ? true : false;
            }
        }
    }

    public static function decodeRecordsNumberArray(&$records, $keyArray)
    {
        if (empty($records)) {
            return;
        }
        if (is_string($keyArray)) {
            $keyArray = [$keyArray];
        }
        foreach ($records as &$record) {
            foreach ($keyArray as $key) {
                $values = @json_decode($record[$key], true);
                if (!empty($values)) {
                    $record[$key] = array_map(function ($o) {
                        return intval($o);
                    }, $values);
                } else {
                    $record[$key] = [];
                }
            }
        }
    }

    public static function decodeRecordJson(&$record, $keyArray, $default = [])
    {
        if (empty($record)) {
            return;
        }
        if (is_string($keyArray)) {
            $keyArray = [$keyArray];
        }
        foreach ($keyArray as $key) {
            $record[$key] = @json_decode($record[$key], true);
            if (empty($record[$key])) {
                $record[$key] = $default;
            }
        }
    }

    public static function decodeRecordsJson(&$records, $keyArray, $default = [])
    {
        if (empty($records)) {
            return;
        }
        if (is_string($keyArray)) {
            $keyArray = [$keyArray];
        }
        foreach ($records as &$record) {
            foreach ($keyArray as $key) {
                $record[$key] = @json_decode($record[$key], true);
                if (empty($record[$key])) {
                    $record[$key] = $default;
                }
            }
        }
    }

    public static function encodeRecordJson(&$record, $keyArray, $default = [])
    {
        if (empty($record)) {
            return;
        }
        if (is_string($keyArray)) {
            $keyArray = [$keyArray];
        }
        foreach ($keyArray as $key) {
            if (empty($record[$key])) {
                $record[$key] = $default;
            }
            $record[$key] = @SerializeUtil::jsonEncode($record[$key]);
        }
    }

    public static function encodeRecordsJson(&$records, $keyArray, $default = [])
    {
        if (empty($records)) {
            return;
        }
        if (is_string($keyArray)) {
            $keyArray = [$keyArray];
        }
        foreach ($records as &$record) {
            foreach ($keyArray as $key) {
                if (empty($record[$key])) {
                    $record[$key] = $default;
                }
                $record[$key] = @SerializeUtil::jsonEncode($record[$key]);
            }
        }
    }





    
    public static function increase($model, $where, $field, $value = 1)
    {
        return ModelUtil::update($model, $where, [
            $field => DB::raw('IFNULL(' . $field . ',0)' . ($value > 0 ? '+' . $value : '-' . abs($value)))
        ]);
    }

    public static function change($model, $where, $field, $value)
    {
        if (is_numeric($where) || is_string($where)) {
            $where = ['id' => $where];
        }
        if ($value > 0) {
            self::model($model)->where($where)->increment($field, $value);
        } else {
            self::model($model)->where($where)->decrement($field, -$value);
        }
    }

    public static function sum($model, $field, $where = [])
    {
        return self::model($model)->where($where)->sum($field);
    }

    public static function max($model, $field, $where = [])
    {
        return self::model($model)->where($where)->max($field);
    }


    public static function traverse($model, $key, $default = null)
    {
        if (is_array($model)) {
            return array_get($model, $key, $default);
        }

        if (is_null($key)) {
            return $model;
        }

        if (($model instanceof \stdClass) && isset($model->{$key})) {
            return $model->{$key};
        } else if (isset($model[$key])) {
            return $model[$key];
        }

        foreach (explode('.', $key) as $segment) {
            try {
                $model = $model->$segment;
            } catch (\Exception $e) {
                return value($default);
            }
        }

        return $model;
    }

    
    public static function queryRemoveCondition($query, $type, $column)
    {
        $type = strtolower($type);
        BizException::throwsIf('Unsupported type ' . $type, !in_array($type, ['basic', 'in']));
        $wheres = $query->getQuery()->wheres;
        $bindings = $query->getQuery()->getRawBindings();
        $bindingsWhere = $bindings['where'];
        $bindingIndex = 0;
        $newWheres = $wheres;
        $newBindingsWhere = $bindingsWhere;
                        foreach ($wheres as $i => $v) {
            $bindingsCount = self::getBindingsCount([$v]);
            if (strtolower($v['type']) == $type && $v['column'] == $column) {
                array_splice($newWheres, $i, 1);
                array_splice($newBindingsWhere, $bindingIndex, $bindingsCount);
                break;
            }
            $bindingIndex += $bindingsCount;
        }
                        $query->getQuery()->wheres = $newWheres;
        $query->getQuery()->setBindings($newBindingsWhere);
                return $query;
    }

    private static function getBindingsCount($wheres)
    {
        $count = 0;
        foreach ($wheres as $v) {
            switch (strtolower($v['type'])) {
                case 'in':
                    $count += count($v['values']);
                    break;
                case 'raw':
                    break;
                case 'nested':
                    $count += self::getBindingsCount($v['query']->wheres);
                    break;
                default:
                    $count++;
                    break;
            }
        }
        return $count;
    }

    public static function quoteLikeKeywords($keywords)
    {
        return str_replace(['\\', '%', '_'], ['\\\\\\', '\\%', '\\_'], $keywords);
    }

}
