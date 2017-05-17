<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Image;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\imagine\Image as Imagine;

/**
 * ImageController implements the CRUD actions for Image model.
 */
class ImageController extends Controller
{

    /**
     * server image path
     * @return bool|string
     */
    public static function getPath()
    {
        return Yii::getAlias('@app/web');
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],

            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['GET'],
                ],
            ],
        ];
    }

    /**
     * Creates a new Image model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Image();

        if (Yii::$app->request->isPost) {
            $model->image = UploadedFile::getInstance($model, 'image');
            //set model data
            $model->filePath = '/images/store/' . $model->image->baseName . '.' . $model->image->extension;
            $model->user_id = Yii::$app->user->identity->id;
            //go to save model image first and than upload file
            if ($model->save() && $model->upload()) {
                return $this->redirect(['site/images', 'id' => $model->id]);
            } else {
                return $this->render('site/images', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('site/images', [
                'model' => $model,
            ]);
        }

    }

    /**
     * Deletes an existing Image model and image file.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $message = 'Danger';
        $model = $this->findModel($id);
        //check user currect
        if($model->user_id === Yii::$app->user->id)
        {
            $user_imgage = self::getPath.$model->filePath;
            //file delete
            if(is_file($user_imgage))
            {
                unlink($user_imgage);
            }
            //delete record by id
            $this->findModel($id)->delete();
            $message = 'Success';

        }

        return $this->redirect(['site/images',
            \Yii::$app->session->setFlash('message', $message),
        ]);


    }

    /**
     * Start Rotate image and save
     * @param $id
     * @return array
     */
    public function actionImageRotate($id)
    {
        //if ajax came
        if (Yii::$app->request->isAjax) {

            $message = 'Danger';
            $model = $this->findModel($id);
            if ($model->user_id === Yii::$app->user->id) {

                $file_path_before_changes = $model->filePath;
                preg_match("#/images/store/(.+)#is", $model->filePath, $file_name);

                //save new path
                $model->filePath = "/images/store/".rand(1, 1000).mb_substr($file_name[1], 4);
                $model->save();

                //Rotate start and save on server
                Imagine::frame( \frontend\controllers\ImageController::getPath().$file_path_before_changes, 5, 'fff', 0)
                    ->rotate(90)
                    ->save(\frontend\controllers\ImageController::getPath().$model->filePath , ['jpeg_quality' => 100]);

                //delete file after rotation
                unlink(\frontend\controllers\ImageController::getPath().$file_path_before_changes);
            }
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'image_path' => $model->filePath
            ];
        }
    }

    /**
     * Finds the Image model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Image the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Image::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
