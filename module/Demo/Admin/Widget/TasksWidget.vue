<!-- @DEMO 加载ECharts，处理任务统计请求，权限控制 -->
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
                    left: 'left',
                    top: 'middle'
                },
                series: [
                    {
                        name: '访问占比',
                        type: 'pie',
                        radius: ['40%', '70%'],
                        center: ['75%', '50%'],
                        avoidLabelOverlap: false,
                        data: this.records,
                        itemStyle: {
                            borderRadius: 10,
                            borderColor: '#fff',
                            borderWidth: 2
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
            <div class="tw-flex-grow tw-font-bold">任务状态</div>
            <div>
                <el-select v-model="time" placeholder="请选择">
                    <el-option label="最近3天" value="3"></el-option>
                    <el-option label="最近一周" value="7"></el-option>
                    <el-option label="最近一月" value="30"></el-option>
                </el-select>
            </div>
        </div>
        <div v-loading="loading">
            <div ref="chart" style="height:180px;"></div>
        </div>
    </div>
</template>
