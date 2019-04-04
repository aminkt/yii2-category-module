<?php

namespace saghar\category\models;

use yii\data\ActiveDataProvider;

/**
 * Category search model for sarching in saghar\category\models\Category class.
 *
 * @see    Category
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
            [['status', 'parent_id', 'depth', 'id'], 'integer'],
        ];
    }

    /**
     * Search in model.
     *
     * @param array       $params
     * @param null|string $formName
     *
     * @return ActiveDataProvider
     *
     * @throws \yii\base\InvalidConfigException
     * @author Amin Keshavarz <ak_1596@yahoo.com>
     */
    public function search($params, $formName = null)
    {
        if ($formName === null) {
            $formName = $this->formName();
        }

        $query = Category::find()->alias('cat');
        $query->leftJoin("{{%categories}} AS c", 'c.id = cat.parent_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->where(['cat.status' => self::STATUS_ACTIVE]);

        if (!($this->load($params, $formName) && $this->validate())) {
            return $dataProvider;
        }


        $query->andFilterWhere(['cat.status' => $this->status])
            ->andFilterWhere(['cat.parent_id' => $this->parent_id])
            ->andFilterWhere(['cat.depth' => $this->depth])
            ->andFilterWhere(['cat.id' => $this->id]);

        $query->andFilterWhere(['like', 'cat.name', $this->name])
            ->andFilterWhere(['like', 'cat.section', $this->section])
            ->andFilterWhere(['like', 'c.name', $this->parentName]);


        return $dataProvider;
    }
}
