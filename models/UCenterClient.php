<?php
/**
 * Created by PhpStorm.
 * Date: 2017/9/13
 * Time: 10:17
 */

namespace mztest\ucenter\models;


use yii\base\Object;
use yii\base\UnknownMethodException;
use yii\helpers\Inflector;

require_once __DIR__ .'/../uc_client/client.php';

class UCenterClient extends Object
{
    public function __call($name, $arguments)
    {
        $name = Inflector::underscore($name);
        if (function_exists($name)) {
            return call_user_func_array($name, $arguments);
        } else {
            throw new UnknownMethodException('Calling unknown method: ' . get_class($this) . "::$name()");
        }
    }
}
