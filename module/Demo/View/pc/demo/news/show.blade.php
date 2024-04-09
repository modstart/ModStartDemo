@extends($_viewFrame)

@section('pageTitleMain'){{$record['title']}}@endsection

@section('bodyContent')
    <div class="ub-container margin-top">
        <div class="ub-panel">
            <div class="head">
                <div class="title">新闻数据</div>
            </div>
            <div class="body">
                <pre>{{\ModStart\Core\Util\SerializeUtil::jsonEncode($record,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)}}</pre>
                <div class="ub-html lg">{!! $record['content'] !!}</div>
            </div>
        </div>
    </div>
@endsection
