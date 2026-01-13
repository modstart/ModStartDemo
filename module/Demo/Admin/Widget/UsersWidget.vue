<!-- @DEMO 加载ECharts，处理用户统计请求，权限控制 -->
<script>
export default {
    data() {
        return {
            loading: false,
            time: '7',
            total: _widgetInitParam.total,
            records: _widgetInitParam.records,
            chart: null,
        }
    },
    watch: {
        time() {
            this.doLoad();
        }
    },
    mounted() {
        this.doRefresh();
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
                xAxis: {
                    type: 'category',
                    data: this.records.map(o => o.time),
                    show: false,
                    axisLabel: {
                        show: false
                    },
                    boundaryGap: false,
                },
                yAxis: {
                    type: 'value',
                    show: false,
                    axisLabel: {
                        show: false
                    }
                },
                grid: {
                    show: false,
                    left: 0,
                    right: 0,
                    top: 0,
                    bottom: 0,
                    containLabel: false
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'none',
                        snap: true
                    },
                    formatter: function (params) {
                        var date = params[0].name;
                        var value = params[0].data;
                        return date + '<br/>' + '' + value;
                    }
                },
                series: [
                    {
                        data: this.records.map(o => o.value),
                        type: 'line',
                        smooth: true,
                        showSymbol: false,
                        areaStyle: {
                            color: 'rgba(30, 144, 255, 0.3)'
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
            <div class="tw-flex-grow tw-font-bold">用户趋势</div>
            <div>
                <el-select v-model="time" placeholder="请选择">
                    <el-option label="最近7天" value="7"></el-option>
                    <el-option label="最近一月" value="30"></el-option>
                    <el-option label="最近一年" value="365"></el-option>
                </el-select>
            </div>
        </div>
        <div class="margin-top">
            <div class="tw-text-xl tw-h-14 tw-leading-14">
                总数 {{ total }}
            </div>
        </div>
        <div v-loading="loading">
            <div ref="chart" style="height:113px;"></div>
        </div>
    </div>
</template>
