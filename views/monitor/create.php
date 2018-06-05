<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\RdbcacheMonitor */

$this->title = 'Create Rdbcache Monitor';
$this->params['breadcrumbs'][] = ['label' => 'Rdbcache Monitors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rdbcache-monitor-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
