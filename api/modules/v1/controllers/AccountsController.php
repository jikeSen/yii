<?php
/**
 * User: 极客Sen
 * Date: 2017/4/10
 * Time: 20:56
 */

namespace api\modules\v1\controllers;


class AccountsController extends ApiActiveController
{
    public $modelClass = 'api\models\ShOrder';

    /**
     * @SWG\Post(
     *     path="/v1/accounts/login",
     *     summary="openid登录",
     *     operationId="login",
     *     description="登录",
     *     produces={"application/json"},
     *     tags={"用户登录服务"},
     *
     *     @SWG\Parameter(name="openId",  in="query",description="用户openid",required=true,type="string"),
     *     @SWG\Parameter(name="password",in="query",description="md5密码",required=true,type="string",),
     *     @SWG\Response(response=200, description="请求成功"),
     *     @SWG\Response(response=401, description="未授权"),
     *     @SWG\Response(response=404, description="用户不存在")
     * )
     */
    public function actionLogin()
    {
        return ['code' => '0000', 'data' => ['a' => '15565']];
    }

    /**
     * @SWG\Post(
     *     path="/v1/accounts/info",
     *     operationId="info",
     *     summary="获取用户信息",
     *     description="获取用户信息",
     *     produces={"application/json"},
     *     tags={"用户登录服务"},
     *
     *     @SWG\Parameter(name="openId",  in="query",description="用户openid",required=true,type="string"),
     *
     *     @SWG\Response(response=200, description="请求成功"),
     *     @SWG\Response(response=401, description="未授权"),
     *     @SWG\Response(response=404, description="用户不存在")
     * )
     */
    public function actionInfo()
    {
        return ['code' => '0000', 'data' => ['abc' => 'nihao hasdja-0']];
    }

    /**
     * @SWG\Post(
     *     path="/v1/accounts/update-profile",
     *     operationId="update-profile",
     *     summary="更新用户信息",
     *     description="更新用户信息",
     *     produces={"application/json"},
     *     tags={"用户登录服务"},
     *
     *     @SWG\Parameter(name="openId",  in="query",description="用户openid",required=true,type="string"),
     *
     *     @SWG\Response(response=200, description="请求成功"),
     *     @SWG\Response(response=401, description="未授权"),
     *     @SWG\Response(response=404, description="用户不存在")
     * )
     */
    public function actionUpdateProfile()
    {
        return ['code' => '0000', 'data' => ['profile' => 'i want to kill you']];
    }


}