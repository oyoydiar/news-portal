<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\Rating;

class RatingController extends Controller
{
  public function beforeAction($action)
  {
    if (Yii::$app->user->isGuest) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return $this->asJson(['success' => false, 'message' => 'Please login to rate articles']);
    }
    return parent::beforeAction($action);
  }

  public function actionRate()
  {
    Yii::$app->response->format = Response::FORMAT_JSON;

    if (!Yii::$app->user->isGuest && Yii::$app->request->isPost) {
      $newsUrl = Yii::$app->request->post('news_url');
      $ratingType = Yii::$app->request->post('rating_type');

      if (empty($newsUrl) || empty($ratingType)) {
        return ['success' => false, 'message' => 'Missing required data'];
      }

      if (!in_array($ratingType, [Rating::TYPE_THUMBS_UP, Rating::TYPE_THUMBS_DOWN])) {
        return ['success' => false, 'message' => 'Invalid rating type'];
      }

      $success = Rating::rateNews(Yii::$app->user->id, $newsUrl, $ratingType);

      if ($success) {
        $counts = Rating::getRatingCounts($newsUrl);
        $userRating = Rating::getUserRating(Yii::$app->user->id, $newsUrl);
        
        return [
          'success' => true,
          'message' => 'Rating submitted successfully',
          'counts' => $counts,
          'user_rating' => $userRating ? $userRating->rating_type : null
        ];
      } else {
        return ['success' => false, 'message' => 'Failed to submit rating'];
      }
    }

    return ['success' => false, 'message' => 'Invalid request'];
  }

  public function actionGetCounts()
  {
    Yii::$app->response->format = Response::FORMAT_JSON;

    $newsUrl = Yii::$app->request->get('news_url');

    if (empty($newsUrl)) {
      return ['success' => false, 'message' => 'News URL is required'];
    }

    $counts = Rating::getRatingCounts($newsUrl);
    $userRating = null;

    if (!Yii::$app->user->isGuest) {
      $userRating = Rating::getUserRating(Yii::$app->user->id, $newsUrl);
    }

    return [
      'success' => true,
      'counts' => $counts,
      'user_rating' => $userRating ? $userRating->rating_type : null
    ];
  }
}