<?php

namespace saghar\category\interfaces;

/**
 * Interface CategoryInterfaces
 * You should implement thi interface in your own model.
 *
 * @package saghar\interfaces
 */
interface CategoryInterfaces
{
    /**
     * Return id of current category
     *
     * @return mixed
     */
    public function getId();

    /**
     * Return parent category of current category
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent();

    /**
     * Return all children of current categoy.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategories();

    /**
     * Get one level down children of current category.
     *
     * @return $this
     */
    public function getChildren();
}