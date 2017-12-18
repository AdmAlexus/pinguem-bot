<?php

use yii\db\Migration;

/**
 * Class m170814_201842_sasvakhero
 */
class m170814_201842_sasvakhero extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%sasvakhero}}', [
            'id' => $this->primaryKey(),
            'phrase' => $this->text()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%sasvakhero}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170814_201842_sasvakhero cannot be reverted.\n";

        return false;
    }
    */
}
