<?php

namespace saghar\category\controllers;

use saghar\category\Category;
use yii\data\ActiveDataProvider;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;

/**
 * Class RestApiController
 * @package rest\versions\v1\controllers
 */
class RestApiController extends ActiveController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // re-add authentication filter
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['options', 'index', 'view'],
        ];
        return $behaviors;
    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        $this->modelClass = Category::getInstance()->modelClass;
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    /**
     * @return mixed
     */
    public function prepareDataProvider()
    {
        $searchModelCalss = Category::getInstance()->searchModelClass;
        if ($searchModelCalss) {
            $model = new $searchModelCalss();
            $params = \Yii::$app->request->queryParams;
            $params['search']['depth'] = 0;
            $dataProvider = $searchModel->search($params, 'search');
        } else {
            $modelClass = Category::getInstance()->modelClass;
            $query = $modelClass::find()->where(['status' => $modelClass::STATUS_ACTIVE]);
            $dataProvider = new ActiveDataProvider([
                'query' => $query
            ]);
        }
        return $dataProvider;
    }

    /**
     * @inheritdoc
     *
     * @param User $model
     */
    public function checkAccess($action, $model = null, $params = [])
    {

    }
}