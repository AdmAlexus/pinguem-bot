<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "words_frequency".
 *
 * @property int $id
 * @property string $word
 * @property int $frequency
 * @property string $lemma
 * @property int $validated
 */
class WordsFrequency extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'words_frequency';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['word', 'frequency', 'lemma'], 'required'],
            [['word'], 'string'],
            [['frequency', 'validated'], 'integer'],
            [['lemma'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'word' => 'Word',
            'frequency' => 'Frequency',
            'lemma' => 'Lemma',
            'validated' => 'Validated',
        ];
    }
}
