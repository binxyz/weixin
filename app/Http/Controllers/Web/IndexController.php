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

        if ($str == $signature && $echostr) {
            //第一次微信接入微信api接口时
            echo $echostr;
            exit;
        } else {
            $this->responseMsg();
        }
    }

    //接收事件推送并回复
    public function responseMsg(Request $request)
    {
        //1.获取微信推送过来的post数据(xml格式)
//        $postXml = $GLOBALS['HTTP_RAW_POST_DATA']; //post传递过来，不是一个数组所以用$GLOBALS接收
        $postXml = file_get_contents('php://input'); //php7写法
        //2.处理消息类型,并设置回复类型和内容
//        <xml>
//        <ToUserName><![CDATA[toUser]]></ToUserName>
//        <FromUserName><![CDATA[FromUser]]></FromUserName>
//        <CreateTime>123456789</CreateTime>
//        <MsgType><![CDATA[event]]></MsgType>
//        <Event><![CDATA[subscribe]]></Event>
//        </xml>
        $postObj = simplexml_load_string($postXml); //将xml转换为对象

        if (strtolower($postObj->MsgType) == 'event') {
            //如果是关注事件
            if (strtolower($postObj->Event) == 'subscribe') {
                //回复用户消息
                $toUser = $postObj->FromUserName;
                $FromUserName = $postObj->ToUserName;
                $createTime = time();
                $msgType = 'text';
                $content = 'hello';
                $template = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            </xml>";
                $info = sprintf($template, $toUser, $FromUserName, $createTime, $msgType, $content);
                echo $info;
            }
        }


        //回复单图文
        if (strtolower($postObj->MsgType) == 'text' && $postObj->Content == 'tuwen') {
            $arr = [
                [
                    'title' => 'haha',
                    'description' => '这是单图文',
                    'picurl' => 'https://ss0.bdstatic.com/70cFuHSh_Q1YnxGkpoWK1HF6hhy/it/u=4258410114,1864035878&fm=27&gp=0.jpg',
                    'url' => 'www.baidu.com'
                ]
            ];
            $template = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <ArticleCount>".count($arr)."</ArticleCount>
                        <Articles>";
            foreach($arr as $k => $v) {
                $template .= "<item>
                        <Title><![CDATA[".$v['title']."]]></Title>
                        <Description><![CDATA[".$v['description']."]]></Description>
                        <PicUrl><![CDATA[".$v['picurl']."]]></PicUrl>
                        <Url><![CDATA[".$v['url']."]]></Url>
                        </item>";
            }

            $template .="</Articles>
                        </xml>";
            $toUser = $postObj->FromUserName;
            $fromUser = $postObj->ToUserName;
            $createTime = time();
            $msgType = 'news';
            echo sprintf($template, $toUser, $fromUser, $createTime, $msgType);

        } else {
            //微信用户回复文本消息给公众号
        // <xml>
        // <ToUserName><![CDATA[toUser]]></ToUserName>
        // <FromUserName><![CDATA[fromUser]]></FromUserName>
        // <CreateTime>1348831860</CreateTime>
        // <MsgType><![CDATA[text]]></MsgType>
        // <Content><![CDATA[this is a test]]></Content>
        // <MsgId>1234567890123456</MsgId>
        // </xml>
            switch (trim($postObj->Content)) {
                case 1:
                    $content = '您输入为１';
                    break;
                case 2:
                    $content = '您输入为2';
                    break;
                case 3:
                    $content = '您输入为3';
                    break;
                case 4:
                    $content = "<a href='www.baidu.com'>百度</a>";
                    break;
            }
            $template = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        </xml>";
            $toUser = $postObj->FromUserName;
            $fromUser = $postObj->ToUserName;
            $createTime = time();
            $msgType = 'text';
            $info = sprintf($template, $toUser, $fromUser, $createTime, $msgType, $content);
            echo $info;
        }
    }
}