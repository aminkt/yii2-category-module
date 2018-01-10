<?php

namespace saghar\category\controllers;

use aminkt\widgets\alert\Alert;
use saghar\category\models\Category;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `categoryManager` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     *
     * @param null $id
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex($id = null)
    {
        if ($id) {
            $model = Category::findOne($id);
            if (!$model) {
                throw new NotFoundHttpException("دسته مورر نظر یافت نشد");
            }
        } else {
            $model = new Category();
        }

        if (\Yii::$app->getRequest()->isPost) {
            try {
                if ($model->isNewRecord) {
                    $model = Category::create(\Yii::$app->getRequest()->post('Category'));
                } else {
                    $model = Category::edit($id, \Yii::$app->getRequest()->post('Category'));
                }
            } catch (NotFoundHttpException $e) {
                Alert::error('خطا در انجام عملیات', 'دسته مورد نظر ایجاد نشد');
            } catch (\RuntimeException $e) {
                \Yii::error($e->getMessage());
                Alert::error("خطا در ذخیره اطلاعات", "دسته مورد نظر ذخیره نشد");
            }
            return $this->redirect(['index']);
        }
        return $this->render('index', [
            'model' => $model
        ]);
    }

    /**
     * Delete category
     *
     * @param $id
     *
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $category = Category::findOne($id);
        if ($category) {
            try {
                $category->setStatus(Category::STATUS_REMOVED);
                Alert::success('عملیات با موفقیت انجام شد', 'دسته مورد نظر حذف شد');
                $this->redirect(['index']);
            } catch (\Exception $e) {
                \Yii::error($e->getMessage());
                Alert::error('خطا در انجام عملیات', 'دسته مورد نظر حذف نشد');
            }
        } else {
            throw new NotFoundHttpException("دسته پیدا نشد");
        }
    }
}
