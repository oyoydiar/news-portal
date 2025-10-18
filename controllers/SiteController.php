<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;
use app\models\LoginForm;
use app\models\News;
use app\models\Rating;

class SiteController extends Controller
{
  /**
   * {@inheritdoc}
   */
  public function behaviors()
  {
    return [
      'access' => [
        'class' => AccessControl::class,
        'only' => ['logout'],
        'rules' => [
          [
            'actions' => ['logout'],
            'allow' => true,
            'roles' => ['@'],
          ],
        ],
      ],
      'verbs' => [
        'class' => VerbFilter::class,
        'actions' => [
          'logout' => ['post'],
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function actions()
  {
    return [
      'error' => [
        'class' => 'yii\web\ErrorAction',
      ],
      'captcha' => [
        'class' => 'yii\captcha\CaptchaAction',
        'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
      ],
    ];
  }

  /**
   * Displays homepage.
   *
   * @return string
   */
  public function actionIndex()
  {
    $category = Yii::$app->request->get('category');
    $keyword = Yii::$app->request->get('keyword');
    $ratingCounts = [];
    $userRatings = [];

    $data = News::getTopHeadlines($category, $keyword);

    if (!empty($data['articles'])) {
      foreach ($data['articles'] as $article) {
        $url = $article['url'];
        $ratingCounts[$url] = Rating::getRatingCounts($url);
        
        if (!Yii::$app->user->isGuest) {
          $userRating = Rating::getUserRating(Yii::$app->user->id, $url);
          if ($userRating) {
            $userRatings[$url] = $userRating->rating_type;
          }
        }
      }
    }

    return $this->render('index', [
      'data' => $data,
      'category' => $category,
      'keyword' => $keyword,
      'ratingCounts' => $ratingCounts,
      'userRatings' => $userRatings,
    ]);
  }

  /**
   * Login action.
   *
   * @return Response|string
   */
  public function actionLogin()
  {
    if (!Yii::$app->user->isGuest) {
      return $this->goHome();
    }

    $model = new LoginForm();
    if ($model->load(Yii::$app->request->post()) && $model->login()) {
      return $this->goBack();
    }

    $model->password = '';
    return $this->render('login', [
      'model' => $model,
    ]);
  }

  /**
   * Logout action.
   *
   * @return Response
   */
  public function actionLogout()
  {
    Yii::$app->user->logout();

    return $this->goHome();
  }
}
