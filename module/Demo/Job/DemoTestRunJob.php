<?php


namespace Module\Demo\Job;

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Job\BaseJob;
use ModStart\Core\Util\SerializeUtil;
use Module\Demo\Model\DemoTestJob;
use Module\Vendor\QuickRun\JobManager\JobManager;
use Module\Vendor\Type\JobStatus;

class DemoTestRunJob extends BaseJob
{
    public $id;

    public static function create($id, $delay = 0)
    {
        $job = new static();
        $job->id = $id;
        JobManager::dispatch($job, $delay);
    }

    public static function test($id)
    {
        ModelUtil::update(DemoTestJob::class, $id, [
            'status' => JobStatus::QUEUE,
            'statusRemark' => null,
        ]);
        $job = new static();
        $job->id = $id;
        $job->handle();
    }

    public function handle()
    {
        $manager = JobManager::create(DemoTestJob::class, $this->id, []);
        $record = $manager->getQueued();
        if (empty($record)) {
            return;
        }
        ModelUtil::decodeRecordJson($record, ['data', 'result']);
        try {
                        
                        $update = $record['result'];
            $update['result']['bar'] = '2';

            $update['result'] = SerializeUtil::jsonEncode($update['result']);
                        $manager->markSuccess($update);
        } catch (BizException $e) {
            $manager->markFail($e->getMessage());
            return;
        } catch (\Exception $e) {
            $manager->markFail('任务处理异常');
            throw $e;
        }
    }

}
