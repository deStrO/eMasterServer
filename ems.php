<?php

/*
 * * +----------------------------------------------------------------------------+
 * * |	@File name : 	ems.php
 * * |  @Author : 	Julien Pardons - deStrO
 * * |  @Date : 	Décembre 2011
 * * |  @Project : 	eMasterServer - eSport-Tools
 * * +----------------------------------------------------------------------------+
 */
error_reporting(E_ERROR);
include("function.php");
define('VERSION', '1.0');
define('BOT_IP', '192.168.1.1');
define('BOT_PORT', '55555');
define('PACKET_BASE', "\xFF\xFF\xFF\xFF\x66\x0A");

$listIp = array(
    "127.0.0.1:27015",
    "127.0.0.1:27016",
);


echo "
       __  __           _              _____                          
      |  \/  |         | |            / ____|                         
   ___| \  / | __ _ ___| |_  ___ _ __| (___   ___ _ ____   _____ _ __ 
  / _ \ |\/| |/ _` / __| __|/ _ \ '__|\___ \ / _ \ '__\ \ / / _ \ '__|
 |  __/ |  | | (_| \__ \ |_|  __/ |   ____) |  __/ |   \ V /  __/ |   
  \___|_|  |_|\__,_|___/\__|\___|_|  |_____/ \___|_|    \_/ \___|_|   

";

echo "\teMasterServer Version: " . VERSION . "\r\n";


// Memory Limit
echo "\tLooking for memory: ";
@ini_set('memory_limit', -1);
if (ini_get('memory_limit') == -1)
    echo "unlimited";
else
    echo ini_get('memory_limit');
echo "\r\n";

// Version PHP
echo "\tPHP Version: " . phpversion() . "\r\n";

// Garbage collector
if (function_exists('gc_enabled')) {
    define('GC_ENABLE', true);
    $ms = displayColor('green', "actived", false, true);
    gc_enable();
} else {
    define('GC_ENABLE', false);
    $ms = displayColor('red', "not found", false, true);
}

echo "\tGarbage collector: $ms\r\n";
unset($ms);

// Chargement des classes
echo "\tLoading class and function: ";
ob_start();
include("socket.class.php");
ob_end_clean();
displayColor("green", "ok");

echo "\tOpening UDP sockets (" . BOT_IP . ":" . BOT_PORT . ") : ";
try {
    $socket = new Socket(BOT_IP, BOT_PORT);
} catch (Exception $ex) {
    echo RED . "KO" . NORMAL . "\r\n";
    echo "\tAn exception has been throwed: " . $ex->getMessage() . "\r\n";
    die();
}

if (!$socket) {
    displayColor("red", "ERROR: $errno - $errstr");
} else {
    displayColor("green", "ok");
    echo "\r\n";

    function hextobin($v) {
        return pack("H*", $v);
    }

    while (true) {
        $line = $socket->recvfrom($addr);
        if ($line) {
            $m = chr(getByte($line));
            if ($m == 1) {
                $regCode = getByte($line);
                $ip = getString($line);
                $filter = getString($line);
                echo date('Y-m-d H:i:s') . " - packet form $addr - $ip\r\n";

                if ($ip == "0.0.0.0:0") {
                    $index = 0;
                } else {
                    $index = array_search($ip, $listIp);
                    if ($index === false) {
                        $index = -1;
                    } else {
                        if (count($listIp) > $index + 1)
                            $index++;
                        else
                            $index = -1;
                    }
                }

                if ($index == -1) {
                    $ipS = "0.0.0.0:0";
                } else {
                    $ipS = $listIp[$index];
                }

                preg_match("/^(\d+).(\d+).(\d+).(\d+):(\d+)$/", $ipS, $m5);

                $ip = array($m5[1], $m5[2], $m5[3], $m5[4]);
                $port = $m5[5];

                $packet = PACKET_BASE;

                foreach ($ip as $i) {
                    $packet .= chr($i);
                }

                $packet.=pack("n", $port);
                $addrs = explode(":", $addr);
                $socket->sendto($packet, $addrs[0], $addrs[1]);
            }
        }
        unset($line);
    }
}
?>

