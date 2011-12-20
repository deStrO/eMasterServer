<?php

/*
 * * +----------------------------------------------------------------------------+
 * * |	@File name : 	socket.class.php
 * * |  @Author : 	Julien Pardons - deStrO
 * * |  @Date : 	Mars 2011
 * * |  @Project : 	eBot - eSport-Tools
 * * +----------------------------------------------------------------------------+
 */

class Socket {

    private $socketsEnabled = false;
    private $socket = null;

    public function Socket($bot_ip, $bot_port) {
        $this->socketsEnabled = extension_loaded("sockets");
        if ($this->socketsEnabled) {
            $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            if ($this->socket) {
                if (socket_bind($this->socket, $bot_ip, $bot_port)) {
                    if (!socket_set_nonblock($this->socket)) {
                        throw new Exception("Can't set non-block mode !");
                    }
                } else {
                    throw new Exception("Can't bind the socket");
                }
            } else {
                throw new Exception("Can't create the socket");
            }
        } else {
            throw new Exception("Extension sockets non activée");
        }
    }

    public function recvfrom(&$ip) {
        if ($this->socketsEnabled) {
            $int = socket_recvfrom($this->socket, $line, 1500, 0, $from, $port);
            if ($int) {
                $ip = $from . ":" . $port;
                return $line;
            } else {
                usleep(1000);
            }
        } else {
            $line = stream_socket_recvfrom($this->socket, 1500, null, $addr);
            $ip = $addr;
            return $line;
        }
    }

    public function sendto($mess, $ip, $port) {
        return socket_sendto($this->socket, $mess, strlen($mess), 0, $ip, $port);
    }

}
?>

