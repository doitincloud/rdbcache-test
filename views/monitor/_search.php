<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\RdbcacheMonitorSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rdbcache-monitor-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'thread_id') ?>

    <?= $form->field($model, 'duration') ?>

    <?= $form->field($model, 'main_duration') ?>

    <?php // echo $form->field($model, 'client_duration') ?>

    <?php // echo $form->field($model, 'started_at') ?>

    <?php // echo $form->field($model, 'ended_at') ?>

    <?php // echo $form->field($model, 'trace_id') ?>

    <?php // echo $form->field($model, 'built_info') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
