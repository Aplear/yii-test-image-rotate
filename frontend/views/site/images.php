<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">

        <?php if(\Yii::$app->session->getFlash('message') !== null): ?>
            <div id="message">
                <div class="alert alert-<?= strtolower(\Yii::$app->session->getFlash('message'))?>">
                    <strong><?= \Yii::$app->session->getFlash('message')?>!</strong>
                </div>

            </div>
        <?php endif; ?>

        <!-- start image upload form -->
        <div class="image-form form-inline">

            <?php $form = ActiveForm::begin([
                'options' => ['enctype' => 'multipart/form-data'],
                'method' => 'post',
                'action' => ['image/create'],
            ]); ?>

            <?= $form->field($model, 'image')->fileInput()->label(false) ?>

            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div><!-- end mage upload form -->

    </div>

    <!-- start image container-->
    <div class="container">
        <div class="row">

        <?php foreach ($images as $image): ?>

            <div class="col-sm-6 col-md-4">
                <a href="#" class="thumbnail">
                    <img id="img_file_uploaded_<?=$image->id?>" src="<?=$image->filePath?>" alt="<?=$image->filePath?>">
                </a>
                <p>
                    <a href="#" class="btn btn-primary" role="button" class="image_rotate" onclick="ImagesRotate( <?=$image->id?>); return false;">Rotate</a>
                    <a href="/frontend/web/image/delete?id=<?=$image->id?>" class="btn btn-danger" role="button">Delete</a>
                </p>
            </div>

        <?php endforeach; ?>
        </div>
    </div><!-- end image container-->




</div>

<script>
    //Update image after rotation
    var ImagesRotate = function (id) {
            var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');

            url = '/frontend/web/image/image-rotate?id='+id;
            xhr.open("GET", url, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Accept', 'text/html');
            xhr.onreadystatechange = function (state) {

                if (xhr.readyState === 4) {
                    var obj = JSON.parse(xhr.response);
                    var img = document.getElementById("img_file_uploaded_"+id);
                    img.src=obj.image_path;
                }
            };
            xhr.send();
        };
</script>