<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "stop_lemmas".
 *
 * @property int $id
 * @property string $lemma
 */
class StopLemmas extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stop_lemmas';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lemma'], 'required'],
            [['lemma'], 'string', 'max' => 255],
            [['lemma'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lemma' => 'Lemma',
        ];
    }
}
