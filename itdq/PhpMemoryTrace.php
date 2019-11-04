<?php
namespace itdq;

class PhpMemoryTrace
{
    static function reportPeek($file,$line, $errorLog=true, $console=false){
        $_SESSION['peekUsage'] = isset($_SESSION['peekUsage']) ? $_SESSION['peekUsage'] : 0;  // initialise peekUsage

        $peek = memory_get_usage(true);
        if($peek > $_SESSION['peekUsage']){
            if($errorLog) {
                error_log("File:" .  $file . " Line:" .  $line .  " Memory peek:" . memory_get_peak_usage(true),0);
            }
            if($console){
                echo "<br/>File:" . __FILE__ . " Line:" . __LINE__;
                echo "\nMemory limit:" . ini_get('memory_limit');
                echo "\nMemory usage:" . memory_get_peak_usage(true);
                echo "\nMemory peek:" . memory_get_usage(true);

            }
            $_SESSION['peekUsage'] = $peek;
        }
    }
}

