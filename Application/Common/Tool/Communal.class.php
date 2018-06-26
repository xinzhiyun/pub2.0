<?php
/**
 * Created by PhpStorm.
 * User: 李振东
 * Time: 2018/3/29 下午2:37
 */
namespace Common\Tool;

use Think\Log;
class Communal
{
    public static function login($user)
    {
        $url = "http://devicecloud.dianqiukj.com/api/Login/index";
        $res =  httpPost($url,['user'=>$user]);
        $res = json_decode($res);
        objtoArray($res);

        return $res;
;    }
    /**
     * 设置微信
     */
    public static function setWX($conf)
    {
        $_SESSION['WX_CONFIG']['TOKEN']     = $conf['wx_tocken'];
        $_SESSION['WX_CONFIG']['APPID']     = $conf['wx_appid'];
        $_SESSION['WX_CONFIG']['APPSECRET'] = $conf['wx_appsecret'];
        $_SESSION['WX_CONFIG']['MCHID']     = $conf['wx_mchid'];
        $_SESSION['WX_CONFIG']['KEY']       = $conf['wx_key'];

        // 相关链接的生成 支付回调 事件回调
    }

    /**
     * 设置数据库
     */
    public static function setDB($conf)
    {
        $_SESSION['DB_CONFIG']['DB_PREFIX'] = $conf['db_prefix'];
        $_SESSION['DB_CONFIG']['DB_USER']   = $conf['db_user'];
        $_SESSION['DB_CONFIG']['DB_PWD']    = $conf['db_password'];
        $_SESSION['DB_CONFIG']['DB_HOST']   = $conf['db_host'];
        $_SESSION['DB_CONFIG']['DB_PORT']   = $conf['db_port'];
        $_SESSION['DB_CONFIG']['DB_NAME']   = $conf['db_name'];
    }

    public static function toJson($e, $msg='', $status=200)
    {
        if(is_array($e)){
            $res=array_merge($e,['status'=>$status,'msg'=>$msg]);
        }else{
            $res = [
                'status' => $e->getCode(),
                'msg' =>   $e->getMessage(),
            ];
        }
        $data['state']   = (!empty($data['status']) and $data['status']== 200) ? "success" : "fail";

        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($data));
    }


}