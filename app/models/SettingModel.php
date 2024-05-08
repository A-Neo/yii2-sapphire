<?php

namespace app\models;

use app\components\LanguageSelector;
use app\models\enumerables\SettingType;
use Yii;
use yii\helpers\ArrayHelper;
use yii2mod\editable\EditableAction;
use yii2mod\settings\models\enumerables\SettingStatus;

/**
 *
 * @property int $system
 */
class SettingModel extends \yii2mod\settings\models\SettingModel
{

    const STATUS_ACTIVE   = SettingStatus::ACTIVE;
    const STATUS_INACTIVE = SettingStatus::INACTIVE;

    const SYSTEM_OFF       = 0;
    const SYSTEM_ON        = 1;
    const SYSTEM_PROTECTED = 2;

    public function actions(): array {
        return [
            'edit-setting' => [
                'class'       => EditableAction::class,
                'modelClass'  => SettingModel::class,
                'forceCreate' => false,
            ],
        ];
    }

    public function rules(): array {
        return [
            [['section', 'key', 'value'], 'required'],
            [['section', 'key'], 'unique', 'targetAttribute' => ['section', 'key']],
            [['type'], 'string'],
            [['section', 'key', 'description'], 'string', 'max' => 255],
            [['status'], 'integer'],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['status'], 'in', 'range' => SettingStatus::getConstantsByName()],
            [['system'], 'boolean'],
            [['status'], 'default', 'value' => 0],
            [['type', 'value'], 'safe'],
            [['system'], 'integer'],
            [['system'], 'default', 'value' => self::SYSTEM_OFF],
            [['system'], 'in', 'range' => [self::SYSTEM_OFF, self::SYSTEM_ON, self::SYSTEM_PROTECTED]],
        ];
    }

    public function attributeLabels(): array {
        return [
            'id'          => Yii::t('site', 'ID'),
            'type'        => Yii::t('site', 'Type'),
            'section'     => Yii::t('site', 'Section'),
            'key'         => Yii::t('site', 'Key'),
            'value'       => Yii::t('site', 'Value'),
            'status'      => Yii::t('site', 'Status'),
            'system'      => Yii::t('site', 'System'),
            'description' => Yii::t('site', 'Description'),
            'created_at'  => Yii::t('site', 'Created'),
            'updated_at'  => Yii::t('site', 'Updated'),
        ];
    }

    public function afterFind() {
        switch($this->type){
            /*case SettingType::TRANSLATED_STRING_TYPE:
                if (is_string($this->value)) {
                    try {
                        $this->value = json_decode($tmp = $this->value, true);
                        $this->value = is_null($this->value) ? $tmp : $this->value;
                    } catch (\Exception $e) {
                        $this->value = $tmp;
                    }
                }
                break;*/
            case SettingType::INTEGER_TYPE:
                $this->value = (string)intval($this->value);
                break;
            case SettingType::FLOAT_TYPE:
                $this->value = (string)floatval($this->value);
                break;
            case SettingType::BOOLEAN_TYPE:
                $this->value = (string)filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
                break;
            default;
                $this->value = (string)$this->value;
        }
        parent::afterFind();
    }

    public function validate($attributeNames = null, $clearErrors = true) {
        $this->afterFind();
        if($this->system == self::SYSTEM_PROTECTED){
            return $this->value = $this->getOldAttribute('value');
        }
        if($this->type == SettingType::BOOLEAN_TYPE){
            $this->value = filter_var($this->value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        }
        /*if ($this->type == SettingType::TRANSLATED_STRING_TYPE) {
            try {
                $old = json_decode($tmp = $this->getOldAttribute('value'), true);
                $old = is_null($old) ? $tmp : $old;
            } catch (\Exception $e) {
                $old = [];
            }
            if (is_null($old)) {
                $old = array_fill_keys(LanguageSelector::codes(), $tmp);
            }
            $key = LanguageSelector::codes()[0];
            $this->value = json_encode(ArrayHelper::merge($old, is_array($this->value) ? $this->value : [$key => $this->value]), JSON_UNESCAPED_UNICODE);
        }*/
        return parent::validate($attributeNames, $clearErrors);
    }

    public function setSetting($section, $key, $value, $type = null, $system = false): bool {
        $model = static::findOne(['section' => $section, 'key' => $key]);

        if(empty($model)){
            $model = new static();
        }
        if($model->system == self::SYSTEM_PROTECTED && $system != self::SYSTEM_PROTECTED){
            return false;
        }
        $model->section = $section;
        $model->key = $key;
        $model->value = $value;
        if($system){
            $model->system = self::SYSTEM_ON;
            if($system > self::SYSTEM_PROTECTED){
                $model->system = self::SYSTEM_PROTECTED;
            }
        }

        if($type !== null && ArrayHelper::keyExists($type, SettingType::getConstantsByValue())){
            $model->type = $type;
        }else{
            $model->type = gettype($value);
        }
        return $model->save();
    }

    /**
     * @param null $section
     * @param null $key
     * @param bool $all
     *
     * @return float|int|mixed|string|null
     */
    public function getSetting($section = null, $key = null, $all = false) {
        if($section && $key){
            $model = static::findOne(['section' => $section, 'key' => $key]);
            if(empty($model)){
                return null;
            }
        }else{
            $model = $this;
        }
        switch($model->type){
            /*case SettingType::TRANSLATED_STRING_TYPE:
                $languages = LanguageSelector::languages();
                if (is_string($this->value)) {
                    try {
                        $value = json_decode($tmp = $this->value, true);
                        $value = is_null($value) ? $tmp : $value;
                    } catch (\Exception $e) {
                        $value = $tmp;
                    }
                } else {
                    $value = $model->value;
                }
                if ($all) {
                    $result = [];
                    if (is_array($value)) {
                        foreach ($value as $k => $v) {
                            $result[] = ($languages[$k] ?? $k) . ': ' . $v;
                        }
                    } else {
                        return $value;
                    }
                    return implode($all, $result);
                } else {
                    $lang = substr(Yii::$app->language, 0, 2);
                    return isset($value[$lang]) ? $value[$lang] : null;
                }*/
            case SettingType::INTEGER_TYPE:
                return intval($model->value);
            case SettingType::FLOAT_TYPE:
                return floatval($model->value);
            case SettingType::BOOLEAN_TYPE:
                return filter_var($model->value, FILTER_VALIDATE_BOOLEAN);
            default;
                return (string)$this->value;
        }
    }

    public function incSetting($section, $key, $inc) {
        Yii::$app->db->createCommand('UPDATE ' . SettingModel::tableName() . ' SET value=value+:inc WHERE `section`=:section AND `key`=:key')
            ->bindValue(':inc', $inc)
            ->bindValue(':section', $section)
            ->bindValue(':key', $key)
            ->execute();
    }

    public function getStatusesList() {
        return [
            self::STATUS_INACTIVE => Yii::t('site', 'Inactive'),
            self::STATUS_ACTIVE   => Yii::t('site', 'Active'),
        ];
    }

    public function getSystemsList() {
        return [
            self::SYSTEM_OFF       => Yii::t('site', 'Regular'),
            self::SYSTEM_ON        => Yii::t('site', 'System'),
            self::SYSTEM_PROTECTED => Yii::t('site', 'Protected'),
        ];
    }

}