<?php

namespace App\Helpers\Classes;

class Helper
{
    /**
     * @throws \Exception
     */
    public static function randomPassword($length, $onlyDigit = false): string
    {
        $alphabet = $onlyDigit ? '1234567890' : 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';

        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $length; $i++) {
            $n = random_int(0, $alphaLength);
            $pass[] = $alphabet[$n] == '0' ? '1' : $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}
