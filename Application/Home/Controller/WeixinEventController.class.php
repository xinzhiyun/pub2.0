<?php
namespace Home\Controller;
use Common\Tool\Communal;
use Common\Tool\WeiXin;
use Home\Controller\WechatController;
use Think\Controller;
use Think\Log;

class WeixinEventController extends Controller
{
    public function __call($name, $arguments)
    {
        $res = Communal::login($name);

        if(!empty($res) && $res['status']==200){
            Communal::setDB($res['data']);
            Communal::setWX($res['data']);
            $this->getEventData();exit;
        }
    }

    // 接受微信服务器下发的事件
    public function getEventData()
    {
        if(empty($_SESSION['WX_CONFIG'])){
            //辨别微信客户
            if(!empty($_GET['user_sign'])){
                unset($_SESSION['DB_CONFIG']);
                $res = Communal::login($_GET['user_sign']);
                if(!empty($res) && $res['status']==200){
                    Communal::setWX($res['data']);
                    Communal::setDB($res['data']);
                }
            }
        }

    	// 接受微信推送的事件
    	$xml=file_get_contents('php://input', 'r');

		if($xml){
		    // 转成php数组
			$data = $this->toArray($xml);
//			Log::write(json_encode($data), '微信');

            if(!empty($data['Event'])){
                $Wechat = new WechatController; // 实例化微信信息类型
                // 判断如果是关注事件
                if($data['Event'] == 'subscribe'){
                    // 调用填写微信信息的方法
                    $Wechat->add($data['FromUserName']);
                    exit;
                }

                // 判断如果是取消关注事件
                if($data['Event'] == 'unsubscribe'){
                    // 调用删除微信信息的方法
                    $Wechat->delete($data['FromUserName']);
                    exit;
                }
                // 判断如果是上报地理位置事件
                if($data['Event'] == 'LOCATION'){
                    // 调用上报用户地理位置的方法
                    $Wechat->location($data);
                    exit;
                }
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