<?php

namespace app\controllers;

use Yii;
use app\models\RdbcacheKvPair;
use app\models\RdbcacheKvPairSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * KvPairController implements the CRUD actions for RdbcacheKvPair model.
 */
class KvPairController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all RdbcacheKvPair models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RdbcacheKvPairSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RdbcacheKvPair model.
     * @param string $id
     * @param string $type
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $type)
    {
        return $this->render('view', [
            'model' => $this->findModel($id, $type),
        ]);
    }

    /**
     * Creates a new RdbcacheKvPair model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RdbcacheKvPair();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id, 'type' => $model->type]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing RdbcacheKvPair model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @param string $type
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id, $type)
    {
        $model = $this->findModel($id, $type);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id, 'type' => $model->type]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing RdbcacheKvPair model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @param string $type
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id, $type)
    {
        $this->findModel($id, $type)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the RdbcacheKvPair model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @param string $type
     * @return RdbcacheKvPair the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $type)
    {
        if (($model = RdbcacheKvPair::findOne(['id' => $id, 'type' => $type])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
