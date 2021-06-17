<?php
namespace App\App;

class CurrencyConverter 
{

    public static function convert($date, $srcCurr, $dstCurr, $value, $EXCH)
    {
        if ( (! isset($EXCH[$srcCurr])) || (! isset($EXCH[$srcCurr][$date])) ) {
            throw new \Exception(sprintf("No exchange rate for currency %s and date %s.", $srcCurr, $date), 1);
        }
        if ( (! isset($EXCH[$dstCurr])) || (! isset($EXCH[$dstCurr][$date])) ) {
            throw new \Exception(sprintf("No exchange rate for currency %s and date %s.", $dstCurr, $date), 1);
        }
        return $value * $EXCH[$srcCurr][$date] / $EXCH[$dstCurr][$date];
    }

}