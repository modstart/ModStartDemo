<div class="line" data-field="{{$name}}">
    <div class="label">
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        @if(is_array($value))
            <div class="">
                <table class="ub-table mini border" style="white-space:normal;word-break:break-all;">
                    @foreach($value as $k=>$v)
                        <tr>
                            <td width="50">{{$k}}</td>
                            <td>{{$v}}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @elseif(!empty($value))
            <pre style="margin:0;line-height:1rem;overflow:auto;">{{\ModStart\Core\Util\SerializeUtil::jsonEncodePretty($value)}}</pre>
        @else
            <span class="ub-text-muted">-</span>
        @endif

    </div>
</div>
