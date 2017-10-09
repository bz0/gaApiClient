<?php
namespace src\models;

class dateCalc {
    /*
     * 直近〇週間前の月曜・日曜の日付を取得する
     */
    public function week($now, $weekCount){
        $date  = \Cake\Chronos\Chronos::parse($now);
        $week  = $date->subWeek($weekCount);

        $monday = $week->startOfWeek();
        $sunday = $week->endOfWeek();
        
        $res = array(
            'start' => $monday->toDateString()
          , 'end'   => $sunday->toDateString()      
        );
        
        return $res;
    }
    
    public function date($now, $dayCount){
        $date  = \Cake\Chronos\Chronos::parse($now);
        $day   = $date->subDay($dayCount);
        $res   = array(
            'start' => $day->toDateString()
          , 'end'   => $day->toDateString()
        );
        
        return $res;
    }
    
    /*
     * 直近〇カ月前の月初・月末日付の取得
     */
    public function month($now, $monthCount){
        $date  = \Cake\Chronos\Chronos::parse($now);
        $date  = $date->subMonth($monthCount);
        
        $firstOfMonth = $date->firstOfMonth();
        $lastOfMonth  = $date->lastOfMonth();
        
        $res = array(
             'start' => $firstOfMonth->toDateString()
           , 'end'   => $lastOfMonth->toDateString()
        );
        
        return $res;
    }
}
