# yii2-ucenter-module
Yii2 module for integration ucenter

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist mztest/yii2-ucenter-module "*"
```

or add

```
"mztest/yii2-ucenter-module": "*"
```

to the require section of your `composer.json` file.

Usage
-----
Step 1:

Login UCenter admin system, create a new application.
```
应用的主 URL: <code>http://your site doamin/ucenter</code>
```
 
Step 2: 

Save the config into `@app/config/ucenter.php`
```php
define('UC_CONNECT', 'mysql');
define('UC_DBHOST', 'localhost');
define('UC_DBUSER', '');
define('UC_DBPW', '');
define('UC_DBNAME', '');
define('UC_DBCHARSET', 'utf8');
define('UC_DBTABLEPRE', '`ultrax`.pre_ucenter_');
define('UC_DBCONNECT', '0');
define('UC_KEY', '');
define('UC_API', 'http://youdomain/discuz/uc_server');
define('UC_CHARSET', 'utf-8');
define('UC_IP', '');
define('UC_APPID', '2');
define('UC_PPP', '20');

// For auto creating discuz member
define('UC_DISCUZ_MEMBER', true);
define('UC_DISCUZTABLEPRE', '`ultrax`.pre_');
```

Step 3:

Config `@app/config/main.php`
```php
return [
    '...',
    'modules' => [
        'ucenter' => [
            'class' => 'mztest\ucenter\Module',
            'configFile' => '@app/config/ucenter.php' // default '@app/config/ucenter.php',
            'userModel' => '\app\models\User',
            'emailAttribute' => 'email',
        ],
    ],
    'components' => [
        '...',
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                ['pattern' => 'ucenter/api/uc.php', 'route' => 'ucenter/api/index'],
            ],
        ],
    ],
    '...'
];
```

Step 4:

About method, using <code>camelCase()</code>. Please see [PSR-1#methods](http://www.php-fig.org/psr/psr-1/#43-methods).
```php
uc_user_register() to ucUserRegister()
uc_get_user() to ucGetUser()
```
Enjoy it.

Example
------

Synlogin after user login.

Sometimes we build main site first, then merge it into ucenter later, so a little check was added here.
```php
// synchronize register and login
try {
    $uCenterClient = Yii::$app->getModule('ucenter')->getUCenterClient();
    if ($uCenterUser = $uCenterClient->ucGetUser($model->email)) {
        list($uCenterUid,,) = $uCenterUser;
    } else {
        $uCenterUid = $uCenterClient->ucUserRegister($model->email, $model->password, $model->email);
        if ($uCenterUid < 0) {
            $uCenterUid = false;
        }
    }
    if ($uCenterUid && ($syncScript = $uCenterClient->ucUserSynlogin($uCenterUid))) {
        Yii::$app->session->setFlash('success', $syncScript);
    }
}  catch (\Exception $e) {
    //
}
```