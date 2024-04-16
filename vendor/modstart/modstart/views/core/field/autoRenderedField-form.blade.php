<div class="line" data-field="{{$name}}">
    <div class="label">
        {!! (!empty($rules) && in_array('required',$rules))?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        @if(!empty($label))
            {{$label}}
        @endif
    </div>
    <div class="field">
        {!! $value !!}
    </div>
</div>
