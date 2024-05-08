<?php

namespace app\models;

use app\models\behaviors\TranslateBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii2tech\ar\position\PositionBehavior;

/**
 * This is the model class for table "page".
 *
 * @property int    $id
 * @property string $slug
 * @property string $title
 * @property string $seo_title
 * @property string $seo_description
 * @property string $seo_keywords
 * @property string $text
 * @property int    $position
 * @property int    $status
 * @property int    $created_at
 * @property int    $updated_at
 */
class Page extends \yii\db\ActiveRecord
{

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE   = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'page';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['slug', 'title', 'text'], 'required'],
            [['position'], 'integer'],
            [['slug'], 'unique'],
            [['text'], 'string'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['slug'], 'string', 'max' => 255],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            [['title', 'seo_title', 'seo_description', 'seo_keywords'], 'string', 'max' => 2048],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id'              => Yii::t('site', 'ID'),
            'position'        => Yii::t('site', 'Position'),
            'slug'            => Yii::t('site', 'Slug'),
            'title'           => Yii::t('site', 'Title'),
            'seo_title'       => Yii::t('site', 'Seo title'),
            'seo_description' => Yii::t('site', 'Seo description'),
            'seo_keywords'    => Yii::t('site', 'Seo keywords'),
            'text'            => Yii::t('site', 'Text'),
            'status'          => Yii::t('site', 'Status'),
            'created_at'      => Yii::t('site', 'Created'),
            'updated_at'      => Yii::t('site', 'Updated'),
        ];
    }

    public function behaviors() {
        return [
            TimestampBehavior::class,
            PositionBehavior::class,
            [
                'class'      => TranslateBehavior::class,
                'attributes' => ['title', 'seo_title', 'seo_description', 'seo_keywords', 'text'],
            ],

        ];
    }

    public function getStatusesList() {
        return [
            self::STATUS_INACTIVE => Yii::t('site', 'Inactive'),
            self::STATUS_ACTIVE   => Yii::t('site', 'Active'),
        ];
    }

}
