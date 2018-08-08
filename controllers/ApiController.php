<?php
/**
 * Date: 2017/9/12
 * Time: 14:38
 */

namespace mztest\ucenter\controllers;

use Yii;
use yii\web\Controller;
use mztest\ucenter\models\UCenter;

class ApiController extends Controller
{
    public $enableCsrfValidation = false;

    /* @var $uCenter UCenter */
    protected $uCenter;

    public function init()
    {
        parent::init();

        $this->uCenter = new UCenter([
            'code' => Yii::$app->request->get('code')
        ]);
        $this->uCenter->validate();
    }

    public function actionIndex()
    {
        $action = $this->uCenter->getAction();

        // Load special params.
        if (in_array($action, ['synLogin'])) {
            $model = $this->module->userModel;
            $attribute = $this->module->emailAttribute;

            return $this->uCenter->{$action}($model, $attribute);
        }
        return $this->uCenter->{$action}();
    }

}
