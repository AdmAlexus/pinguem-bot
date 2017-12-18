<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sasvakhero".
 *
 * @property int $id
 * @property string $phrase
 */
class Sasvakhero extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sasvakhero';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phrase'], 'required'],
            [['phrase'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phrase' => 'Phrase',
        ];
    }
}
