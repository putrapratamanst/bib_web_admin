<?php

namespace App\Helpers;

class TerbilangHelper
{
    public static function convert($number)
    {
        $number = abs($number);
        $words = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($number < 12) {
            $temp = " " . $words[$number];
        } else if ($number < 20) {
            $temp = self::convert($number - 10) . " belas";
        } else if ($number < 100) {
            $temp = self::convert($number / 10) . " puluh" . self::convert($number % 10);
        } else if ($number < 200) {
            $temp = " seratus" . self::convert($number - 100);
        } else if ($number < 1000) {
            $temp = self::convert($number / 100) . " ratus" . self::convert($number % 100);
        } else if ($number < 2000) {
            $temp = " seribu" . self::convert($number - 1000);
        } else if ($number < 1000000) {
            $temp = self::convert($number / 1000) . " ribu" . self::convert($number % 1000);
        } else if ($number < 1000000000) {
            $temp = self::convert($number / 1000000) . " juta" . self::convert($number % 1000000);
        } else if ($number < 1000000000000) {
            $temp = self::convert($number / 1000000000) . " milyar" . self::convert(fmod($number, 1000000000));
        } else if ($number < 1000000000000000) {
            $temp = self::convert($number / 1000000000000) . " trilyun" . self::convert(fmod($number, 1000000000000));
        }
        return $temp;
    }

    public static function terbilang($number, $currency = 'rupiah')
    {
        if ($number < 0) {
            $hasil = "minus " . trim(self::convert($number));
        } else {
            $hasil = trim(self::convert($number));
        }
        return ucwords($hasil . " " . $currency);
    }
}
