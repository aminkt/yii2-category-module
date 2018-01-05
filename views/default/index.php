<?php
/** @var \yii\web\View $this */
/** @var \saghar\category\models\Categories[] $categories */
/** @var \saghar\category\models\Categories $model */

$this->title = "مدیریت دسته ها"

?>

<div class="categoryManager-default-index">
    <h1><?= $this->title ?></h1>

    <div class="row">
        <div class="col-md-6">
            <?= yii\helpers\Html::a('ایجاد دسته جدید', ['/category/default/index'], ['class' => 'btn btn-primary']) ?>

            <?= \aminkt\widgets\tree\TreeView::widget([
                'data' => \saghar\category\models\Categories::getCategoriesAsArray(),
                'remove' => ['/category/default/delete'],
                'edit' => ['/category/default/index'],
            ]);
            ?>
        </div>
        <div class="col-md-6">
            <?=
            $this->render('_form', [
                'model' => $model
            ]);
            ?>
        </div>
    </div>
</div>

