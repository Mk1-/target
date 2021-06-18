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
        return self::convert_value($value, $EXCH[$srcCurr][$date], $EXCH[$dstCurr][$date]);
    }

    public static function convert_value($value, $srcRate, $dstRate) {
        return $value * $srcRate / $dstRate;
    }

}