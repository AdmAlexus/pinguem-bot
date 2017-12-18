<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "history".
 *
 * @property int $message_id
 * @property int $chat_id
 * @property string $chat_title
 * @property int $from_id
 * @property string $from_first_name
 * @property string $from_last_name
 * @property string $date
 * @property string $text
 * @property string $status
 */
class History extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['chat_id', 'chat_title', 'from_id', 'from_first_name', 'date', 'text', 'status'], 'required'],
            [['chat_id', 'from_id'], 'integer'],
            [['chat_title', 'from_first_name', 'from_last_name', 'text', 'status'], 'string'],
            [['date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'message_id' => 'Message ID',
            'chat_id' => 'Chat ID',
            'chat_title' => 'Chat Title',
            'from_id' => 'From ID',
            'from_first_name' => 'From First Name',
            'from_last_name' => 'From Last Name',
            'date' => 'Date',
            'text' => 'Text',
            'status' => 'Status',
        ];
    }

    public static function getIntegerFromHexStringLittleEndian($string) {
        $data = '';
        for ($i = 0; $i < strlen($string); $i += 2) {
            $data .= chr(hexdec(substr($string, $i, 2)));
        }
        return unpack('P', $data)[1];
    }

}
