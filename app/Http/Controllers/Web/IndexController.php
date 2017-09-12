<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index (Request $request)
    {
        //1.获得参数　signature nonce token timestamp echostr
        $nonce = $request->nonce;
        $timestamp = $request->timestamp;
        $echostr = $request->echostr;
        $signature = $request->signature;
        $token = 'weixin';

        //2.形成数组，按字典顺序排序
        $arr = [];
        $arr = [$nonce, $timestamp, $token];
        sort($arr);
        //3.拼接成字符串，按sha1加密,然后与signature进行校验
        $str = sha1(implode('', $arr));

        if ($str == $signature) {
            echo $echostr;
            exit;
        }
    }

    //接收事件推送并回复
    public function responseMsg(Request $request)
    {
        //1.获取微信推送过来的post数据(xml格式)
    }
}