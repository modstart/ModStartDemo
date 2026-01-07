@extends($_viewFrame)

@section('pageTitleMain')Demo演示@endsection

@section('bodyContent')

    <div class="ub-container margin-top">
        <div class="row">
            <div class="col-md-6">
                <div class="ub-panel">
                    <div class="head">
                        <div class="title">新闻分类</div>
                    </div>
                    <div class="body">
                        <pre>{{\ModStart\Core\Util\SerializeUtil::jsonEncode(\Module\Demo\Util\DemoNewsUtil::categories(),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)}}</pre>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="ub-panel">
                    <div class="head">
                        <div class="title">新闻内容</div>
                    </div>
                    <div class="body">
                        <pre>{{\ModStart\Core\Util\SerializeUtil::jsonEncode(\Module\Demo\Util\DemoNewsUtil::all(),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)}}</pre>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="ub-panel">
                    <div class="head">
                        <div class="title">简单配置</div>
                    </div>
                    <div class="body">
                        <pre>{{modstart_config('Demo_Title','Demo_Title默认值')}}</pre>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="ub-panel">
                    <div class="head">
                        <div class="title">多语言</div>
                    </div>
                    <div class="body">
                        <pre>模块：{{LM('Demo','TestLanguage')}}</pre>
                        <pre>系统：{{L('Please Select')}}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
