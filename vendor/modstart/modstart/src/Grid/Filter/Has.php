<?php


namespace ModStart\Grid\Filter;


use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Type\BaseType;

class Has extends AbstractFilter
{
    protected $query = 'whereIn';

    
    public function condition($searchInfo)
    {
        if (isset($searchInfo['has']) && is_array($searchInfo['has'])) {
            return $this->buildCondition($this->column, $searchInfo['has']);
        }
        return null;
    }

    
    public function checkbox($options)
    {
        $this->field = new Field\Checkbox($this);
        $this->field->options($options);
        return $this;
    }

    
    public function cascader($options)
    {
        $this->field = new Field\Cascader($this);
        $this->field->nodes($options);
        return $this;
    }

    
    public function cascaderModel($table, $idKey = 'id', $pidKey = 'pid', $titleKey = 'title', $sortKey = 'sort')
    {
        $nodes = [];
        foreach (ModelUtil::all($table, [], [$idKey, $pidKey, $titleKey, $sortKey], [$sortKey, 'asc']) as $item) {
            $nodes[] = [
                'id' => $item[$idKey],
                'title' => $item[$titleKey],
                'pid' => $item[$pidKey],
                'sort' => $item[$sortKey],
            ];
        }
        return $this->cascader($nodes);
    }
}
