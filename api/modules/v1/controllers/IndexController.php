<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/11/21
 * Time: 16:06
 */

namespace api\modules\v1\controllers;


class IndexController extends ApiActiveController
{
    public $modelClass = 'api\models\Order';

    public function actionSay()
    {
        echo 'say';
    }
}