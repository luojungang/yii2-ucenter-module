<?php
/**
 * Date: 2017/9/12
 * Time: 17:26
 */

namespace luojungang\ucenter\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Object;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

require_once __DIR__ .'/../uc_client/client.php';

class UCenter extends Object
{
    const API_RETURN_SUCCEED = 1;

    public $code;

    protected $get = [];
    protected $post = [];

    public function init()
    {
        parent::init();

        if ($this->code === null) {
            throw new InvalidConfigException('"Code" property must be specified.');
        }

        parse_str(uc_authcode($this->code, 'DECODE', UC_KEY), $this->get);
        $this->post = uc_unserialize(file_get_contents('php://input'));
    }

    public function validate()
    {
        if (empty($this->get)
            || !($action = $this->getAction())
            || !($time = ArrayHelper::getValue($this->get, 'time'))
        ) {
            throw new BadRequestHttpException();
        }

        if(time() - $time > 3600) {
            throw new BadRequestHttpException('Authorisation has expired');
        }
    }

    public function test()
    {
        return self::API_RETURN_SUCCEED;
    }

    public function updateApps()
    {
        $cacheFile = __DIR__.'/../uc_client/data/cache/apps.php';
        $fp = fopen($cacheFile, 'w');
        $s = "<?php\r\n";
        $s .= '$_CACHE[\'apps\'] = '.var_export($this->post, true).";\r\n";
        fwrite($fp, $s);
        fclose($fp);

        return self::API_RETURN_SUCCEED;
    }

    public function synLogin($model, $attribute)
    {
        if ($ucUser = uc_get_user($this->get['uid'], 1)) {
            list($uid, , $email) = $ucUser;
            $user = $model::findOne([$attribute => $email]);
            if ($user) {
                header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
                Yii::$app->user->login($user, 3600 * 24 * 30);
                return self::API_RETURN_SUCCEED;
            }
        }
        return false;
    }

    public function synLogout()
    {
        header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
        Yii::$app->user->logout();
        return self::API_RETURN_SUCCEED;
    }

    /**
     * @return mixed|null
     */
    public function getAction()
    {
        $actionList = [
            'test' => 'test',
            'deleteuser' => 'deleteUser',
            'renameuser' => 'renameUser',
            'updatepw' => 'updatePassword',
            'gettag' => 'getTag',
            'synlogin' => 'synLogin',
            'synlogout' => 'synLogout',
            'updatebadwords' => 'updateBadWords',
            'updatehosts' => 'updateHosts',
            'updateapps' => 'updateApps',
            'updateclient' => 'updateClient',
            'updatecredit' => 'updateCredit',
            'getcreditsettings' => 'getCreditSettings',
            'updatecreditsettings' => 'updateCreditSettings',
            'getcredit' => 'getCredit',
        ];
        $action = ArrayHelper::getValue($this->get, 'action');

        if (isset($actionList[$action])) {
            return $actionList[$action];
        }
        return null;
    }
}
