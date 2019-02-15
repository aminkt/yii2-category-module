<?php

use yii\db\Migration;

/**
 * Class m180103_112059_create_table
 */
class m180103_112059_create_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

        $this->createTable('{{%categories}}', [
            'id' => $this->primaryKey(),
            'section' => $this->string()->notNull()->defaultValue('main'),
            'name' => $this->string()->notNull(),
            'description' => $this->string(),
            'status' => $this->smallInteger(1)->defaultValue(1),
            'parent_id' => $this->integer(),
            'depth' => $this->integer()->notNull()->defaultValue(0),
            'update_at' => $this->dateTime(),
            'create_at' => $this->dateTime(),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%categories}}');
    }

}
