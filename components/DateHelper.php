<?php

namespace app\components;

class DateHelper
{
    public static function formatToIndonesiaDateTime($isoDate)
    {
        if (empty($isoDate)) {
            return '-';
        }

        $timestamp = strtotime($isoDate);

        return gmdate('d/m/Y H:i:s', $timestamp + (7 * 3600));
    }
    
    public static function formatToIndonesiaDate($isoDate)
    {
        if (empty($isoDate)) {
            return '-';
        }

        $timestamp = strtotime($isoDate);

        return gmdate('d/m/Y', $timestamp + (7 * 3600));
    }
}
