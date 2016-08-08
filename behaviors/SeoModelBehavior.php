<?php
namespace seo\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use seo\models\SeoData;
use kalpok\helpers\Inflector;
use kalpok\helpers\Utility;

class SeoModelBehavior extends Behavior
{
    public $metaKeywords;
    public $metaDescription;
    public $seoTitle;
    public $seoUrl;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
        ];
    }

    public function afterSave()
    {
        $owner = $this->owner;
        $seoData = new SeoData;
        $seoData->ownerClassName = $owner::className();
        $seoData->ownerId = $owner->id;
        $title = $this->seoTitle;
        $seoData->title = $owner->$title;
        $metaKeywords = $this->metaKeywords;
        $seoData->metaKeywords = $owner->$metaKeywords;
        $metaDescription = $this->metaDescription;
        $seoData->metaDescription = Utility::makeExcerpt($owner->$metaDescription, 160);
        $seoUrl = $this->seoUrl;
        $seoData->url = Inflector::persianSlug($owner->$seoUrl);
        $seoData->save();
    }

    public function beforeDelete()
    {
        $owner = $this->owner;
        $seoData = new SeoData;
        $seoData->findByOwner($owner::className(), $owner->id);
        $seoData->delete();
    }

    public function getSeoData()
    {
        $owner = $this->owner;
        return $this->owner->hasOne(SeoData::className(), ['ownerId' => 'id'])
            ->where(['ownerClassName'=>$owner::className()]);
    }

    public function getSeoParams($paramIndexes)
    {
        $params = [];
        foreach ($paramIndexes as $index) {
            if ($index == 'slug') {
                $params[$index] = $this->owner->seoData->url;
            } else {
                $params[$index] = $this->owner->$index;
            }
        }
        return $params;
    }
}
