<?php

namespace Module\Demo\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Core\Util\SerializeUtil;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use ModStart\Widget\ButtonAjaxRequest;
use Module\Demo\Admin\Traits\DemoPreviewTrait;
use Module\Demo\Job\DemoTestRunJob;
use Module\Demo\Model\DemoTestJob;
use Module\Vendor\Type\JobStatus;

class AppTestJobController extends Controller
{
    use DemoPreviewTrait;
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $this->setupDemoPreview('任务调度数据展示示例');
        $builder
            ->init(DemoTestJob::class)
            ->field(function ($builder) {
                
                $builder->id('id', 'ID');
                $builder->type('status', '状态')->type(JobStatus::class);
                $builder->display('statusRemark', '说明');
                $builder->datetime('startTime', '开始时间');
                $builder->datetime('endTime', '结束时间');
                $builder->json('data', '参数');
                $builder->json('result', '结果');
                $builder->display('created_at', L('Created At'))->listable(false);
                $builder->display('updated_at', L('Updated At'))->listable(false);
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('id', L('ID'));
            })
            ->gridOperateAppend(
                ButtonAjaxRequest::make('primary', '测试增加任务调度', modstart_admin_url('demo/app_test_job/create'))
            )
            ->disableCUD()
            ->canShow(true)
            ->title('任务调度数据');
    }

    public function create()
    {
        AdminPermission::demoCheck();
        $record = ModelUtil::insert(DemoTestJob::class, [
            'status' => JobStatus::QUEUE,
            'data' => SerializeUtil::jsonEncode([
                'foo' => '1'
            ])
        ]);
        DemoTestRunJob::create($record['id']);
        return Response::redirect(CRUDUtil::jsGridRefresh());
    }
}
