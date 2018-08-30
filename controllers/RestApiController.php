<?php
namespace saghar\category\controllers;

use aminkt\exceptions\yii2\InputValidationException;
use aminkt\exceptions\yii2\InvalidInputException;
use saghar\category\models\Category;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class RestApiController
 * @package rest\versions\v1\controllers
 */
class RestApiController extends ActiveController
{
    public $modelClass = Category::class;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // re-add authentication filter
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['options'],
        ];
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create'], $actions['update']);
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    /**
     * @return mixed
     */
    public function prepareDataProvider()
    {
        $searchModel = new \saghar\category\models\CategorySearch();
        $params = \Yii::$app->request->queryParams;
        $params['search']['depth'] = 0;
        return $searchModel->search($params, 'search');
    }


    /**
     * Create new category.
     *
     * @internal  array $data <code>
     *      [
     *          'section' => $section,
     *          'name' => $name,
     *          'tags' => $tags,
     *          'description' => $description,
     *          'status' => $status,
     *          'parentId' => $parentId,
     *          'depth' => $depth,
     *      ]
     * </code>
     *
     * @return Category
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        $request = \Yii::$app->getRequest()->getBodyParams();
        $category = new Category();
        $category->load($request, '');
        if($category->save()){
            return $category;
        }else{
            throw new InputValidationException("Please check your inputs.", $category->getErrors());
        }
    }

    /**
     * Edit category.
     *
     * @internal  array $data <code>
     *      [
     *          'section' => $section,
     *          'name' => $name,
     *          'tags' => $tags,
     *          'description' => $description,
     *          'status' => $status,
     *          'parentId' => $parentId,
     *          'depth' => $depth,
     *      ]
     * </code>
     *
     * @return Category
     *
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $request = \Yii::$app->getRequest()->getBodyParams();
        $category = Category::findOne($id);
        if(!$category){
            throw new NotFoundHttpException("Category not found");
        }
        $category->load($request, '');
        if($category->save()){
            return $category;
        }else{
            \Yii::$app->getResponse()->setStatusCode(400);
            return $category->errors;
        }
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