<?php

use yii\db\Migration;

/**
 * Class m180104_145240_add_parent_relation
 */
class m180104_145240_add_parent_relation extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addForeignKey(
            'categories_fk_categories_parentId',
            '{{%categories}}',
            'parentId',
            '{{%categories}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('categories_fk_categories_parentId', '{{%categories}}');

        return false;
    }
}
