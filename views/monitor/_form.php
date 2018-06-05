<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\RdbcacheMonitor */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rdbcache-monitor-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'thread_id')->textInput() ?>

    <?= $form->field($model, 'duration')->textInput() ?>

    <?= $form->field($model, 'main_duration')->textInput() ?>

    <?= $form->field($model, 'client_duration')->textInput() ?>

    <?= $form->field($model, 'started_at')->textInput() ?>

    <?= $form->field($model, 'ended_at')->textInput() ?>

    <?= $form->field($model, 'trace_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'built_info')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
