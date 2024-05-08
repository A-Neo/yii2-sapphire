<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;


class EmeraldOrder extends \yii\db\ActiveRecord
{
    private $_username;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'emerald_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'integer'],
            [['user_id', 'product_id', 'fullname', 'country', 'birth_date', 'phone', 'order', 'city', 'zip_code', 'whatsapp', 'status'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'user_id',
            'fullname'             => Yii::t('site', 'Full name'),
            'country'               => Yii::t('site', 'Country'),
            'birth_date'            => Yii::t('site', 'Birth date'),
            'phone'                 => Yii::t('site', 'Phone'),
            'order' => 'Заказ',
            'product_id' => 'Товар',
            'city' => 'Город',
            'zip_code' => 'Индекс',
            'whatsapp' => 'WhatsApp',
            'status' => 'Статус',
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => time(),
            ],
        ];
    }

    public function formSave($data)
    {
        $order = new self();
        $order->id_user = $data['id_user'];
        $order->fullname = $data['fullname'];
        $order->country = $data['country'];
        $order->birth_date = $data['birth_date'];
        $order->phone = $data['phone'];
        $order->order = 'Пластырь';
        $order->product_id = 1;
        $order->city = $data['city'];
        $order->zip_code = $data['zip_code'];
        $order->whatsapp = $data['whatsapp'];
        $order->status = $data['status'];
        if ($order->save) return true;
    }


}
