<?php

use yii\db\Migration;

/**
 * Class m170826_171905_stop_words
 */
class m170826_171905_stop_lemmas extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%stop_lemmas}}', [
            'id' => $this->primaryKey(),
            'lemma' => $this->string(255)->notNull()->unique(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%stop_lemmas}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170826_171905_stop_words cannot be reverted.\n";

        return false;
    }
    */
}
