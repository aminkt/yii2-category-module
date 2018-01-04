<?php
/** @var \yii\web\View $this */
/** @var \saghar\category\models\Categories[] $categories */
/** @var \saghar\category\models\Categories $model */

?>

<p>
    <?=
    $this->render('_form', [
        'categories' => $categories,
        'model' => $model
    ]);
    ?>
</p>
