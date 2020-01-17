<?php
//by Towky 

class freeCalendarBlocks{

    var $freeTime = Array();

    function __construct($cal1,$cal2,$wd1,$wd2,$duration){
        $this->calendar1 = $cal1;
        $this->calendar2 = $cal2;
        $this->workingDay1 = $wd1;
        $this->workingDay2 = $wd2;
        $this->duration = $duration;
        $this->freeTime = array_fill(0, 1441, 'x');
    }

    function calcFreeTime(){
        if ($this->getMinutesFromString($this->workingDay1[0]) > $this->getMinutesFromString($this->workingDay2[0])){
            $startMinutes = $this->getMinutesFromString($this->workingDay1[0]);
        } else {
            $startMinutes = $this->getMinutesFromString($this->workingDay2[0]);
        }
        
        if ($this->getMinutesFromString($this->workingDay1[1]) > $this->getMinutesFromString($this->workingDay2[1])){
            $endMinutes = $this->getMinutesFromString($this->workingDay1[1]);
        } else {
            $endMinutes = $this->getMinutesFromString($this->workingDay2[1]);
        }

        if($startMinutes > 0) $this->removeTime(0,$startMinutes);
        if($endMinutes < 1440) $this->removeTime($endMinutes,1440);

        $this->removeTimeFromArrByCal($this->calendar1);
        $this->removeTimeFromArrByCal($this->calendar2);

        //Calculate free block time here
        $list = Array(); //array list of times
        $start=0;
        $durtemp=0;
        for($i=0;$i<1440;$i++){
            if (isset($this->freeTime[$i])){
                if($start == 0) $start = $i;
                $durtemp++;
            } else {
                if($durtemp >= $this->duration){
                    $list[] = Array($this->getStringFromMinutes($start),$this->getStringFromMinutes($start+$durtemp));
                }
                $start=0;
                $durtemp=0;
            }
        }
        return $list;
    }

    //remove minutes from the day, that are blocked
    function removeTime($start, $end){
        for ($i = $start; $i <= $end; $i++){
            unset ($this->freeTime[$i]);
        }
    }

    //function for walking the calendar
    function removeTimeFromArrByCal($calendar){
        foreach ($calendar as $item){
            $this->removeTime($this->getMinutesFromString($item[0]),$this->getMinutesFromString($item[1]));
        }
    }

    function getMinutesFromString($timestr){
        $exp = explode(":",$timestr);
        return ($exp[0] * 60) + $exp[1];
    }

    function getStringFromMinutes($minutes){
        return str_pad(floor($minutes/60),2,"0",STR_PAD_LEFT) .":". str_pad(($minutes % 60),2,"0",STR_PAD_LEFT);
    }

}

$cal1 = Array( Array("9:30","10:30"), Array("12:00","14:00"), Array("14:12","16:00"));
$wd1 = Array("9:00","20:00");

$cal2 = Array(Array("9:00","10:00"), Array("11:00","11:30"), Array("17:20","18:00"));
$wd2 = Array("8:00","18:00");

$duration = 29;

$freeCalendar = new freeCalendarBlocks($cal1,$cal2,$wd1,$wd2,$duration);
print_r($freeCalendar->calcFreeTime());
