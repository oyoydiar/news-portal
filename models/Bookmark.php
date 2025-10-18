<?php

namespace app\models;

use yii\db\ActiveRecord;

class Bookmark extends ActiveRecord
{
  public static function tableName()
  {
    return 'bookmarks';
  }

  public function rules()
  {
    return [
      [['user_id', 'url', 'title'], 'required'],
      [['user_id'], 'integer'],
      [['description'], 'string'],
      [['published_at', 'created_at'], 'safe'],
      [['url', 'title', 'image_url', 'source', 'author'], 'string', 'max' => 255],
    ];
  }

  public function getUser()
  {
    return $this->hasOne(User::class, ['id' => 'user_id']);
  }

  public function beforeSave($insert)
  {
    if (parent::beforeSave($insert)) {
      if ($insert) {
        $this->created_at = date('Y-m-d H:i:s');
      }
      return true;
    }
    return false;
  }

  public static function isAlreadyBookmarked($userId, $url)
  {
    return self::find()
            ->where(['user_id' => $userId, 'url' => $url])
            ->exists();
  }

  public static function findByUrl($userId, $url)
  {
    return self::find()
            ->where(['user_id' => $userId, 'url' => $url])
            ->one();
  }

  public static function createBookmark($userId, $articleData)
  {
    $bookmark = new self();
    $bookmark->user_id = $userId;
    $bookmark->url = $articleData['url'];
    $bookmark->title = $articleData['title'];
    $bookmark->description = $articleData['description'] ?? '';
    $bookmark->image_url = $articleData['image'] ?? '';
    $bookmark->source = $articleData['source'] ?? '';
    $bookmark->author = $articleData['author'] ?? '';

    if (!empty($articleData['published_at'])) {
      $publishedAt = $articleData['published_at'];
      $dateTime = \DateTime::createFromFormat('d/m/Y H:i:s', $publishedAt);
      if ($dateTime) {
        $bookmark->published_at = $dateTime->format('Y-m-d H:i:s');
      }
    }

    return $bookmark;
  }

  public static function deleteBookmark($userId, $id)
  {
    $bookmark = self::findOne(['id' => $id, 'user_id' => $userId]);
    return $bookmark ? $bookmark->delete() : false;
  }

  public static function deleteByUrl($userId, $url)
  {
    $bookmark = self::findByUrl($userId, $url);
    return $bookmark ? $bookmark->delete() : false;
  }

  public static function getUserBookmarks($userId)
  {
    return self::find()
            ->where(['user_id' => $userId])
            ->orderBy(['published_at' => SORT_DESC, 'created_at' => SORT_DESC])
            ->all();
  }
}