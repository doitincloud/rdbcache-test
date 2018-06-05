<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\RdbcacheStopwatch */

$this->title = 'Create Rdbcache Stopwatch';
$this->params['breadcrumbs'][] = ['label' => 'Rdbcache Stopwatches', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rdbcache-stopwatch-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
