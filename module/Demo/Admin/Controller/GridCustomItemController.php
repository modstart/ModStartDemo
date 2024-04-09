<?php

namespace Module\Demo\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminGrid;
use ModStart\Field\AbstractField;
use ModStart\Grid\Grid;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasPageTitleInfo;
use Module\Demo\Admin\Traits\DemoPreviewTrait;
use Module\Demo\Model\DemoNews;

class GridCustomItemController extends Controller
{
    use DemoPreviewTrait;
    use HasPageTitleInfo;
    use HasAdminGrid;

    public function grid()
    {
        $this->setupDemoPreview('支持页面条目的自定义显示');
        $grid = Grid::make(DemoNews::class);
        $grid->gridRowCols([3, 6]);
        $grid->useSimple(function (AbstractField $field, $item, $index) {
            $html = <<<HTML
<div class="ub-content-box tw-shadowed ub-border hover:tw-shadow-lg">
    <div class="lg:tw-w-4/5 tw-mx-auto margin-bottom">
        <div style="background-image:url({$item->cover});" class="ub-cover-1-1"></div>
    </div>
    <div class="ub-text-truncate">{$item->title}</div>
</div>
HTML;

            return $html;
        });

        $grid->gridFilter(function (GridFilter $filter) {
            $filter->eq('id', 'ID');
            $filter->like('title', '标题');
        });
        $grid->canAdd(false)->canEdit(false)->canDelete(false)->canShow(false);
        $grid->title('自定义试图');
        return $grid;
    }
}
