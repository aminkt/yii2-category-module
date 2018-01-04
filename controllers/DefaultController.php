<?php

namespace saghar\category\controllers;

use aminkt\widgets\alert\Alert;
use saghar\category\models\Categories;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `categoryManager` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @param null $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex($id = null)
    {
        if ($id) {
            $model = Categories::findOne($id);
            if (!$model) {
                throw new NotFoundHttpException("دسته مورر نظر یافت نشد");
            }
        }else{
            $model = new Categories();
        }

        if (\Yii::$app->getRequest()->isPost) {
            try {
                if ($model->isNewRecord) {
                    $model = Categories::create(\Yii::$app->getRequest()->post('Categories'));
                } else {
                    $model = Categories::edit($id, \Yii::$app->getRequest()->post('Categories'));
                }
            } catch (NotFoundHttpException $e) {
                Alert::error('خطا در انجام عملیات', 'دسته مورد نظر ایجاد نشد');
            } catch (\RuntimeException $e) {
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
        $category = Categories::findOne($id);
        if ($category) {
            try {
                $category->setStatus(Categories::STATUS_REMOVED);
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
