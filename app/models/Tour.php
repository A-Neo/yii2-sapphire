<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%tour}}".
 *
 * @property int           $id
 * @property int|null      $user_id
 * @property resource|null $passport
 * @property string        $number
 * @property string        $whatsapp
 * @property int           $status
 * @property int           $created_at
 * @property int           $updated_at
 *
 * @property User          $user
 * @property TourName      $tourName
 */
class Tour extends \yii\db\ActiveRecord
{

    const STATUS_REJECTED  = -1;
    const STATUS_NEW       = 0;
    const STATUS_CONFIRMED = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%tour}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_id', 'tour_name_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['passport'], 'safe'],
            [['tour_name_id', 'number', 'whatsapp'], 'required'],
            [['passport'], 'required', 'when' => function ($model) {
                return empty($model->passport);
            }],
            [['status'], 'default', 'value' => self::STATUS_NEW],
            [['status'], 'in', 'range' => [self::STATUS_REJECTED, self::STATUS_NEW, self::STATUS_CONFIRMED]],
            [['number', 'whatsapp'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['tour_name_id'], 'exist', 'skipOnError' => true, 'targetClass' => TourName::class, 'targetAttribute' => ['tour_name_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id'           => Yii::t('site', 'ID'),
            'user_id'      => Yii::t('site', 'User'),
            'tour_name_id' => Yii::t('site', 'Tour'),
            'passport'     => Yii::t('site', 'International passport photo'),
            'number'       => Yii::t('site', 'International passport number'),
            'whatsapp'     => Yii::t('site', 'Whatsapp number'),
            'status'       => Yii::t('site', 'Status'),
            'created_at'   => Yii::t('site', 'Created'),
            'updated_at'   => Yii::t('site', 'Updated'),
        ];
    }

    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }

    public function getStatusName() {
        return $this->getStatusesList()[(int)$this->status];
    }

    public function getStatusesList() {
        return [
            self::STATUS_REJECTED  => Yii::t('site', 'Rejected'),
            self::STATUS_NEW       => Yii::t('site', 'New'),
            self::STATUS_CONFIRMED => Yii::t('site', 'Confirmed'),
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Gets query for [[TourName]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTourName() {
        return $this->hasOne(TourName::class, ['id' => 'tour_name_id']);
    }

}
