<?php
/** @var \saghar\category\models\Category $model */
?>

<?php $form = \yii\widgets\ActiveForm::begin([
    'method' => 'post'
]); ?>

<?= $form->field($model, 'name')->textInput() ?>
<?= $form->field($model, 'section')->textInput() ?>
<?= $form->field($model, 'tags')->widget(\aminkt\widgets\inputTag\InputTag::className(), [
    'options' => [
        'maxlength' => true,
        'class' => 'form-control maxlength-handler'
    ]
]) ?>
<?= $form->field($model, 'description')->textarea() ?>
<?php
if ($model->id) {
    $categories = \saghar\category\models\Category::find()
        ->where(['!=', 'status', \saghar\category\models\Category::STATUS_REMOVED])
        ->andWhere(['!=', 'id', $model->id])
        ->all();
} else {
    $categories = \saghar\category\models\Category::find()
        ->where(['!=', 'status', \saghar\category\models\Category::STATUS_REMOVED])
        ->all();
}
$categories = \yii\helpers\ArrayHelper::map($categories, 'id', 'name', 'parentName');
echo \kartik\select2\Select2::widget([
    'model' => $model,
    'attribute' => 'parentId',
    'data' => $categories,
    'options' => ['placeholder' => 'دسته را انتخاب کنید ...'],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);
?>

<?= \yii\helpers\Html::submitButton("Submit", [
    'class' => 'btn btn-primary'
]) ?>

<?php \yii\widgets\ActiveForm::end(); ?>