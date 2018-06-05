<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\RdbcacheMonitorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rdbcache Monitors';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rdbcache-monitor-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Rdbcache Monitor', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'thread_id',
            'duration',
            'main_duration',
            //'client_duration',
            //'started_at',
            //'ended_at',
            //'trace_id',
            //'built_info',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
