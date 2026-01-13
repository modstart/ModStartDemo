{{--@DEMO 数据导入功能--}}
@extends('modstart::admin.frame')

@section($_tabSectionName)

    <div class="ub-panel">
        <div class="head">
            <div class="title">{{$pageTitle}}</div>
        </div>
        <div class="body">
            <div id="app" v-cloak>
                <div class="ub-form">
                    <div class="line">
                        <div class="label">
                            <span>*</span>
                            字段1
                        </div>
                        <div class="field">
                            <el-input v-model="dataField1" />
                            <div class="help">
                                字段说明1
                            </div>
                        </div>
                    </div>
                    <div class="line">
                        <div class="label">
                            上传文件导入
                        </div>
                        <div class="field">
                            <div>
                                <el-upload action="" ref="upload" :auto-upload="false" :file-list="[]" drag
                                           :on-change="doFileSelect">
                                    <i class="el-icon-upload"></i>
                                    <div class="el-upload__text">将文件拖到此处，或<em>点击上传XLSX文件</em></div>
                                </el-upload>
                            </div>
                            <div class="tw-pb-4 tw-font-mono tw-text-sm">
                                <div class="tw-mb-3">
                                    <a href="javascript:;"
                                       class="btn btn-round"
                                       @click="doDownloadTemplate()"><i
                                            class="iconfont icon-download"></i> 点击这里下载模板</a>
                                </div>
                                <table class="ub-table border">
                                    <tbody>
                                    <tr>
                                        <td class="tw-truncate" width="100">导入列要求</td>
                                        <td>
                                            <div v-for="(h,hIndex) in importHeader" :key="hIndex"
                                                 class="tw-pr-3 tw-bg-gray-100 tw-rounded-2xl tw-inline-block tw-mr-1 tw-mb-1 tw-py-0"
                                                 style="min-width:7rem;"
                                            >
                                                <div
                                                    class="tw-bg-gray-200 tw-rounded-2xl tw-py-1 tw-px-2 tw-inline-block tw-text-center"
                                                    style="min-width:1.5rem;">
                                                    @{{ hIndex + 1 }}
                                                </div>
                                                @{{ h }}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            导入说明
                                        </td>
                                        <td class="ub-html">
                                            <p>导入说明。</p>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="line">
                        <div class="label">
                            上传结果
                        </div>
                        <div class="field">
                            <table class="ub-table border">
                                <thead>
                                <tr>
                                    <th width="80">序号</th>
                                    <th width="80">结果</th>
                                    <th>内容</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-if="uploadResult.length<=0">
                                    <td colspan="3">
                                        <div class="ub-text-muted">暂无上传数据</div>
                                    </td>
                                </tr>
                                <tr v-for="(r,rIndex) in uploadResult">
                                    <td>
                                        @{{ r.index }}
                                    </td>
                                    <td>
                                        <span v-if="r.status==='duplicate'" class="ub-text-warning">重复</span>
                                        <span v-else-if="r.status==='success'" class="ub-text-success">成功</span>
                                        <span v-else-if="r.status==='fail'" class="ub-text-danger">失败</span>
                                    </td>
                                    <td>
                                        @{{ JSON.stringify(r.data) }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-fixed-bottom-toolbox-placeholder"></div>

@endsection

@section('bodyAppend')
    <script src="@asset('asset/common/editor.js')"></script>
    <script src="@asset('asset/common/file.js')"></script>
    <script src="@asset('asset/vendor/vue.js')"></script>
    <script src="@asset('asset/vendor/element-ui/index.js')"></script>
    <script src="@asset('vendor/Vendor/entry/all.js')"></script>
    <script>
        $(function(){
            new Vue({
                el:'#app',
                data(){
                    return {
                        importHeader: [
                            // 0
                            '序号',
                            // 1
                            '字段2',
                            // 2
                            '字段3',
                        ],
                        file: null,
                        dataField1:'',
                        headerIndices: {
                            field2: 1,
                            field3: 1,
                        },
                        importOptions: {
                            overwrite: false,
                        },
                        uploadResult: [],
                    }
                },
                methods:{
                    doFileSelect(file) {
                        this.file = file.raw
                        let importSuccess = 0, importDuplicated = 0, importFail = 0
                        const loadingIndex = this.$dialog.loadingOn('正在导入')
                        const loading = (text) => {
                            $('#layui-layer' + loadingIndex + ' .loading-text').html(text)
                            $(window).resize()
                        }
                        const error = (text) => {
                            this.$refs.upload.clearFiles()
                            this.$dialog.alertError(text)
                            this.$dialog.loadingOff()
                        }
                        const success = (text) => {
                            this.$refs.upload.clearFiles()
                            this.$dialog.alertSuccess(text)
                            this.$dialog.loadingOff()
                        }
                        const upload = (data, format) => {
                            if (!this.dataField1) {
                                error('请先填写字段1');
                                return
                            }
                            if (data.length < 1) {
                                error('数据为空')
                                return
                            }
                            if (JSON.stringify(this.importHeader) !== JSON.stringify(data[0])) {
                                console.log('data', data)
                                error('文件格式不正确')
                                return
                            }
                            data.shift()
                            data = data
                                .filter(o => {
                                    return o[this.headerIndices.field2]
                                        && o[this.headerIndices.field3]
                                })
                            this.uploadResult = []
                            let processed = 0
                            let total = data.length
                            new MS.file.listDispatcher()
                                .set(data)
                                .chunk(100)
                                .error((msg, me) => {
                                    error('上传数据错误：' + msg)
                                })
                                .interval(0)
                                .dispatch((list, cb, me) => {
                                    (async ()=>{
                                        processed += list.length
                                        loading(`数据导入中（进度${processed}/${total}，成功${importSuccess}条，失败${importFail}条，重复${importDuplicated}条）`)
                                        const dataList = [];
                                        const itemList = [];
                                        for(const one of list){
                                            const dataItem = {
                                                field1: this.dataField1,
                                                field2: one[this.headerIndices.field2],
                                                field3: one[this.headerIndices.field3],
                                            }
                                            dataList.push(dataItem);
                                            itemList.push({
                                                index: one[0],
                                                status:'pending',
                                                data: dataItem,
                                            })
                                        }
                                        window.api.base.post(this.$url.admin('demo/app_import'),
                                            {importData:JSON.stringify(dataList)},
                                            res => {
                                                if(res.code){
                                                    for(const item of itemList){
                                                        importFail++
                                                        item.status = 'fail'
                                                    }
                                                }else{
                                                    for(let i =0;i<res.data.results.length;i++){
                                                        const result = res.data.results[i];
                                                        if (result.code) {
                                                            if (result.code === 1) {
                                                                importDuplicated++
                                                                itemList[i].status = 'duplicate'
                                                            } else {
                                                                importFail++
                                                                itemList[i].status = 'fail'
                                                            }
                                                        } else {
                                                            importSuccess++
                                                            itemList[i].status = 'success'
                                                        }
                                                    }
                                                }
                                                for(const item of itemList){
                                                    item.index = this.uploadResult.length + 1;
                                                    this.uploadResult.push(item);
                                                }
                                                cb({code: 0, msg: null})
                                            })
                                    })();
                                })
                                .finish((me) => {
                                    success(`成功上传${importSuccess}条数据，失败${importFail}条数据，重复${importDuplicated}条`)
                                })
                                .start()
                        }
                        new MS.file.excelReader().file(this.file).parse((data) => {
                            upload(data)
                        })
                    },
                    doDownloadTemplate() {
                        new MS.file.excelWriter()
                            .data([
                                this.importHeader,
                                ['字段2', '字段3'],
                            ])
                            .filename('ImportTemplate.xlsx')
                            .download()
                    },
                }
            })
        })
    </script>
@endsection
