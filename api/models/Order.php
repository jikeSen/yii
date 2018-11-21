<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "sh_order".
 *
 * @property int $or_id 自增id
 * @property int $order_num 内部订单的的订单号 例如：提交支付包
 * @property int $order_type 订单类型  1：捎货 2：商城 3：宅配
 * @property int $handle_uid 当前责任人uid 比如：捎货人
 * @property int $status 订单状态：（1,2,3,4分别为等待买家付款，买家已付款，商家已发货，交易完成）
 * @property string $pay_log_ids 订单产生的流水的id的集合 该字段存储json
 * @property string $create_time 订单创建时间
 * @property string $update_time 更新时间
 */
class ShOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sh_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_num', 'order_type', 'status', 'pay_log_ids'], 'required'],
            [['order_num', 'order_type', 'handle_uid', 'status'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['pay_log_ids'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'or_id' => 'Or ID',
            'order_num' => 'Order Num',
            'order_type' => 'Order Type',
            'handle_uid' => 'Handle Uid',
            'status' => 'Status',
            'pay_log_ids' => 'Pay Log Ids',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
