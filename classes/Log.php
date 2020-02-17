<?php

/**
 * Class Log
 */
class Log
{

    public static function write($message, $printToScreen = true)
    {
        $script_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
        if ($printToScreen && $script_name != "api") {
            echo $message . "<br/>";
        }

        $message = self::convertBrToBreakLine($message);
        $file = "config/log/application.log";
        $time = @date('[d/M/Y:H:i:s]');

        file_put_contents($file, "$time ($script_name) $message\r\n", FILE_APPEND);
        usleep(10);
    }

    public static function convertBrToBreakLine($message)
    {
        return str_replace("<br/>", "\r\n", $message);
    }

}
