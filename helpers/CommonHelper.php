<?php

namespace app\helpers;

class CommonHelper
{
  public static function formatToIndonesiaDateTime($isoDate)
  {
    if (empty($isoDate)) {
      return '-';
    }

    $timestamp = strtotime($isoDate);

    return gmdate('d/m/Y H:i:s', $timestamp + 7 * 3600);
  }

  public static function formatToIndonesiaDate($isoDate)
  {
    if (empty($isoDate)) {
      return '-';
    }

    $timestamp = strtotime($isoDate);

    return gmdate('d/m/Y', $timestamp + 7 * 3600);
  }

  public static function remainingTimeBlocked($blockedUntil)
  {
    $remaining = strtotime($blockedUntil) - time();
    
    if ($remaining <= 0) return '0 seconds';
    
    $minutes = floor($remaining / 60);
    $seconds = $remaining % 60;
    
    if ($minutes > 0) {
      return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . 
              ($seconds > 0 ? ' and ' . $seconds . ' second' . ($seconds > 1 ? 's' : '') : '');
    }
    
    return $seconds . ' second' . ($seconds > 1 ? 's' : '');
  }

  public static function createFromFormat($format, $time)
  {
    $dateTime = \DateTime::createFromFormat($format, $time);
    if ($dateTime === false) {
      return null;
    }
    return $dateTime;
  } 
}
