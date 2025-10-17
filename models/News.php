<?php

namespace app\models;

use app\components\DateHelper;
use Yii;

class News
{
  public static function getTopHeadlines($category = null, $keyword = null){
    $apiKey = 'dfec6e5bc08245e090be4abaae7c2e76';
    $url  = 'https://newsapi.org/v2/top-headlines?country=us&apiKey=' . $apiKey;
    
    if ($category !== null && !empty($category)) $url .= '&category=' . urlencode($category);
    if ($keyword !== null && !empty($keyword)) $url .= '&q=' . urlencode($keyword);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'MyNewsPortal/1.0');
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
      $error = curl_error($ch);
      curl_close($ch);
      return ['error' => $error];
    }
    
    curl_close($ch);

    $data = json_decode($response, true);

    if ($data['status'] === 'error') {
      return [
        'status' => 'error',
        'code' => $data['code'],
        'message' => $data['message']
      ];
    }

    if (isset($data['articles']) && is_array($data['articles'])) {
      usort($data['articles'], function ($a, $b) {
        $timeA = strtotime($a['publishedAt']);
        $timeB = strtotime($b['publishedAt']);
        return $timeB <=> $timeA; 
      });

      foreach ($data['articles'] as &$article) {
        if (!empty($article['publishedAt'])) {
          $article['publishedAt'] = DateHelper::formatToIndonesiaDateTime($article['publishedAt']);
        }
      }
      unset($article);
    }

    return [
      'status' => $data['status'] ?? 'error',
      'totalResults' => $data['totalResults'] ?? 0,
      'articles' => $data['articles'] ?? [],
    ];
  }
}