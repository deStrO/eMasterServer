<?php

/*
 * * +----------------------------------------------------------------------------+
 * * |	@File name : 	function.php
 * * |  @Author : 	Julien Pardons - deStrO
 * * |  @Date : 	Mars 2011
 * * |  @Project : 	eBot - eSport-Tools
 * * +----------------------------------------------------------------------------+
 */

if (PHP_OS == "Linux") {
    define('BLACK', "\033[30m");
    define('RED', "\033[31m");
    define('GREEN', "\033[32m");
    define('YELLOW', "\033[33m");
    define('BLUE', "\033[34m");
    define('S', "\033[35m");
    define('NORMAL', "\033[37m");
    define('WHITE', "\033[0m");
} else {
    define('BLACK', "");
    define('RED', "");
    define('GREEN', "");
    define('YELLOW', "");
    define('BLUE', "");
    define('NORMAL', "");
    define('WHITE', "");
    define('S', "");
}
function displayColor($color, $message, $retourLigne = true, $return = false) {
    if (PHP_OS == "Linux") {
        $number = 0;
        switch ($color) {
            case "black" : $number = 30;
                break;
            case "red" : $number = 31;
                break;
            case "green" : $number = 32;
                break;
            case "yellow" : $number = 33;
                break;
            case "blue" : $number = 34;
                break;
            case "s" : $number = 35;
                break;
            default : $number = 37;
        }

        if ($return) {
            return "\033[" . $number . "m" . $message . "\033[37m";
        } else {
            echo "\033[" . $number . "m" . $message . "\033[37m";
            if ($retourLigne)
                echo "\r\n";
        }
    } else {
        if ($return) {
            return $message;
        } else {
            echo $message;
            if ($retourLigne)
                echo "\r\n";
        }
    }
}

function getByte(&$string) {
    $data = substr($string, 0, 1);
    $string = substr($string, 1);
    $data = unpack('Cvalue', $data);

    return $data['value'];
}

function getShortUnsigned(&$string) {
    $data = substr($string, 0, 2);
    $string = substr($string, 2);
    $data = unpack('nvalue', $data);

    return $data['value'];
}

function getShortSigned(&$string) {
    $data = substr($string, 0, 2);
    $string = substr($string, 2);
    $data = unpack('svalue', $data);

    return $data['value'];
}

function getLong(&$string) {
    $data = substr($string, 0, 4);
    $string = substr($string, 4);
    $data = unpack('Vvalue', $data);

    return $data['value'];
}

function getFloat(&$string) {
    $data = substr($string, 0, 4);
    $string = substr($string, 4);
    $array = unpack("fvalue", $data);

    return $array['value'];
}

function getString(&$string) {
    $data = "";
    $byte = substr($string, 0, 1);
    $string = substr($string, 1);

    while (ord($byte) != "0") {
        $data .= $byte;
        $byte = substr($string, 0, 1);
        $string = substr($string, 1);
    }

    return $data;
}

?>