<?php

class JalaliDate
{
    public static function gregorianToJalali($g_y, $g_m, $g_d)
    {
        $g_days_in_month = [31,28,31,30,31,30,31,31,30,31,30,31];
        $j_days_in_month = [31,31,31,31,31,31,30,30,30,30,30,29];
    
        $gy = (int)$g_y - 1600;
        $gm = (int)$g_m - 1;
        $gd = (int)$g_d - 1;
    
        $g_day_no = 365*$gy + (int)(($gy+3)/4) - (int)(($gy+99)/100) + (int)(($gy+399)/400);
    
        for ($i=0; $i < $gm; ++$i)
            $g_day_no += $g_days_in_month[$i];
        if ($gm>1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0)))
            $g_day_no++;
        $g_day_no += $gd;
    
        $j_day_no = $g_day_no - 79;
    
        $j_np = (int)($j_day_no / 12053);
        $j_day_no %= 12053;
    
        $jy = 979 + 33*$j_np + 4*(int)($j_day_no/1461);
        $j_day_no %= 1461;
    
        if ($j_day_no >= 366) {
            $jy += (int)(($j_day_no-1)/365);
            $j_day_no = ($j_day_no-1)%365;
        }
    
        for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; $i++)
            $j_day_no -= $j_days_in_month[$i];
        $jm = $i+1;
        $jd = $j_day_no+1;
    
        return [$jy, $jm, $jd];
    }

    public static function formatJalali($gregorianDate, $separator = '/')
    {
        $parts = explode('-', $gregorianDate);
        if (count($parts) !== 3) return $gregorianDate;

        list($gy, $gm, $gd) = $parts;
        list($jy, $jm, $jd) = self::gregorianToJalali($gy, $gm, $gd);

        return sprintf('%04d%s%02d%s%02d', $jy, $separator, $jm, $separator, $jd);
    }
    
    public static function convertToISODate($date)
    {
        $parts = explode('/', $date);
        if (count($parts) !== 3) return $date;
    
        list($m, $d, $y) = $parts;
        return sprintf('%04d-%02d-%02d', $y, $m, $d);
    }

}
