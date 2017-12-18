<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quests".
 *
 * @property int $id
 * @property string $author
 * @property string $comment
 * @property string $description
 * @property string $answer
 */
class Quests extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'quests';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['author', 'description', 'answer'], 'required'],
            [['author', 'comment', 'description', 'answer'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author' => 'Author',
            'comment' => 'Comment',
            'description' => 'Description',
            'answer' => 'Answer',
        ];
    }
}
