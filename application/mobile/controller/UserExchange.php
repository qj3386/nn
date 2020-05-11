<?php

namespace app\mobile\controller;

use think\Db;
use think\Request;
use app\common\lib\ReturnData;
use app\common\lib\Helper;
use app\common\logic\UserExchangeLogic;
use app\common\logic\UserExchange as UserExchangeModel;

class UserExchange extends Base
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function getLogic()
    {
        return new UserExchangeLogic();
    }

    //余额明细-列表
    public function index()
    {
        //参数
        $pagesize = 15;
        $offset = 0;
        if (isset($_REQUEST['page'])) {
            $offset = ($_REQUEST['page'] - 1) * $pagesize;
        }
        //获取余额明细列表
        $where = array();
        $orderby = 'id desc';
        $where['user_id'] = $this->login_info['id'];
        $res = $this->getLogic()->getList($where, $orderby, '*', $offset, $pagesize);

        $assign_data['list'] = $res['list'];
        //总页数
        $assign_data['totalpage'] = ceil($res['count'] / $pagesize);

        if (isset($_REQUEST['page_ajax']) && $_REQUEST['page_ajax'] == 1) {
            $html = '';

            if ($res['list']) {
                foreach ($res['list'] as $k => $v) {
                    $html .= '<li>';
                    $html .= '<div class="info"><p class="tit">' . $v['content'];
					if (!empty($v['address'])) { $html .= '<br>收货地址：' . $v['address']; }
					if (!empty($v['tracking_number'])) { $html .= '<br>快递单号：' . $v['tracking_number']; }
					$html .= '</p>';
                    $html .= '<p class="time">' . date('Y-m-d H:i:s', $v['add_time']) . '</p></div>';
                    $html .= '</li>';
                }
            }

            exit(json_encode($html));
        }

        $this->assign($assign_data);
        return $this->fetch();
    }
}