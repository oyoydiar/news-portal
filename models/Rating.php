<?php

namespace app\models;

use yii\db\ActiveRecord;

class Rating extends ActiveRecord
{
  const TYPE_THUMBS_UP = 'thumbs_up';
  const TYPE_THUMBS_DOWN = 'thumbs_down';

  public static function tableName()
  {
    return 'ratings';
  }

  public function rules()
  {
    return [
      [['user_id', 'news_url', 'rating_type'], 'required'],
      [['user_id'], 'integer'],
      [['news_url'], 'string', 'max' => 500],
      [['rating_type'], 'in', 'range' => [self::TYPE_THUMBS_UP, self::TYPE_THUMBS_DOWN]],
      [['created_at'], 'safe'],
    ];
  }

  public function attributeLabels()
  {
    return [
      'id' => 'ID',
      'user_id' => 'User ID',
      'news_url' => 'News URL',
      'rating_type' => 'Rating Type',
      'created_at' => 'Created At',
    ];
  }

  public function getUser()
  {
    return $this->hasOne(User::class, ['id' => 'user_id']);
  }

  public static function getRatingCounts($newsUrl)
  {
    $thumbsUp = self::find()
                  ->where(['news_url' => $newsUrl, 'rating_type' => self::TYPE_THUMBS_UP])
                  ->count();

    $thumbsDown = self::find()
                  ->where(['news_url' => $newsUrl, 'rating_type' => self::TYPE_THUMBS_DOWN])
                  ->count();

    return [
      'thumbs_up' => $thumbsUp,
      'thumbs_down' => $thumbsDown,
    ];
  }

  public static function getUserRating($userId, $newsUrl)
  {
    return self::find()
            ->where(['user_id' => $userId, 'news_url' => $newsUrl])
            ->one();
  }

  public static function rateNews($userId, $newsUrl, $ratingType)
  {
    $existingRating = self::getUserRating($userId, $newsUrl);

    if ($existingRating) {
      if ($existingRating->rating_type === $ratingType) {
        return $existingRating->delete();
      } else {
        $existingRating->rating_type = $ratingType;
        return $existingRating->save();
      }
    } else {
      $rating = new self();
      $rating->user_id = $userId;
      $rating->news_url = $newsUrl;
      $rating->rating_type = $ratingType;
      return $rating->save();
    }
  }
}