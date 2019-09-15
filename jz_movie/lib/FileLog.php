<?php

class FileLog
{
    //是否开启log日志
    public static $is_open = true;

    public static function println(...$variable)
    {
        $log_path = fopen(__DIR__ . '/log.php', 'a+');
        if (!FileLog::$is_open) return;
        ob_start();
        foreach ($variable as $item) {
            switch (gettype($item)) {
                case 'array':
                    var_dump($item);
                    $item = ob_get_clean();
                    break;
            }
            fwrite($log_path, "******************************************\n");
            fwrite($log_path, $item . "\n");
        }
        fclose($log_path);
    }
}



