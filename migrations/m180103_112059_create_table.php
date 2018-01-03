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
        $this->createTable('{{%categories}}', [
            'id' => $this->primaryKey(),
            'section' => $this->string()->notNull()->defaultValue('main'),
            'name' => $this->string()->notNull(),
            'tags' => $this->string(),
            'description' => $this->string(),
            'parentId' => $this->integer(),
            'depth' => $this->integer()->notNull()->defaultValue(0),
            'createAt' => $this->integer(20)->notNull(),
            'updateAt' => $this->integer(20)->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%categories}}');
    }

}
