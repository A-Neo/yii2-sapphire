<?php

namespace app\components;

use yii\db\ActiveRecord;

class RecordCopier
{
    /**
     * Копирует запись и изменяет указанные атрибуты.
     *
     * @param ActiveRecord $record Запись для копирования.
     * @param array $attributes Атрибуты для изменения.
     * @return ActiveRecord Копия записи с измененными атрибутами.
     * @throws \yii\base\Exception Если сохранение не удалось.
     */
    public function copyWithAttributes(ActiveRecord $record, array $attributes): ActiveRecord
    {
        // Создаем новую запись того же класса, что и исходная запись
        $className = get_class($record);
        $newRecord = new $className();

        // Копируем атрибуты исходной записи
        $newRecord->attributes = $record->attributes;

        // Изменяем указанные атрибуты
        foreach ($attributes as $name => $value) {
            if ($newRecord->hasAttribute($name)) {
                $newRecord->$name = $value;
            }
        }

        // Сохраняем новую запись
        if (!$newRecord->save()) {
            throw new \yii\base\Exception('Не удалось сохранить новую запись');
        }

        return $newRecord;
    }
}
