<?php
namespace seo\behaviors;

use yii\base\Behavior;
use yii\base\Controller;
use seo\models\SeoData;
use yii\helpers\Url;

class SeoControllerBehavior extends Behavior
{
    public $actions = [];

    public function events()
    {
        return [Controller::EVENT_BEFORE_ACTION => 'beforeAction'];
    }

    public function beforeAction($event)
    {
        // check url whit slug if it exists
        $action = $event->action->id;
        if (array_key_exists($action, $this->actions)) {
            $className = $this->actions[$action]['modelClassName'];
        } else {
            return $event->isValid;
        }
        if (isset($_GET['id'])) {
            $model = $this->findModel($_GET['id'], $className);
            $seoData = SeoData::find()
                ->where(['ownerClassName' => $className, 'ownerId' => $model->id])
                ->one();
        } elseif (isset($_GET['slug'])) {
            $seoData = SeoData::find()
                ->where(['ownerClassName' => $className, 'url' => $_GET['slug']])
                ->one();
            $model = $this->findModel($seoData->ownerId, $className);
        }
        $actionArray = array_merge([$this->actions[$action]['route']], $model->getSeoParams($this->actions[$action]['params']));
        $url = Url::to($actionArray, true);
        if (strpos(\Yii::$app->request->getAbsoluteUrl(), $url) === false) {
            $this->owner->redirect($url, 301);
        }

        // add meta keyword
        \Yii::$app->view->registerMetaTag([
            'name' => 'keywords',
            'content' => $model->seoData->metaKeywords,
        ]);

        // add meta description
        \Yii::$app->view->registerMetaTag([
            'name' => 'description',
            'content' => $model->seoData->metaDescription,
        ]);

        // add title
        \Yii::$app->view->title = $model->seoData->title;

        $this->owner->model = $model;
        return $event->isValid;
    }

    protected function findModel($id, $class)
    {
        if (($model = $class::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
