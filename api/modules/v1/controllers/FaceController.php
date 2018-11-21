<?php
/**
 * Created by PhpStorm.
 * User: sen
 * Date: 2018/5/24
 * Time: 下午3:32
 */

namespace api\modules\v1\controllers;

use api\models\Users;
use Yii;
use yii\httpclient\Client;
use yii\httpclient\Request;
use yii\httpclient\RequestEvent;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;

class FaceController extends ApiActiveController
{
    public $webUrl = 'obs-web.obs.cn-east-2.myhwclouds.com';
    public $modelClass = 'api\models\Users';

    /**
     * @SWG\Post(
     *     path="/v1/face/pk",
     *     summary="颜值打分",
     *     operationId="pk",
     *     description="打分颜值",
     *     produces={"application/json"},
     *     tags={"人脸识别"},
     *
     *     @SWG\Parameter(name="face_img",  in="query",description="用户上传的头像",required=true,type="string"),
     *     @SWG\Response(response=200, description="请求成功"),
     *     @SWG\Response(response=401, description="未授权"),
     *     @SWG\Response(response=404, description="用户不存在")
     * )
     */
    public function actionPk()
    {
        $client_ip = Yii::$app->request->getUserIP() == '127.0.0.1' ? '125.119.7.106' : Yii::$app->request->getUserIP();
        $face_img = Yii::$app->request->get('face_img');

        if (empty($face_img)) {
            return ['code' => '1111', 'data' => '参数不能为空'];
        }

        #生成分数
        $user = new Users();
        $user->ip = $client_ip;
        $user->face_img = $face_img;
        $user->city = '杭州';

        #更新分数
        $face = new \Hanson\Face\Foundation\Face();
        $result = $face->score->get($face_img);
        //var_dump($result);die;
        $user->socer = $result['score'];
        $user->socer_face_img = $result['url'];
        $user->texts = $result['text'];


        #调用api
        $client = new Client();

        $response = $client->createRequest()
            ->setMethod('get')
            ->setUrl('http://apis.juhe.cn/ip/ip2addr')
            ->setData(['ip' => $client_ip, 'key' => '1d2c575f1d874fc7adb642787bc906cd'])
            ->send();
        if ($response->isOk) {
            $apis = $response->data;
        }

        if ($apis['resultcode'] == '200') {
            $user->city = $apis['result']['area'];
        }

        if ($user->save()) {
            return ['code' => '0000', 'data' => $user->toArray()];
        } else {
            return ['code' => '1111', 'data' => $user->errors];
        }
    }

    /**
     * @SWG\Post(
     *     path="/v1/face/list-face",
     *     summary="颜值排序",
     *     operationId="pk",
     *     description="颜值排序",
     *     produces={"application/json"},
     *     tags={"人脸识别"},
     *     @SWG\Parameter(name="pageNum",  in="query",description="页码数",required=true,type="int"),
     *     @SWG\Response(response=200, description="请求成功"),
     *     @SWG\Response(response=401, description="未授权"),
     *     @SWG\Response(response=404, description="用户不存在")
     * )
     */
    public function actionListFace()
    {
        //处理分页
        $pageNum = Yii::$app->request->get("pageNum") ?? 0;
        $page_conf = [
            'totalCount' => Users::find()->count(),
            'page' => $pageNum,
            'defaultPageSize' => Yii::$app->params['pageSize'],
        ];

        $pagination = new Pagination($page_conf);
        $rows = Users::find()
            ->asArray()
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['socer' => SORT_DESC])
            ->all();
        foreach ($rows as $k => $v) {
            $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
            $color = '#' . $rand[rand(0, 15)] . $rand[rand(0, 15)] . $rand[rand(0, 15)] . $rand[rand(0, 15)] . $rand[rand(0, 15)] . $rand[rand(0, 15)];
            $rows[$k]['color'] = $color;
        }

        $page = [
            'totalPage' => $pagination->getPageCount(),
            'currentPageNum' => $pageNum,
            'currentIterm' => $pagination->defaultPageSize,
        ];

        return ['code' => '0000', 'data' => ['page' => $page, 'date' => $rows]];
    }
}