<?php

use yii\db\Migration;

/**
 * Class m170815_090417_commands_history
 */
class m170815_090417_commands_history extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%commands_history}}', [
            'id' => $this->primaryKey(),
            'from_id' => $this->integer()->notNull(),
            'command' => $this->text()->notNull(),
            'text' => $this->text(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%commands_history}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170815_090417_commands_history cannot be reverted.\n";

        return false;
    }
    */
}
