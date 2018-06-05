<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\RdbcacheKvPair */

$this->title = 'Create Rdbcache Kv Pair';
$this->params['breadcrumbs'][] = ['label' => 'Rdbcache Kv Pairs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rdbcache-kv-pair-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
