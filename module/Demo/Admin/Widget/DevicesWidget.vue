<script>
export default {
    data() {
        return {
            loading: false,
            time: '3',
            records: _widgetInitParam.records,
            chart: null,
        }
    },
    mounted() {
        this.doRefresh();
    },
    watch: {
        time() {
            this.doLoad();
        }
    },
    methods: {
        doLoad() {
            this.loading = true;
            MS.widget.requestInContainer(this, {
                scope: 'admin',
                data: {
                    time: this.time
                },
                success: (res) => {
                    this.loading = false;
                    this.records = res.data.records;
                    this.time = res.data.time;
                    this.doRefresh();
                }
            })
        },
        doRefresh() {
            if (!this.chart) {
                this.chart = echarts.init(this.$refs.chart);
                MS.ui.onResize(this.$refs.chart, this.chart.resize);
            }
            this.chart.setOption({
                tooltip: {
                    trigger: 'item'
                },
                legend: {
                    orient: 'vertical',
                    left: 'right',
                    top: 'middle'
                },
                series: [
                    {
                        name: '访问占比',
                        type: 'pie',
                        radius: '80%',
                        center: [75, '50%'],
                        data: this.records,
                        emphasis: {
                            itemStyle: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        },
                        right: 0,
                        label: {
                            show: false
                        }
                    }
                ]
            });
        },
    }
}
</script>

<template>
    <div class="ub-content-box margin-bottom">
        <div class="tw-flex">
            <div class="tw-flex-grow tw-font-bold">
                <i class="iconfont icon-desktop"></i>
                访问设备
            </div>
            <div>
                <el-select v-model="time" placeholder="请选择">
                    <el-option label="最近3天" value="3"></el-option>
                    <el-option label="最近一周" value="7"></el-option>
                    <el-option label="最近一月" value="30"></el-option>
                </el-select>
            </div>
        </div>
        <div v-loading="loading">
            <div ref="chart" style="height:150px;"></div>
        </div>
    </div>
</template>
