<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "items_read".
 *
 * @property string $group
 * @property int $from_id
 * @property int $item_id
 */
class ItemsRead extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'items_read';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group', 'from_id'], 'required'],
            [['group'], 'string'],
            [['from_id', 'item_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'group' => 'Group',
            'from_id' => 'From ID',
            'item_id' => 'Item ID',
        ];
    }
}
