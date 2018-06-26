<?php
namespace Home\Controller;
use Common\Tool\Communal;
use Common\Tool\WeiXin;
use Think\Controller;
// 引入微信事件接收类
// use Home\Controller\WeixinEventController;

class WechatController extends Controller 
{
	/**
     * [index 微信关注事件-填写微信信息表]
     * @return [type] [description]
     */
    public function add($openid)
    {
        // 获取用户openid
        $accessToken = WeiXin::getAccessToken();

        // 请求返回简体中文版用户信息数据
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$accessToken.'&openid='.$openid.'&lang=zh_CN';

        // 发送请求获取用户信息
        $userInfo = WeiXin::httpGet($url);

        // 把 JSON 格式的字符串转换为PHP数组
        $userInfo = json_decode($userInfo, true);

        // 准备微信信息表数据
        // 微信的ID号
        $data['open_id'] = $userInfo['openid'];
        $data['nickname'] = $userInfo['nickname'];
        $data['head'] = $userInfo['headimgurl'];
        // 性别{0:未定义, 1:男, 2:女}
        $data['sex'] = $userInfo['sex'];
        // 地区 省份
        $data['area'] = $userInfo['province'];
        // 国家 省份 市区
        $data['address'] = $userInfo['country'].' '.$userInfo['province'].' '.$userInfo['city'];
        // 根据微信openid查询数据库id字段1条数据
        $userId = M('Wechat')->field('id')->where('`open_id`="'.$data['open_id'].'"')->find();

        // 如果数据库并未存储，将用户信息写入数据库
        if(empty($userId)){
            // 将用户信息写入数据库
            $insertId = M('Wechat')->data($data)->add();
            if($insertId){
                $userData['open_id'] = $data['open_id'];
                $userData['created_at'] = time();
                $userData['login_time'] = $userData['created_at'];
                $userData['login_ip'] = get_client_ip();
                M('Users')->data($userData)->add();
            }
        // 微信用户信息已存在，说明用户是第二是关注微信公众号
        }else{
            // 修改用户状态为1（启用）
            $userData['user_status'] = 1;
            M('Users')->where('`open_id`="'.$data['open_id'].'"')->save($userData);
        }
    }

    /**
     * [delete 取消关注事件 修改用户状态]
     * @param  [type] $openid [description]
     * @return [type]         [description]
     */
    public function delete($openid)
    {
        // 修改用户状态为0（禁用）
        $userData['user_status'] = 0;
        M('Users')->where('`open_id`="'.$openid.'"')->save($userData);
    }

    public function location($data)
    {
        file_put_contents('./LOCATION.txt', 
            '用户ID：'.$data['FromUserName']."\t\n".
            '消息创建时间 （整型）:'.$data['CreateTime']."\t\n".
            '地理位置纬度:'.$data['Latitude']."\t\n".
            '地理位置经度:'.$data['Longitude']."\t\n".
            '地理位置精度:'.$data['Precision']."\t\n"
        );
    }

    // 请先关注微信公众号
    public function follow()
    {
        // 显示模板
        $this->display();
    }

    /**
     * 生成自定义菜单
     * @return bool true or false
     * http://demo.dianqiukj.com/index.php/Home/Wechat/create_menu.html?user_sign=hanpai
     */
    public function create_menu()
    {
        if(!empty($_GET['user_sign'])){
            unset($_SESSION['DB_CONFIG']);
            $res = Communal::login($_GET['user_sign']);
            if(!empty($res) && $res['status']==200){
                Communal::setWX($res['data']);
            }else{
                echo 'ERROR';exit;
            }
        }

        // 调用获取公众号的全局唯一接口调用凭据
        $access_token = WeiXin::getAccessToken();

        $baseUrl = "http://demo.dianqiukj.com/index.php/";
        $user_sign = "dianqiu";

        $menu =[
            "button"=>[// 按钮数组
                [
                    "name"=>'设备管理',
                    "sub_button"=>[
                        ['type'=>"view",'name'=>"我的水机","url"=>$baseUrl."home/index/index/user_sign/".$user_sign],
                        ['type'=>"view",'name'=>"安装人员",'url'=>$baseUrl."home/index/index/user_sign/".$user_sign],
                        ['type'=>"view",'name'=>"代缴费",'url'=>$baseUrl."home/index/index/user_sign/".$user_sign],

                    ]
                ],
                [
                    "name"=>'关于韩派',
                    "sub_button"=>[
                        ['type'=>"view",'name'=>"新闻动态",'url'=>"http://www.hanpwater.com/wap/list/108_1.html"],

                    ]
                ]
            ]
        ];
        $jsonmenu = json_encode($menu,JSON_UNESCAPED_UNICODE);

//        $jsonmenu = '{
//            "button":[
//                {
//                  "name":"设备管理",
//                  "sub_button":[
//                        {
//                           "type":"view",
//                           "name":"我的水机",
//                           "url":"http://hanpai2.dianqiukj.com/index.php/home/index/index/user_sign/hanpai"
//                        },
//                        {
//                           "type":"view",
//                           "name":"安装人员",
//                           "url":"http://hanpai2.dianqiukj.com/home/users/login.html?user_sign=hanpai"
//                        },
//                        {
//                           "type":"view",
//                           "name":"代缴费",
//                           "url":"http://hanpai2.dianqiukj.com/home/shop/topup?user_sign=hanpai"
//                        }
//                    ]
//                },
//                {
//                   "name":"关于韩派",
//                   "sub_button":[
//                       {
//                           "type":"view",
//                           "name":"韩派官网",
//                           "url":"http://www.hanpwater.com/wap"
//                        },
//                        {
//                           "type":"view",
//                           "name":"新闻动态",
//                           "url":"http://www.hanpwater.com/wap/list/108_1.html"
//                        },
//                        {
//                           "type":"view",
//                           "name":"产品中心",
//                           "url":"http://www.hanpwater.com/wap/list/107_1.html"
//                        },
//                        {
//                           "type":"view",
//                           "name":"联系我们",
//                           "url":"http://www.hanpwater.com/wap/page/131.html"
//                        }
//                    ]
//               }
//           ]
//        }';


        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
        $result = WeiXin::httpPost($url, $jsonmenu);
        var_dump($result);

    }

}