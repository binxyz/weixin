<?php
namespace App\Models;

class Weixin
{
    public function responseNews($postObj, $arr)
    {
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
    }
}