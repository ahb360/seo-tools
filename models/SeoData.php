<?php

namespace seo\models;

use Yii;

class SeoData extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'seo_data';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ownerClassName', 'ownerId'], 'required'],
            [['ownerId'], 'integer'],
            [['ownerClassName', 'title', 'metaKeywords', 'metaDescription', 'url'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ownerClassName' => 'Owner Class Name',
            'ownerId' => 'Owner ID',
            'title' => 'Title',
            'metaKeywords' => 'Meta Keywords',
            'metaDescription' => 'Meta Description',
            'url' => 'Url',
        ];
    }
}