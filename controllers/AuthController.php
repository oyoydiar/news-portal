<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\User;
use app\helpers\CommonHelper;

class AuthController extends Controller
{
  public function beforeAction($action)
  {
    if (!Yii::$app->user->isGuest) {
      return $this->redirect(['/my-news-portal']);
    }

    return parent::beforeAction($action);
  }

  private function validateRegisterForm($name, $email, $password, $retypePassword)
  {
    if (empty($name) || empty($email) || empty($password) || empty($retypePassword)) {
      return 'All fields are required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return 'Email is not valid.';
    }

    if ($password !== $retypePassword) {
      return 'Password does not match.';
    }

    $passwordError = User::validatePasswordStrength($password);
    if ($passwordError) {
      return $passwordError;
    }

    return null;
  }

  private function validateLoginForm($email, $password)
  {
    if (empty($email) || empty($password)) {
      return 'Email and password must be filled.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return 'Email is not valid.';
    }

    return null;
  }

  public function actionRegisterSuccess()
  {
    if (!Yii::$app->user->isGuest) {
      return $this->redirect(['/my-news-portal']);
    }

    return $this->render('register-success');
  }

  public function actionRegister()
  {
    if (!Yii::$app->user->isGuest) {
      return $this->redirect(['/my-news-portal']);
    }

    $request = Yii::$app->request;

    if ($request->isPost) {
      $name = $request->post('name');
      $email = $request->post('email');
      $password = $request->post('password');
      $retypePassword = $request->post('retypePassword');

      $validationError = $this->validateRegisterForm($name, $email, $password, $retypePassword);
      if ($validationError) {
        Yii::$app->session->setFlash('error', $validationError);
        return $this->refresh();
      }

      if (User::isEmailTaken($email)) {
        Yii::$app->session->setFlash('error', 'Email is already taken.');
        return $this->refresh();
      }

      $user = User::createUser($name, $email, $password);

      if ($user->save()) {
        return $this->redirect(['auth/register-success']);
      } else {
        Yii::$app->session->setFlash(
          'error',
          'Something went wrong. Please try again.'
        );
      }
    }

    return $this->render('register');
  }

  public function actionLogin()
  {
    if (!Yii::$app->user->isGuest) {
      return $this->redirect(['/my-news-portal']);
    }

    $request = Yii::$app->request;

    if ($request->isPost) {
      $email = $request->post('email');
      $password = $request->post('password');

      $validationError = $this->validateLoginForm($email, $password);
      if ($validationError) {
        Yii::$app->session->setFlash('error', $validationError);
        return $this->refresh();
      }

      $user = User::findByEmail($email);

      if (!$user) {
        Yii::$app->session->setFlash('error', 'Email not found.');
        return $this->refresh();
      }

      if ($user->isBlocked()) {
        $remainingTime = CommonHelper::remainingTimeBlocked($user->blocked_until);
        Yii::$app->session->setFlash(
          'error',
          'Account blocked. Please try again in ' . $remainingTime
        );
        return $this->refresh();
      }

      if ($user->validatePassword($password)) {
        $user->resetLoginAttempts();
        Yii::$app->user->login($user);
        return $this->redirect(['/my-news-portal']);
      } else {
        $user->handleFailedLogin();
        Yii::$app->session->setFlash('error', 'Password is wrong.');
        return $this->refresh();
      }
    }

    return $this->render('login');
  }

  public function actionLogout()
  {
    Yii::$app->user->logout();
    return $this->redirect(['/my-news-portal']);
  }
}