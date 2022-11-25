<?php

namespace Utils;

abstract class TOTP
{
    private static string $base32Chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ234567";
    private static function base32Decode(string $string):  string
    {
        $string = strtoupper($string);
        $string = str_replace(" ", "", $string);
        $string = str_split($string);
        $result = "";
        $buffer = 0;
        $bufferLength = 0;
        foreach ($string as $char) {
            $buffer = $buffer << 5;
            $buffer = $buffer | (int) (strpos(self::$base32Chars, $char));
            $bufferLength += 5;
            if ($bufferLength >= 8) {
                $bufferLength -= 8;
                $result .= chr($buffer >> $bufferLength);
                $buffer &= (1 << $bufferLength) - 1;
            }
        }
        return $result;
    }

    public static function getTOTP(string $secret, int $timeSlice = 30): int
    {
        $secret = self::base32Decode($secret);
        $timeSlice = floor(time() / $timeSlice);
        $timeSlice = pack("N", $timeSlice);
        $timeSlice = str_pad($timeSlice, 8, chr(0), STR_PAD_LEFT);
        $hash = hash_hmac("sha1", $timeSlice, $secret, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $hashPart = substr($hash, $offset, 4);
        $value = unpack("N", $hashPart);
        $value = $value[1];
        $value = $value & 0x7FFFFFFF;
        $modulo = pow(10, 6);
        return $value % $modulo;
    }

    public static function verifyTOTP(string $secret, int $code): bool
    {
        if (self::getTOTP($secret) === $code) {
            return true;
        }
        return false;
    }


    public static function generatePrivateKey(): string
    {
        $alphabet = str_split(self::$base32Chars);
        $key = '';
        for ($i = 0; $i < 16; $i++) $key .= $alphabet[mt_rand(0,31)];
        return $key;
    }

}