<?php

namespace app\models;

use app\helpers\CommonHelper;
use Yii;

class News
{
  private static $mode = 'file'; // options: 'api' | 'file'
  
  private static function formatDates(&$data)
  {
    foreach ($data['articles'] as &$article) {
      if (!empty($article['publishedAt'])) {
        $article['publishedAt'] = CommonHelper::formatToIndonesiaDateTime(
          $article['publishedAt'],
        );
      }
    }
    unset($article);
  }

  public static function getTopHeadlines($category = null, $keyword = null)
  {
    if (self::$mode === 'file') {
      return self::getFromFile($category, $keyword);
    }
    return self::getFromApi($category, $keyword);
  }

  private static function getFromFile($category, $keyword)
  {
    $baseDir = \Yii::getAlias('@app/data/');
    $filePath =
      $baseDir . ($category ? '/news-' . $category . '.json' : 'news-all.json');

    if (!file_exists($filePath)) {
      return [
        'status' => 'error',
        'code' => 'file_not_found',
        'message' => 'File lokal tidak ditemukan: ' . $filePath,
      ];
    }

    $jsonData = file_get_contents($filePath);
    $data = json_decode($jsonData, true);

    if (!$data || $data['status'] !== 'ok') {
      return [
        'status' => 'error',
        'code' => 'something_went_wrong',
        'message' => 'Format file tidak valid atau kosong.',
      ];
    }

    if (!empty($keyword)) {
      $filtered = [];
      foreach ($data['articles'] as $article) {
        foreach ($article as $value) {
          if (is_string($value) && stripos($value, $keyword) !== false) {
            $filtered[] = $article;
            break;
          }
        }
      }
      $data['articles'] = $filtered;
    }

    $data['articles'] = array_values($data['articles']);

    self::formatDates($data);
    return $data;
  }

  public static function getFromApi($category = null, $keyword = null)
  {
    $apiKey = getenv('NEWSAPI_KEY');
    $baseUrl = getenv('NEWSAPI_URL');

    if (!$apiKey || !$baseUrl) {
      return [
        'status' => 'error',
        'code' => 'envonment_variable_missing',
        'message' => 'API key atau URL tidak ditemukan',
      ];
    }

    $url = $baseUrl . '/top-headlines?country=us&apiKey=' . urlencode($apiKey);
    if ($category !== null && !empty($category)) {
      $url .= '&category=' . urlencode($category);
    }
    if ($keyword !== null && !empty($keyword)) {
      $url .= '&q=' . urlencode($keyword);
    }

    $startTime = microtime(true);
    $logData = [
      'endpoint' => $url,
      'method' => 'GET',
      'request_params' => json_encode([
        'category' => $category,
        'keyword' => $keyword,
        'country' => 'us'
      ]),
      'request_headers' => json_encode([
        'User-Agent' => 'MyNewsPortal/1.0'
      ]),
      'ip_address' => Yii::$app->request->userIP,
      'user_agent' => Yii::$app->request->userAgent,
      'user_id' => Yii::$app->user->isGuest ? null : Yii::$app->user->id,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'MyNewsPortal/1.0');
    curl_setopt($ch, CURLOPT_HEADER, true); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $duration = round((microtime(true) - $startTime) * 1000);

    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);

    $logData['response_status'] = $httpCode;
    $logData['duration_ms'] = $duration;
    $logData['response_headers'] = ApiLog::parseHeaders($headers);
    $logData['response_body'] = ApiLog::truncateResponse($body);

    if (curl_errno($ch)) {
      $error = curl_error($ch);
      curl_close($ch);
      $logData['response_body'] = $error;
      ApiLog::createLog($logData);
      
      return ['error' => $error];
    }

    curl_close($ch);

    ApiLog::createLog($logData);

    $data = json_decode($body, true);

    if (!$data || $data['status'] === 'error') {
      return [
        'status' => 'error',
        'code' => $data['code'] ?? 'parse_error',
        'message' => $data['message'] ?? 'Failed to parse API response',
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
          $article['publishedAt'] = CommonHelper::formatToIndonesiaDateTime(
            $article['publishedAt'],
          );
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
