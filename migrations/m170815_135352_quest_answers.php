<?php

use yii\db\Migration;

/**
 * Class m170815_135352_quest_answers
 */
class m170815_135352_quest_answers extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%answers}}', [
            'quest_id' => $this->integer()->notNull(),
            'text' => $this->text()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%answers}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170815_135352_quest_answers cannot be reverted.\n";

        return false;
    }
    */
}
