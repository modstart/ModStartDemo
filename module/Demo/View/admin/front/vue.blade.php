@extends('modstart::admin.frame')

@section('pageTitleMain')
    Vue+ElementUI 集成
@endsection

@section($_tabSectionName)
    <div class="ub-panel">
        <div class="head">
            <div class="title">Vue+ElementUI 集成</div>
        </div>
        <div class="body">
            <div id="app" v-cloak>
                <p class="ub-alert">@{{ message }}</p>
                <el-button @click="onClick" size="mini">ElementUI 按钮</el-button>
            </div>
            <div class="margin-top">
                我们推荐您使用自己熟悉的前端框架，这里我们提供了 Vue.js 和 ElementUI 的集成示例。
            </div>
            <div class="margin-top">
                更多可参考 <a href="https://cn.vuejs.org/v2/guide/" target="_blank">Vue.js 中文文档</a>
                和
                <a href="https://element.eleme.cn/#/zh-CN/component/installation" target="_blank">ElementUI 中文文档</a>
            </div>
        </div>
    </div>
@endsection

@section('bodyAppend')
    @parent
    <script src="@asset('asset/vendor/vue.js')"></script>
    <script src="@asset('asset/vendor/element-ui/index.js')"></script>
    <script>
        $(function () {
            new Vue({
                el: '#app',
                data: function () {
                    return {
                        message: 'Hello Vue!'
                    };
                },
                methods: {
                    onClick: function () {
                        this.message = 'Hello ModStart!';
                    }
                }
            });
        });
    </script>
@endsection
