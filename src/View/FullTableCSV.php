<?php
namespace App\View;

class FullTableCSV
{
    private const SEPARATOR = ";";

    public static function create($TABLE)
    {
        $ROW = reset($TABLE);
        $CURR = array_keys($ROW['IN_CURRENCY']);
        unset($ROW['IN_CURRENCY']);
        $HEAD = array_merge(array_keys($ROW), $CURR);
        
        $fp = fopen('php://temp', 'w');

        fputcsv($fp, $HEAD, self::SEPARATOR);
        foreach ($TABLE as $ROW) {
            $INCUR = $ROW['IN_CURRENCY'];
            unset($ROW['IN_CURRENCY']);
            fputcsv($fp, array_merge($ROW, $INCUR), self::SEPARATOR);
        }

        rewind($fp);
        $ret = stream_get_contents($fp);
        fclose($fp);
        
        return $ret;
    }
}