<?php
/**
 * Date: 2017/9/12
 * Time: 14:33
 */
namespace luojungang\ucenter;

use luojungang\ucenter\models\UCenterClient;

class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'luojungang\ucenter\controllers';

    public $id = 'ucenter';

    public $layout = 'main';

    //public $layoutPath = '';

    public $viewPath = 'views';

    /**
     * @var string the default route of this module. Defaults to `default`.
     * The route may consist of child module ID, controller ID, and/or action ID.
     * For example, `help`, `post/create`, `admin/post/create`.
     * If action ID is not given, it will take the default value as specified in
     * [[Controller::defaultAction]].
     */
    public $defaultRoute = 'api';

    public $configFile = '@app/config/ucenter.php';

    public $userModel = '\app\models\User';
    public $emailAttribute = 'email';
    /**
     * @var UCenterClient
     */
    protected $uCenterClient;
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
        $this->configFile = \Yii::getAlias($this->configFile);
        require_once($this->configFile);

        $this->uCenterClient = new UCenterClient();
    }

    public function getUCenterClient()
    {
        return $this->uCenterClient;
    }
}
