<?php

use yii\db\Migration;

/**
 * Class m170822_133341_history
 */
class m170822_133341_history extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%history}}', [
            'message_id' => $this->bigPrimaryKey(),
            'chat_id' => $this->bigInteger()->notNull(),
            'chat_title' => $this->text()->notNull(),
            'from_id' => $this->bigInteger()->notNull(),
            'from_first_name' => $this->text()->notNull(),
            'from_last_name' => $this->text(),
            'date' => $this->dateTime()->notNull(),
            'type' => $this->text()->notNull(),
            'text' => $this->text(),
            'status' => $this->text()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%history}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170822_133341_history cannot be reverted.\n";

        return false;
    }
    */
}
