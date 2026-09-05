<?php
class GoogleAuthenticator {
    public static function checkCode(string $secret, string $code): bool {$time = floor(time() / 30);
        for ($i = -1; $i <= 1; $i++) {
            if (self::getCode($secret,$time + $i) ===$code) return true;
        }
        return false;
    }

    private static function getCode(string $secret, int$time): string {
        $map = ['A'=>0,'B'=>1,'C'=>2,'D'=>3,'E'=>4,'F'=>5,'G'=>6,'H'=>7,'I'=>8,'J'=>9,'K'=>10,'L'=>11,'M'=>12,'N'=>13,'O'=>14,'P'=>15,'Q'=>16,'R'=>17,'S'=>18,'T'=>19,'U'=>20,'V'=>21,'W'=>22,'X'=>23,'Y'=>24,'Z'=>25,'2'=>26,'3'=>27,'4'=>28,'5'=>29,'6'=>30,'7'=>31];$secret = strtoupper($secret);$n = 0; $j = 0; $binary = '';
        for ($i = 0; $i < strlen($secret);$i++) {
            if (!isset($map[$secret[$i]])) continue;
            $n = ($n << 5) + $map[$secret[$i]];$j += 5;
            if ($j >= 8) {$j -= 8;
                $binary .= chr(($n & (0xFF << $j)) >>$j);
            }
        }
        $timeData = pack('N*', 0) . pack('N*', $time);$hash = hash_hmac('sha1', $timeData,$binary, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $value = unpack('N', substr($hash,$offset, 4));
        $value =$value[1] & 0x7FFFFFFF;
        return str_pad((string)($value % 1000000), 6, '0', STR_PAD_LEFT);
    }
}
