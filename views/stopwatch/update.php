<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\RdbcacheStopwatch */

$this->title = 'Update Rdbcache Stopwatch: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Rdbcache Stopwatches', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="rdbcache-stopwatch-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
