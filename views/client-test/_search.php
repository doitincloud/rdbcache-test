<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\RdbcacheClientTestSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rdbcache-client-test-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'trace_id') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'passed') ?>

    <?= $form->field($model, 'verify_passed') ?>

    <?php // echo $form->field($model, 'duration') ?>

    <?php // echo $form->field($model, 'process_duration') ?>

    <?php // echo $form->field($model, 'route') ?>

    <?php // echo $form->field($model, 'url') ?>

    <?php // echo $form->field($model, 'data') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
