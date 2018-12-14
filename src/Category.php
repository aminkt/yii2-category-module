<?php

namespace saghar\category;

/**
 * categoryManager module definition class
 */
class Category extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'saghar\category\controllers';

    /** @var string $modelClass Class name of category model */
    public $modelClass = \saghar\category\models\Category::class;

    /** @var string $searchModelClass Class name of category search model */
    public $searchModelClass = saghar\category\models\CategorySearch::class;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @return self
     *
     * @author Amin Keshavarz <amin@keshavarz.pro>
     */
    public static function getInstance()
    {
        if (parent::getInstance())
            return parent::getInstance();

        return \Yii::$app->getModule('category');
    }
}
