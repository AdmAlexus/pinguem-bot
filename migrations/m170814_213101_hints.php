<?php

use yii\db\Migration;

/**
 * Class m170814_213101_hints
 */
class m170814_213101_hints extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%hints}}', [
            'id' => $this->primaryKey(),
            'quest_id' => $this->integer()->notNull(),
            'hint_id' => $this->integer()->notNull(),
            'description' => $this->text()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%hints}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170814_213101_hints cannot be reverted.\n";

        return false;
    }
    */
}
