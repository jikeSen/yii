<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/11/21
 * Time: 11:58
 */

namespace api\modules\v1\controllers;


class PayController extends ApiActiveController
{
    public $modelClass = 'api\models\Order';

    public function actionNotify()
    {
        return ['code' => '200', 'data' => ['success']];
    }
}