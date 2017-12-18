<?php

use yii\db\Migration;

/**
 * Class m170826_171858_words_freq
 */
class m170826_171858_words_frequency extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%words_frequency}}', [
            'id' => $this->primaryKey(),
            'word' => $this->text()->notNull(),
            'frequency' => $this->integer()->notNull(),
            'lemma' => $this->string(255)->notNull(),
            'validated' => $this->boolean()->defaultValue(true),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%words_frequency}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170826_171858_words_freq cannot be reverted.\n";

        return false;
    }
    */
}
