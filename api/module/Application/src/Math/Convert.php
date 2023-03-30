<?php

namespace Application\Math;

/**
 * Class Convert
 * @package Application\Math
 */
class Convert
{

    /**
     * @param $number
     * @return float
     */
    public static function formatNumberToFloat($number): float
    {
        $number = str_replace('R$ ', '', $number);
        $number = str_replace(' ', '', $number);
        $number = str_replace(',', '.', $number);

        return floatval($number);
    }

    /**
     * @param $number
     * @return float
     */
    public static function formatNumberToFloatAux($number): float
    {
        $number = str_replace('.', '', $number);
        return str_replace(',', '.', $number);
    }

    /**
     * @param $number
     * @param bool $symbol
     * @return string
     */
    public static function formatFloatToNumber($number, bool $symbol = true): string
    {
        setlocale(LC_MONETARY, 'pt_BR');

        if ($symbol) {
            return 'R$ '.number_format($number,2, ',', '.');
        }
        return number_format($number,2, ',', '.');
    }

    /**
     * @param $number
     * @return string
     */
    public static function formatFloatToNumberBr($number): string
    {
        return number_format($number, 2, ',', '.');
    }
}
