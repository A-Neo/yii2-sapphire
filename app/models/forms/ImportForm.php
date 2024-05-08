<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class ImportForm extends Model
{

    /**
     * @var UploadedFile
     */
    public $file;

    public function rules() {
        return [
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xls'],
        ];
    }

    public function attributeLabels() {
        return [
            'file' => Yii::t('site', 'File'),
        ];
    }

}