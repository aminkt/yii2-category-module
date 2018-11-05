<?php

namespace saghar\category\mobgo\models;

use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * Category search model for sarching in saghar\category\models\Category class.
 *
 * @see Category
 *
 * @author Amin Keshavarz <ak_1596@yahoo.com>
 */
class CategorySearch extends Category
{
    public $parentName;

    /**
     * @inheritdoc
     *
     * @author Amin Keshavarz <ak_1596@yahoo.com>
     */
   public function rules()
   {
       return [
           [['section', 'name', 'description', 'parentName'], 'string', 'max' => 255],
           [['status', 'parentId', 'depth', 'id'], 'integer'],
       ];
   }

    /**
     * Search in model.
     *
     * @param array $quries
     *
     * @return ActiveDataProvider
     *
     * @author Amin Keshavarz <ak_1596@yahoo.com>
     */
   public function search($params, $formName = null){
       throw new \RuntimeException("Not implemented yet.");
   }
}
