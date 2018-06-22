<?php
namespace Home\Controller;
use Common\Tool\Communal;
use Home\Controller\WechatController;
use Think\Controller;
use Think\Log;

class WeixinEventController extends Controller
{
    public function __call($name, $arguments)
    {
        // 识别客户
        $conf = D('Admin/Login')->where("user='{$name}'")->find();
        if(!empty($conf)){
            Communal::setDB($conf);
            Communal::setWX($conf);
            $this->getEventData();exit;
        }
        echo 'ERROR';
    }

    // 接受微信服务器下发的事件
    public function getEventData()
    {
    	// 接受微信推送的事件
    	$xml=file_get_contents('php://input', 'r');

		if($xml){
		    // 转成php数组
			$data = $this->toArray($xml);
            Log::write(json_encode($data), '微信');

            // 判断如果是关注事件
            if($data['Event'] == 'subscribe'){
            //echo 2;   
                // 实例化微信信息类型
                $Wechat = new WechatController;
                // 调用填写微信信息的方法
                $Wechat->add($data['FromUserName']);
                
                // file_put_contents('./add.txt', $xml);
                exit;
            }

            // 判断如果是取消关注事件
            if($data['Event'] == 'unsubscribe'){
                //file_put_contents('./del.txt', $xml);
                // 实例化微信信息类型
                $Wechat = new WechatController;
                // 调用删除微信信息的方法
                $Wechat->delete($data['FromUserName']);
                exit;
            }

            // 判断如果是上报地理位置事件
            if($data['Event'] == 'LOCATION'){
                //file_put_contents('./del.txt', $xml);
                // 实例化微信信息类型
                $Wechat = new WechatController;
                // 调用上报用户地理位置的方法
                $Wechat->location($data);
                exit;
            }
		}else{
            // // 实例化微信验证对象服务器第一次接入使用
            $wechatObj = new \Org\Util\WechatCallbackapiTest;
            // // 执行验证方法
            $wechatObj->valid();
        }
	}

    /**
     * 将xml转为array
     * @param  string $xml xml字符串
     * @return array       转换得到的数组
     */
    public function toArray($xml){   
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $result= json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);        
        return $result;
    }

}