<?php

class Mailer {
    public static function sendMail(string $to, string $subject, string $message): bool {
        $host = 'ssl://smtp.gmail.com';
        $port = 465;
        $username = 'amr.mansour.mohamed1@gmail.com';
        $password = 'faqutwlhlpqqhbps';
        $timeout = 30;

        $socket = @stream_socket_client($host . ':' . $port, $errno, $errstr, $timeout);
        if (!$socket) {
            error_log("SMTP Error: Could not connect to $host:$port - $errstr ($errno)");
            return false;
        }

        stream_set_timeout($socket, $timeout);

        $serverRes = self::readResponse($socket);
        if (substr($serverRes, 0, 3) !== '220') {
            return self::closeAndReturn($socket, false, "Connection error: $serverRes");
        }

        self::sendCommand($socket, "EHLO localhost");
        $serverRes = self::readResponse($socket);
        if (substr($serverRes, 0, 3) !== '250') {
            return self::closeAndReturn($socket, false, "EHLO error: $serverRes");
        }

        self::sendCommand($socket, "AUTH LOGIN");
        $serverRes = self::readResponse($socket);
        if (substr($serverRes, 0, 3) !== '334') {
            return self::closeAndReturn($socket, false, "AUTH LOGIN error: $serverRes");
        }

        self::sendCommand($socket, base64_encode($username));
        $serverRes = self::readResponse($socket);
        if (substr($serverRes, 0, 3) !== '334') {
            return self::closeAndReturn($socket, false, "Username error: $serverRes");
        }

        self::sendCommand($socket, base64_encode($password));
        $serverRes = self::readResponse($socket);
        if (substr($serverRes, 0, 3) !== '235') {
            return self::closeAndReturn($socket, false, "Password error: $serverRes");
        }

        self::sendCommand($socket, "MAIL FROM:<$username>");
        $serverRes = self::readResponse($socket);
        if (substr($serverRes, 0, 3) !== '250') {
            return self::closeAndReturn($socket, false, "MAIL FROM error: $serverRes");
        }

        self::sendCommand($socket, "RCPT TO:<$to>");
        $serverRes = self::readResponse($socket);
        if (substr($serverRes, 0, 3) !== '250' && substr($serverRes, 0, 3) !== '251') {
            return self::closeAndReturn($socket, false, "RCPT TO error: $serverRes");
        }

        self::sendCommand($socket, "DATA");
        $serverRes = self::readResponse($socket);
        if (substr($serverRes, 0, 3) !== '354') {
            return self::closeAndReturn($socket, false, "DATA error: $serverRes");
        }

        $headers = "From: security@mystore.com\r\n";
        $headers .= "To: $to\r\n";
        $headers .= "Subject: $subject\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "\r\n";

        self::sendCommand($socket, $headers . $message . "\r\n.");
        $serverRes = self::readResponse($socket);
        if (substr($serverRes, 0, 3) !== '250') {
            return self::closeAndReturn($socket, false, "Message sending error: $serverRes");
        }

        self::sendCommand($socket, "QUIT");
        return self::closeAndReturn($socket, true);
    }

    private static function sendCommand($socket, string $command): void {
        fwrite($socket, $command . "\r\n");
    }

    private static function readResponse($socket): string {
        $response = '';
        while ($str = fgets($socket, 515)) {
            $response .= $str;
            if (substr($str, 3, 1) === ' ') {
                break;
            }
        }
        return $response;
    }

    private static function closeAndReturn($socket, bool $success, string $errorLog = null): bool {
        if ($errorLog !== null) {
            error_log("SMTP Error: $errorLog");
        }
        fclose($socket);
        return $success;
    }
}
