<div class="pb-widget-captcha tw-flex">
    <div class="image tw-w-32 tw-mr-2">
        <img src="{{modstart_web_url('captcha/image')}}?{{time()}}"
             data-captcha
             class="tw-rounded tw-cursor-pointer"
             onclick="this.src='{{modstart_web_url('captcha/image')}}?'+Math.random();"
             style="height:2rem;width:100%;" />
    </div>
    <div class="input tw-flex-grow">
        <input type="text" style="width:100%;" class="form-lg" name="captcha" placeholder="图片验证" />
    </div>
</div>
