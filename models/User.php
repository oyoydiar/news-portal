<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
  const MAX_LOGIN_ATTEMPTS = 5;
  const BLOCK_DURATION = '+5 minutes';
  const MIN_PASSWORD_LENGTH = 12;

  public static function tableName()
  {
    return 'users';
  }

  public function rules()
  {
    return [
      [['name', 'email', 'password_hash'], 'required'],
      [['name', 'email'], 'string', 'max' => 255],
      [['email'], 'email'],
      [['email'], 'unique'],
      [['failed_login_count'], 'integer'],
      [['created_at', 'blocked_until'], 'safe'],
    ];
  }

  public function attributeLabels()
  {
    return [
      'id' => 'ID',
      'name' => 'Name',
      'email' => 'Email',
      'password_hash' => 'Password Hash',
      'failed_login_count' => 'Failed Login Count',
      'blocked_until' => 'Blocked Until',
      'created_at' => 'Created At',
    ];
  }

  public static function findIdentity($id)
  {
    return static::findOne($id);
  }

  public static function findIdentityByAccessToken($token, $type = null)
  {
    return null;
  }

  public function getId()
  {
    return $this->id;
  }

  public function getAuthKey()
  {
    return null;
  }

  public function validateAuthKey($authKey)
  {
    return false;
  }

  public static function findByEmail($email)
  {
    return static::findOne(['email' => $email]);
  }

  public function validatePassword($password)
  {
    return Yii::$app->security->validatePassword($password, $this->password_hash);
  }

  public function setPassword($password)
  {
    $this->password_hash = Yii::$app->security->generatePasswordHash($password);
  }

  public function isBlocked()
  {
    return $this->blocked_until && strtotime($this->blocked_until) > time();
  }

  public function getRemainingBlockTime()
  {
    if (!$this->blocked_until) {
      return null;
    }
    
    $remaining = strtotime($this->blocked_until) - time();
    return $remaining > 0 ? $remaining : 0;
  }

  public function handleFailedLogin()
  {
    $this->failed_login_count++;
    
    if ($this->failed_login_count >= self::MAX_LOGIN_ATTEMPTS) {
      $this->blocked_until = date('Y-m-d H:i:s', strtotime(self::BLOCK_DURATION));
      $this->failed_login_count = 0;
    }
    
    return $this->save();
  }

  public function resetLoginAttempts()
  {
    $this->failed_login_count = 0;
    $this->blocked_until = null;
    return $this->save();
  }

  public static function validatePasswordStrength($password)
  {
    if (strlen($password) < self::MIN_PASSWORD_LENGTH) {
      return 'Password must be at least ' . self::MIN_PASSWORD_LENGTH . ' characters long.';
    }

    if (!preg_match('/[A-Z]/', $password)) {
      return 'Password must contain at least 1 capital letter.';
    }

    if (!preg_match('/[0-9]/', $password)) {
      return 'Password must contain at least 1 number.';
    }

    if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) {
      return 'Password must contain at least 1 symbol.';
    }

    return null;
  }

  public static function createUser($name, $email, $password)
  {
    $user = new self();
    $user->name = strtoupper($name);
    $user->email = $email;
    $user->setPassword($password);
    $user->created_at = date('Y-m-d H:i:s');
    
    return $user;
  }

  public static function isEmailTaken($email)
  {
    return self::find()->where(['email' => $email])->exists();
  }
}