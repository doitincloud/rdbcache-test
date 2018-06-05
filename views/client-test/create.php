<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\RdbcacheClientTest */

$this->title = 'Create Rdbcache Client Test';
$this->params['breadcrumbs'][] = ['label' => 'Rdbcache Client Tests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rdbcache-client-test-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
