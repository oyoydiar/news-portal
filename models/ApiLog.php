<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class ApiLog extends ActiveRecord
{
  public static function tableName()
  {
    return 'api_logs';
  }

  public function rules()
  {
    return [
      [['endpoint', 'method'], 'required'],
      [['request_params', 'request_headers', 'response_body', 'response_headers', 'user_agent'], 'string'],
      [['response_status', 'user_id', 'duration_ms'], 'integer'],
      [['endpoint'], 'string', 'max' => 500],
      [['method'], 'string', 'max' => 10],
      [['ip_address'], 'string', 'max' => 45],
      [['created_at'], 'safe'],
    ];
  }

  public function attributeLabels()
  {
    return [
      'id' => 'ID',
      'endpoint' => 'Endpoint',
      'method' => 'Method',
      'request_params' => 'Request Parameters',
      'request_headers' => 'Request Headers',
      'response_status' => 'Response Status',
      'response_body' => 'Response Body',
      'response_headers' => 'Response Headers',
      'ip_address' => 'IP Address',
      'user_agent' => 'User Agent',
      'user_id' => 'User ID',
      'duration_ms' => 'Duration (ms)',
      'created_at' => 'Created At',
    ];
  }

  public function getUser()
  {
      return $this->hasOne(User::class, ['id' => 'user_id']);
  }

  public static function createLog($logData)
  {
    try {
      $log = new self();
      $log->endpoint = $logData['endpoint'] ?? null;
      $log->method = $logData['method'] ?? 'GET';
      $log->request_params = $logData['request_params'] ?? null;
      $log->request_headers = $logData['request_headers'] ?? null;
      $log->response_status = $logData['response_status'] ?? null;
      $log->response_body = $logData['response_body'] ?? null;
      $log->response_headers = $logData['response_headers'] ?? null;
      $log->ip_address = $logData['ip_address'] ?? Yii::$app->request->userIP;
      $log->user_agent = $logData['user_agent'] ?? Yii::$app->request->userAgent;
      $log->user_id = $logData['user_id'] ?? (Yii::$app->user->isGuest ? null : Yii::$app->user->id);
      $log->duration_ms = $logData['duration_ms'] ?? null;
        
      return $log->save();
    } catch (\Exception $e) {
      Yii::error('Failed to save API log: ' . $e->getMessage());
      return false;
    }
  }

  public static function parseHeaders($headers)
  {
    $headersArray = [];
    $headerLines = explode("\r\n", $headers);
    
    foreach ($headerLines as $line) {
      if (strpos($line, ':') !== false) {
        list($key, $value) = explode(':', $line, 2);
        $headersArray[trim($key)] = trim($value);
      }
    }
    
    return json_encode($headersArray);
  }

  public static function truncateResponse($content, $maxLength = 10000)
  {
    if (strlen($content) > $maxLength) {
      return substr($content, 0, $maxLength) . '...';
    }
    return $content;
  }
}