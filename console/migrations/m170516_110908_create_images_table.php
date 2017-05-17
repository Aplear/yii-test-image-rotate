<?php

use yii\db\Migration;

/**
 * Handles the creation of table `images`.
 */
class m170516_110908_create_images_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%image}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->defaultValue(null),
            'filePath' => $this->string(400)->notNull()->defaultValue(null),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-post-user_id',
            'image',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-image-user_id',
            'image',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('images');
    }
}
