<?php

use yii\db\Migration;

/**
 * Class m170814_212854_quests
 */
class m170814_212854_quests extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%quests}}', [
            'id' => $this->primaryKey(),
            'author' => $this->text()->notNull(),
            'comment' => $this->text(),
            'description' => $this->text()->notNull(),
            'answer' => $this->text()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%quests}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170814_212854_quests cannot be reverted.\n";

        return false;
    }
    */
}
