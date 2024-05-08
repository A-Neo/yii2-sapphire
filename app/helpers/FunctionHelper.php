<?php

namespace app\helpers;

use Exception;
use Yii;
use yii\helpers\Json;

class FunctionHelper
{
    public static $f_name = 'file.txt';

    /**
     * @param $arr
     * @return void
     */
    public static function dd($arr, $die = true)
    {
        echo "<pre>";
        print_r($arr);
        if ($die) die();
    }

    public static function wfile($variable) {
        $file = null;
        try {
            // Проверка существования файла
            if(file_exists(self::$f_name)) {
                $file = fopen(self::$f_name, 'a'); // Открытие файла для добавления
            } else {
                $file = fopen(self::$f_name, 'w'); // Создание файла, если он не существует
            }

            fwrite($file, "\n".$variable);
        } catch (Exception $e) {
            // Обработка исключения
            echo 'File error: ',  $e->getMessage(), "\n";
        } finally {
            // Закрытие файла
            if ($file) fclose($file);
        }
    }
}
