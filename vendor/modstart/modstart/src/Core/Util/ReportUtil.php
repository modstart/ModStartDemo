<?php

namespace ModStart\Core\Util;

use Illuminate\Support\Facades\DB;
use ModStart\Core\Dao\ModelUtil;

class ReportUtil
{
    
    public static function tableCountSeriesDaily($start, $end, $series = [])
    {
        $startTime = $start . ' 00:00:00';
        $endTime = $end . ' 23:59:59';
        $startTs = strtotime($startTime);
        $endTs = strtotime($endTime);
        $timeDataMap = [];
        for ($t = $startTs; $t < $endTs; $t += TimeUtil::PERIOD_DAY) {
            $item = [];
            foreach ($series as $seriesItem) {
                $item[] = 0;
            }
            $timeDataMap[date('Y-m-d', $t)] = $item;
        }
        foreach ($series as $index => $seriesItem) {
            if (empty($seriesItem['where'])) {
                $seriesItem['where'] = [];
            }
            $data = ModelUtil::model($seriesItem['table'])
                ->where('created_at', '>=', $startTime)
                ->where('created_at', '<', $endTime)
                ->where($seriesItem['where'])
                ->groupBy('_time')
                ->get([
                    DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') _time"),
                    DB::raw('COUNT(1) AS _cnt'),
                ])->toArray();
            foreach ($data as $item) {
                $timeDataMap[$item['_time']][$index] = $item['_cnt'];
            }
        }
        $seriesValue = [];
        $timeDataValues = array_values($timeDataMap);
        foreach ($series as $index => $seriesItem) {
            foreach ($timeDataValues as $timeDataValue) {
                $seriesValue[$index][] = $timeDataValue[$index];
            }
        }
        return [
            'time' => array_keys($timeDataMap),
            'values' => $seriesValue,
        ];
    }

    
    public static function tableSumSeriesDaily($start, $end, $series = [])
    {
        $startTime = $start . ' 00:00:00';
        $endTime = $end . ' 23:59:59';
        $startTs = strtotime($startTime);
        $endTs = strtotime($endTime);
        $timeDataMap = [];
        for ($t = $startTs; $t < $endTs; $t += TimeUtil::PERIOD_DAY) {
            $item = [];
            foreach ($series as $seriesItem) {
                $item[] = 0;
            }
            $timeDataMap[date('Y-m-d', $t)] = $item;
        }
        foreach ($series as $index => $seriesItem) {
            if (empty($seriesItem['where'])) {
                $seriesItem['where'] = [];
            }
            $data = ModelUtil::model($seriesItem['table'])
                ->where($seriesItem['where'])
                ->where('created_at', '>=', $startTime)
                ->where('created_at', '<', $endTime)
                ->groupBy('_time')
                ->get([
                    DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') _time"),
                    DB::raw('SUM(' . $seriesItem['field'] . ') AS _total'),
                ])->toArray();
            foreach ($data as $item) {
                $timeDataMap[$item['_time']][$index] = $item['_total'];
            }
        }
        $seriesValue = [];
        $timeDataValues = array_values($timeDataMap);
        foreach ($series as $index => $seriesItem) {
            foreach ($timeDataValues as $timeDataValue) {
                $seriesValue[$index][] = $timeDataValue[$index];
            }
        }
        return [
            'time' => array_keys($timeDataMap),
            'values' => $seriesValue,
        ];
    }
}
