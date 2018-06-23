<?php
namespace Home\Controller;
use Common\Tool\Communal;
use Common\Tool\WeiXin;
use Think\Controller;
use \Org\Util\WeixinJssdk;
use Home\Controller\WechatController;
/**
 * 前共控制器
 * 前台控制器除login外必须继承我
 * @author 吴智彬 <519002008@qq.com>
 */

class CommonController extends Controller 
{
    public function __construct()
    {
        if(!empty($_GET['user_sign'])){
            $conf = D('Admin/Login')->where("id='{$_GET['user_sign']}'")->find();
            if(!empty($conf)){
                Communal::setDB($conf);
                Communal::setWX($conf);
            }else{
                echo 'ERROR';
            }
        }
        parent::__construct();
    }
	/**
     * 初始化
     * @author 吴智彬 <519002008@qq.com>
     */
    public function _initialize()
    {
        // 获取用户信息写入缓存
        if(empty($_SESSION['homeuser'])){

            if(C('wx_debug')){
                $openId   = 'oXwY4t_vkTgtlD0CBTZ-vTbIMWHs';
            }else{
                $openId = WeiXin::GetOpenid();
                $openId_ifno = WeiXin::getSignPackage();
            }
            $weixinInfo = [$openId,$openId_ifno];
            session('weixin',$weixinInfo);
            // 查询用户信息
            $info = M('Users')->where("open_id='{$openId}'")->find();
            
            // 判断用户是否存在
            if($info){
                // 用户当前设备
                $info['did'] = M('currentDevices')->where("`uid`={$info['id']}")->field('did')->find()['did'];

                $_SESSION['homeuser'] = $info;   
                
            }else{
                // 用户不存在
                redirect(U('/Home/Wechat/follow'), 2, '请先关注微信公众号...');
                
            }
        }

        if(empty($_SESSION['homeuser']['did'])){
            $_SESSION['homeuser']['did']= M('currentDevices')->where("`uid`={$_SESSION['homeuser']['id']}")->field('did')->find()['did'];
        }
    }


}