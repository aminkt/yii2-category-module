<?php
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
