@extends('theme.default.pc.frame')

@section('pageTitle')ModStart框架演示@endsection
@section('pageKeywords')ModStart框架演示@endsection
@section('pageDescription')ModStart框架演示@endsection

@section('headAppend')
    @parent
    <style>
        .demo-container-bg{
            position:absolute;
            left:7rem;top:7rem;bottom:7rem;right:7rem;
            background-image:linear-gradient( -45deg, #bd34fe 50%, #47caff 50% );
            filter: blur(10rem);
        }
        .demo-container-text{
            background:linear-gradient( 120deg, #bd34fe 30%, #41d1ff );
            background-clip: text;
            color: transparent;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
@endsection

@section('bodyContent')

    <div class="ub-container">
        <div class="tw-relative margin-top margin-bottom tw-bg-white tw-rounded-lg tw-flex tw-items-center"
             style="min-height:calc(100vh - 350px);"
        >
            <div class="demo-container-bg"></div>
            <div class="tw-relative tw-text-center tw-w-full">
                <div class="tw-pt-32">
                    <span class="demo-container-text tw-text-5xl tw-font-bold">
                        ModStart 开发框架演示
                    </span>
                </div>
                <div class="tw-pt-10 tw-text-2xl">
                    基于 Laravel 的模块化全栈开发框架
                </div>
                <div class="tw-pt-10">
                    <a href="{{modstart_admin_url('login',['username'=>'demo','password'=>'123456'])}}" class="btn btn-lg btn-round btn-primary-line">
                        后台登录
                    </a>
                    <a href="https://modstart.com" target="_blank" class="btn btn-lg btn-round btn-primary-line">
                        官方网站
                    </a>
                    <a href="https://modstart.com/store" target="_blank" class="btn btn-lg btn-round btn-primary-line">
                        模块市场
                    </a>
                    <a href="https://modstart.com/doc" target="_blank" class="btn btn-lg btn-round btn-primary-line">
                        开发文档
                    </a>
                </div>
                <div class="tw-pt-10 tw-pb-32">
                    后台管理系统演示 账号：demo 密码：123456
                </div>
            </div>
        </div>
    </div>

@endsection
