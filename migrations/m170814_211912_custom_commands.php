<?php

use yii\db\Migration;

/**
 * Class m170814_211912_custom_commands
 */
class m170814_211912_custom_commands extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%custom_commands}}', [
            'id' => $this->primaryKey(),
            'command' => $this->text()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%custom_commands}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170814_211912_custom_commands cannot be reverted.\n";

        return false;
    }
    */
}
