<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\Bookmark;
use app\models\Rating;

class BookmarkController extends Controller
{
  public function beforeAction($action)
  {
    if (Yii::$app->user->isGuest) {
      return $this->redirect(['auth/login']);
    }
    return parent::beforeAction($action);
  }

  public function actionIndex()
  {
    $bookmarks = Bookmark::getUserBookmarks(Yii::$app->user->id);
    $ratingCounts = [];
    $userRatings = [];

    foreach ($bookmarks as $bookmark) {
      $url = $bookmark->url;
      $ratingCounts[$url] = Rating::getRatingCounts($url);
      
      if (!Yii::$app->user->isGuest) {
        $userRating = Rating::getUserRating(Yii::$app->user->id, $url);
        if ($userRating) {
          $userRatings[$url] = $userRating->rating_type;
        }
      }
    }

    return $this->render('index', [
      'bookmarks' => $bookmarks,
      'ratingCounts' => $ratingCounts,
      'userRatings' => $userRatings,
      'isGuest' => Yii::$app->user->isGuest,
    ]);
  }

  public function actionAdd()
  {
      Yii::$app->response->format = Response::FORMAT_JSON;

    if (!Yii::$app->user->isGuest && Yii::$app->request->isPost) {
      $articleData = Yii::$app->request->post('article');

      if (Bookmark::isAlreadyBookmarked(Yii::$app->user->id, $articleData['url'])) {
        return ['success' => false, 'message' => 'Article already bookmarked'];
      }

      $bookmark = Bookmark::createBookmark(Yii::$app->user->id, $articleData);

      if ($bookmark->save()) {
        return [
          'success' => true,
          'message' => 'Article bookmarked successfully',
        ];
      } else {
        Yii::error('Failed to save bookmark: ' . print_r($bookmark->errors, true));
        return ['success' => false, 'message' => 'Failed to bookmark article'];
      }
    }

    return ['success' => false, 'message' => 'Invalid request'];
  }

  public function actionRemove()
  {
    Yii::$app->response->format = Response::FORMAT_JSON;

    if (!Yii::$app->user->isGuest && Yii::$app->request->isPost) {
      $id = Yii::$app->request->post('id');

      if (empty($id)) {
        return ['success' => false, 'message' => 'Bookmark ID is required'];
      }

      $bookmark = Bookmark::findOne([
        'id' => $id,
        'user_id' => Yii::$app->user->id,
      ]);

      if ($bookmark && $bookmark->delete()) {
          return ['success' => true, 'message' => 'Bookmark removed successfully'];
      } else {
          return ['success' => false, 'message' => 'Bookmark not found or you do not have permission'];
      }
    }

    return ['success' => false, 'message' => 'Invalid request'];
  } 

  public function actionRemoveByUrl()
  {
    Yii::$app->response->format = Response::FORMAT_JSON;

    if (!Yii::$app->user->isGuest && Yii::$app->request->isPost) {
      $articleData = Yii::$app->request->post('article');

      $deleted = Bookmark::deleteByUrl(Yii::$app->user->id, $articleData['url']);

      if ($deleted) {
        return ['success' => true, 'message' => 'Bookmark removed successfully'];
      } else {
        return ['success' => false, 'message' => 'Bookmark not found'];
      }
    }

    return ['success' => false, 'message' => 'Invalid request'];
  }
}