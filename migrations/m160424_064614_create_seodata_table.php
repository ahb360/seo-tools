<?php

use yii\db\Schema;
use yii\db\Migration;

class m160424_064614_create_seodata_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('seo_data', [
            'id' => 'pk',
            'ownerClassName' => 'string NOT NULL',
            'ownerId' => 'integer NOT NULL',
            'title' => 'string',
            'metaKeywords' => 'string',
            'metaDescription' => 'string',
            'url' => 'string'
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('seo_data');
    }
}
