<?php

class MPQReader
{
    static function byte(&$string, &$num_byte) 
    {
        if ($num_byte >= strlen($string))
            return false;

        $tmp = unpack("C",substr($string,$num_byte,1));
        $num_byte++;

        return $tmp[1];
    }

    static function bytes($string, &$num_byte, $length) 
    {
        if (strlen($string) - $num_byte - $length < 0) 
            return false;

        $tmp = substr($string,$num_byte,$length);
        $num_byte += $length;

        return $tmp;
    }

    static function UInt8($string, &$num_byte) 
    {
        if (strlen($string) - $num_byte - 1 < 0)
            return false;

        $tmp = unpack("c",substr($string,$num_byte));
        $num_byte += 1;

        return $tmp[1];
    }

    static function UInt16($string, &$num_byte) 
    {
        if (strlen($string) - $num_byte - 2 < 0)
            return false;

        $tmp = unpack("v",substr($string,$num_byte,2));
        $num_byte += 2;

        return $tmp[1];
    }

    static function UInt32($string, &$num_byte) 
    {
        if (strlen($string) - $num_byte - 4 < 0)
            return false;

        $tmp = unpack("V",substr($string,$num_byte,4));
        $num_byte += 4;

        return $tmp[1];
    }

    static function String($string, &$num_byte) 
    {
        $out = "";

        while ( ($s = MPQReader::byte($string, $num_byte)) != 0)
            $out .= chr($s);

        return $out;
    }

    static function VLFNumber($string, &$num_byte) 
    {
        $number = 0;
        $first = true;
        $multiplier = 1;

        for ($i = self::byte($string,$num_byte),$bytes = 0; true; $i = self::byte($string,$num_byte), $bytes++) 
        {
            $number += ($i & 0x7F) * pow(2,$bytes * 7);

            if ($first)
             {
                if ($number & 1) 
                {
                    $multiplier = -1;
                    $number--;
                }
                $first = false;
            }

            if (($i & 0x80) == 0) break;
        }

        $number *= $multiplier;
        $number /= 2; // can't use right-shift because the datatype will be float for large values on 32-bit systems
        return $number;
    }
}

?>