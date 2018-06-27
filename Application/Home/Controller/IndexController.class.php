<?php
namespace Home\Controller;
use Common\Tool\Communal;
use Common\Tool\GatewayClient;
use Think\Controller;
class IndexController extends CommonController 
{
    public function indexx()
    {
    	$userInfo = $_SESSION['homeuser'];

    	if($userInfo){
	        // 获取用户当前设备
	        $did = $userInfo['did'];
            //show($did);die;
	        if(empty($did)){
	        	// 请先绑定设备
	        	$this->success('请先绑定设备...', '/Home/Devices/manage');
                // exit;
	        }else{
	        	// 查询用户设备设备码
	        	$deviceInfo = M('Devices')
	        	->where('pub_devices.id='.$did)
	        	->join('pub_devices_statu ON pub_devices_statu.DeviceID=pub_devices.device_code')
	        	->field('RawTDS,PureTDS,ReFlow,SumFlow,device_code')
	        	->find();
                
                if(empty($deviceInfo)){ $this->redirect('/Home/Devices/manage'); }
                // 滤芯详情
                $code = M('devices')->where("id={$_SESSION['homeuser']['did']}")->find();

                $status = M('devices_statu')->where("DeviceID='{$code['device_code']}'")->find();
                $type = M('device_type')->where("id={$code['type_id']}")->find();
                unset($type['id'], $type['typename'], $type['addtime']);
                $sum = array_filter($type);
                foreach ($sum as $key => $value) {
                    $str = stripos($value,'-');
                    $map['filtername'] = substr($value, 0,$str);
                    $map['alias'] = substr($value, $str+1);
                    $res[] = M('filters')->where($map)->find();
                }
                $assign = array(
                    'res' => json_encode($res),
                    'status' => json_encode($status),
                    'deviceInfo' => json_encode($deviceInfo),
                );


                // 分配数据到模板
                $this->assign($assign);
            	// 显示模板
	        	$this->display();	
	        }


    	}

	}

    // 滤芯数据
    public function filter()
    {
        // 滤芯详情
        $code = M('devices')->where("id={$_SESSION['homeuser']['did']}")->find();
        $status = M('devices_statu')->where("DeviceID='{$code['device_code']}'")->find();
        $type = M('device_type')->where("id={$code['type_id']}")->find();
        unset($type['id'], $type['typename'], $type['addtime']);
        $sum = array_filter($type);
        foreach ($sum as $key => $value) {
            $str = stripos($value,'-');
            $map['filtername'] = substr($value, 0,$str);
            $map['alias'] = substr($value, $str+1);
            $res[] = M('filters')->where($map)->find();
        }
        $assign = array(
            'res' => json_encode($res),
            'status' => json_encode($status),

        );
        // 分配数据到模板
        $this->assign($assign);
        // 显示模板
        $this->display();
    }


    public function index()
    {
        $userInfo = $_SESSION['homeuser'];

        if($userInfo){
            // 获取用户当前设备
            $did = $userInfo['did'];
            //show($did);die;
            if(empty($did)){
                // 请先绑定设备
                $this->success('请先绑定设备...', '/Home/Devices/manage');
                // exit;
            }else{
                // 查询用户设备设备码
                $deviceInfo = M('Devices')
                    ->where('pub_devices.id='.$did)
                    ->join('pub_devices_statu ON pub_devices_statu.DeviceID=pub_devices.device_code')
                    ->field('RawTDS,PureTDS,ReFlow,SumFlow,device_code')
                    ->find();

                if(empty($deviceInfo)){ $this->redirect('/Home/Devices/manage'); }
                // 滤芯详情
                $code = M('devices')->where("id={$_SESSION['homeuser']['did']}")->find();

                $status = M('devices_statu')->where("DeviceID='{$code['device_code']}'")->field('leasingmode,devicestause')->find();

                $type = M('device_type')->where("id={$code['type_id']}")->find();
                unset($type['id'], $type['typename'], $type['addtime']);
                $sum = array_filter($type);
                foreach ($sum as $key => $value) {
                    $str = stripos($value,'-');
                    $map['filtername'] = substr($value, 0,$str);
                    $map['alias'] = substr($value, $str+1);
                    $res[] = M('filters')->where($map)->find();
                }
//                $assign = array(
//                    'res' => json_encode($res),
//                    'status' => $status,
//                    'deviceInfo' => json_encode($deviceInfo),
//                );
                if(empty($status['leasingmode'])){
                    $status['leasingmode'] = 0;
                }
                // 分配数据到模板
                $this->assign('leasingmode',$status['leasingmode']);
                $this->assign('devicestause',$status['devicestause']);

                $this->assign('res',json_encode($res));
                $this->assign('deviceInfo',json_encode($deviceInfo));
                // 显示模板
                $this->display('indexWebsocketNew');
            }


        }
    }
}