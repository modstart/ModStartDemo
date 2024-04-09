<style type="text/css">
    .pb-icon-list {
        overflow: hidden;
    }

    .pb-icon-list .item {
        float: left;
        width: 16.66%;
        text-align: center;
        color: #666;
        font-size: 13px;
        border-right: 1px solid #eee;
        border-bottom: 1px solid #eee;
        margin-right: -1px;
        margin-bottom: -1px;
        padding: 1rem 0;
    }

    .pb-icon-list .item .icon {
        display: block;
        transition: color .15s linear;
        height: 4rem;
        line-height: 4rem;
        cursor: pointer;
    }

    .pb-icon-list .item .icon i {
        display: block;
        transition: color .15s linear;
        line-height: 4rem;
        font-size: 2rem;
    }

    .pb-icon-list .item .title {
        display: block;
        font-size: 12px;
        white-space: nowrap;
        text-overflow: ellipsis;
        color: #999;
        cursor: pointer;
    }
</style>
@foreach($records as $r)
    <div class="ub-panel">
        <div class="head">
            <div class="title">
                {{$r['title']}}
            </div>
        </div>
        <div class="body">
            <div class="pb-icon-list">
                @foreach($r['icons'] as $icon)
                    <div class="item">
                        <div class="icon" data-clipboard-text="{{$icon['cls']}}">
                            <i class="{{$icon['cls']}}"></i>
                        </div>
                        <div class="title" data-clipboard-text="{{$icon['cls']}}">{{$icon['cls']}}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endforeach
