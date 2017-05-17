<?php

namespace frontend\models;

use frontend\controllers\ImageController;
use Yii;
use common\models\User;

/**
 * This is the model class for table "image".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $filePath
 *
 * @property User $user
 */
class Image extends \yii\db\ActiveRecord
{

    /**
     * @var $image
     */
    public $image;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'image';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
            [['filePath'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => false, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'filePath' => Yii::t('app', 'File Path'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }


    /**
     * upload image
     *
     * @return bool
     */
    public function upload()
    {
        if ($this->validate()) {
            $this->image->saveAs(ImageController::getPath().$this->filePath);
            return true;
        } else {
            return false;
        }
    }
}
