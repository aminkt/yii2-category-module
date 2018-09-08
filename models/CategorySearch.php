<?php

namespace saghar\category\models;

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
           [['section', 'name', 'tags', 'description', 'parentName'], 'string', 'max' => 255],
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
       if($formName === null){
           $formName = $this->formName();
       }

       $query = Category::find()->alias('cat');
       $query->leftJoin("{{%categories}} AS c", 'c.id = cat.parentId');

       $dataProvider = new ActiveDataProvider([
           'query' => $query,
       ]);

       $query->where(['cat.status' => self::STATUS_ACTIVE]);

       if (!($this->load($params, $formName) && $this->validate())) {
           return $dataProvider;
       }


       $query->andFilterWhere(['cat.status' => $this->status])
           ->andFilterWhere(['cat.parentId' => $this->parentId])
           ->andFilterWhere(['cat.depth' => $this->depth])
           ->andFilterWhere(['cat.id' => $this->id]);

       $query->andFilterWhere(['like', 'cat.name', $this->name])
           ->andFilterWhere(['like', 'cat.section', $this->section])
           ->andFilterWhere(['like', 'c.name', $this->parentName])
           ->andFilterWhere(['like', 'cat.tags', $this->tags]);


       return $dataProvider;
   }
}
