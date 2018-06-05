<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\RdbcacheClientTestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rdbcache Client Tests';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rdbcache-client-test-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Rdbcache Client Test', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'trace_id',
            'status',
            'passed',
            'verify_passed',
            //'duration',
            //'process_duration',
            //'route',
            //'url:url',
            //'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
