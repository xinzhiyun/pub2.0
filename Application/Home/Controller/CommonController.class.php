<?php
namespace Home\Controller;
use Common\Tool\Communal;
use Common\Tool\WeiXin;
use Think\Controller;
/**
 * 前共控制器
 * 前台控制器除login外必须继承我
 * @author 吴智彬 <519002008@qq.com>
 */

class CommonController extends Controller 
{
	/**
     * 初始化
     * @author 吴智彬 <519002008@qq.com>
     */
    public function _initialize()
    {
        if(!empty($_GET['user_sign'])){
            //unset($_SESSION['DB_CONFIG']);
            $res = Communal::login($_GET['user_sign']);

            if(!empty($res) && $res['status']==200){
                Communal::setDB($res['data']);
                Communal::setWX($res['data']);
            }else{
                echo 'ERROR';exit;
            }
        }

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