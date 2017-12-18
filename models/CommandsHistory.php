<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "commands_history".
 *
 * @property int $id
 * @property int $from_id
 * @property string $command
 * @property string $text
 */
class CommandsHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'commands_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from_id', 'command'], 'required'],
            [['from_id'], 'integer'],
            [['command', 'text'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'from_id' => 'From ID',
            'command' => 'Command',
            'text' => 'Text',
        ];
    }
}
