<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\RdbcacheKvPair */

$this->title = 'Update Rdbcache Kv Pair: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Rdbcache Kv Pairs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id, 'type' => $model->type]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="rdbcache-kv-pair-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
