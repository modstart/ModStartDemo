<?php

namespace Module\Vendor\QuickRun\JobManager;

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Job\BaseJob;
use ModStart\Core\Util\LogUtil;
use ModStart\Core\Util\StrUtil;
use Module\Vendor\Type\JobStatus;

class JobManager
{
    protected $param = [];
    protected $model;
    protected $id;
    protected $modelName;

    public static function dispatch(BaseJob $job, $queue = 'default')
    {
        $job->onQueue($queue);
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }

    public static function create($model, $id, $param)
    {
        $ins = new static();
        $ins->model = $model;
        $ins->id = $id;
        $ins->modelName = class_basename($model);
        $ins->param = array_merge([
            'statusField' => 'status',
            'statusRemarkField' => 'statusRemark',
            'statusRemarkFieldLength' => 400,
        ], $param);
        return $ins;
    }

    public function getQueued()
    {
        ModelUtil::transactionBegin();
        $record = ModelUtil::getWithLock($this->model, $this->id);
        LogUtil::info('JobManager.getQueuedJob.' . $this->modelName, [
            'id' => $this->id,
        ]);
        if (empty($record)) {
            ModelUtil::transactionCommit();
            LogUtil::info('JobManager.getQueuedJob.' . $this->modelName . '.notFound', [
                'id' => $this->id,
            ]);
            return null;
        }
        if ($record[$this->param['statusField']] != JobStatus::QUEUE) {
            ModelUtil::transactionCommit();
            LogUtil::info('JobManager.getQueuedJob.' . $this->modelName . '.notQueued', [
                'id' => $this->id,
                'status' => $record[$this->param['statusField']],
            ]);
            return null;
        }
        ModelUtil::update($this->model, $this->id, [
            $this->param['statusField'] => JobStatus::PROCESS,
        ]);
        ModelUtil::transactionCommit();
        unset($record[$this->param['statusField']]);
        unset($record[$this->param['statusRemarkField']]);
        return $record;
    }


    public function markSuccess($update = [])
    {
        $update[$this->param['statusField']] = JobStatus::SUCCESS;
        $update[$this->param['statusRemarkField']] = '';
        LogUtil::info('JobManager.markSuccess.' . $this->modelName, [
            'id' => $this->id,
            'update' => $update,
        ]);
        ModelUtil::update($this->model, $this->id, $update);
    }

    public function markFail($remark, $update = [])
    {
        $update[$this->param['statusField']] = JobStatus::FAIL;
        $update[$this->param['statusRemarkField']] = StrUtil::mbLimit($remark, $this->param['statusRemarkFieldLength']);
        LogUtil::info('JobManager.markFail.' . $this->modelName, [
            'id' => $this->id,
            'remark' => $remark,
            'update' => $update,
        ]);
        ModelUtil::update($this->model, $this->id, $update);
    }
}
