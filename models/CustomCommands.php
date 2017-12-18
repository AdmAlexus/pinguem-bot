<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "custom_commands".
 *
 * @property int $id
 * @property string $command
 */
class CustomCommands extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'custom_commands';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['command'], 'required'],
            [['command'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'command' => 'Command',
        ];
    }
}
