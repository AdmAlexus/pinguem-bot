<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "hints".
 *
 * @property int $id
 * @property int $quest_id
 * @property int $hint_id
 * @property string $description
 */
class Hints extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hints';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['quest_id', 'hint_id', 'description'], 'required'],
            [['quest_id', 'hint_id'], 'integer'],
            [['description'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'quest_id' => 'Quest ID',
            'hint_id' => 'Hint ID',
            'description' => 'Description',
        ];
    }
}
