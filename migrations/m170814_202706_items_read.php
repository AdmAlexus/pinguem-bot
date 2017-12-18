<?php

use yii\db\Migration;

/**
 * Class m170814_202706_sasvakhero_read
 */
class m170814_202706_items_read extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%items_read}}', [
            'group' => $this->text()->notNull(),
            'from_id' => $this->integer()->notNull(),
            'item_id' => $this->integer(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%items_read}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170814_202706_sasvakhero_read cannot be reverted.\n";

        return false;
    }
    */
}
