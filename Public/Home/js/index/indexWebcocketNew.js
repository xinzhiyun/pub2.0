var home = new Vue({
	el: '.home',
	data() {
		return {
			code: '',			// 用户码，分享的时候用
			dataList: '',		//websocket接收的数据
			deviceId: '',		// 设备编码
			tdsPure: '--',		// 纯水tds
			tdsRaw: '--',		// 原水tds
			homeStyle: '',		// 首页部分
			leasingmode: '',	// 滤芯模式
			statusText: '--',	// 水机状态
			devicestause: '--',	// 设备状态
			leasingmode: '',	// 租赁模式
			filtermode: '',		// 滤芯模式
			// 设备状态集合
			dstauseList: {'0':'制水','1':'冲洗','2':'水满','3':'缺水','4':'漏水','5':'检修','6':'欠费停机','7':'关机','8':'开机(仅命令)'},
			// 租赁模式集合
			leasingmodeList: {'0':'零售型','1':'按流量计费','2':'按时间计费','3':'时长和流量套餐','4':'商务型'},
			// 滤芯模式集合
			filtermodeList: {'0':'按时长','1':'按流量','2':'时长和流量'},
			//包数据
			ajson: {
			    "DeviceID": 'deviceId',
			    "PackType": "login",
			    "Vison": 0,
			},
			switchText: '',			// 开关机状态
			reday: '--',			//剩余天数
			usedday: '--',			//已用天数
			reflow: '--',			//剩余流量
			usedflow: '--',			//已用流量
			powerStatus: '开机',						// 开关机状态
			filterstyle: 'display:none;',				// 滤芯部分
			lineStyle: '',								//横线
			filmainStyle: '',							// 滤芯详情，滤芯复位
			filterList: [],								// 滤芯详情数据集合
			resetFilter: '',							// 滤芯复位
			share: ''
		}
	},
	computed: {},
	mounted() {},
	methods: {
		// 开关机
		power: function(){
			var that = this;
			var text = '';
			if(that.powerStatus == '开机'){
				console.log('开机');
				text = '确定要开机吗？';

			}else if(that.powerStatus == '关机'){
				console.log('关机');
				text = '确定要关机吗？';
			}

			// 确认取消框
			confirmFn(text, function(res){
				if(res){
					// 设置数据包配置
					home.ajson['DeviceID'] = home.deviceId;
					home.ajson['PackType'] = 'SetData';
					home.ajson['curTime'] = 0;

					// 点击确定
					if(that.powerStatus == '开机'){
	                    home.ajson['DeviceStause'] = 8;
	                    home.ajson['type'] = '开机中';
						that.powerStatus = '关机';

					}else if(that.powerStatus == '关机'){
	                    home.ajson['DeviceStause'] = 7;
	                    home.ajson['type'] = '关机中';
						that.powerStatus = '开机';

					}
					// 发送数据包
					that.sendMSG(home.ajson);
					
					// 设置水机状态
					// home.statusIconName = home.statusIconClass[home.ajson['DeviceStause']];	// 水机状态图标
					// home.statusText = home.dstauseList[home.ajson['DeviceStause']];	// 水机状态

				}else{
					// 点击取消
					noticeFn({text:'取消！',time:800});
				}
			})
		},
		// 冲洗
		wash: function(){
			var that = this;
			if(home.statusText == '欠费停机' || home.statusText == '关机' || home.statusText == '检修'){
				noticeFn({text: '当前设备状态不能冲洗!'});
				return
			}
			confirmFn('点击确认开始冲洗', function(res){
				if(res){
					home.ajson['PackType'] = 'SetData';
					home.ajson['DeviceStause'] = '1';
					home.ajson['type'] = '冲洗中';
					home.ajson['curTime'] = '0';
					// 发送数据包
					that.sendMSG(home.ajson);
					
				}else{
					noticeFn({text: '取消'});
				}
			});

			// 设置水机状态
			// home.statusIconName = home.statusIconClass[1];	// 水机状态图标
			// home.statusText = home.dstauseList[1];	// 水机状态
		},
		// 显示分享面板
		sharePanel: function(){
			$('.shareContent').show();
			this.share = 'shareShow';
		},
		// 隐藏分享面板
		cancelShare: function(){
			this.share = '';
			setTimeout(function(){
				$('.shareContent').hide();
			},300);
		},
		// 显示滤芯页面
		filterShow: function(){
			location.href = location.href + '?filter&filtermode=' + this.filtermode;
			
		},
		// 切换滤芯详情，滤芯复位
		filterMove: function(e){
			var index = +e.target.getAttribute('index');
			var _class = e.target.getAttribute('dataclass');
			var other = index == 0 ? 1 : 0;			// 滤芯详情或滤芯复位
			// console.log(e);
			// 初始化
			$('.filterTop>span').attr('class',$('.filterTop>span').attr('dataclass'));
			// 设置字体颜色
			e.target.setAttribute('class',_class + ' fblue');
			// 横线滑动
			this.lineStyle = 'transition: .3s linear;transform: translateX('+ index*50 +'vw);';
			this.filmainStyle = 'transition: .3s linear;transform: translateX(-'+ index*100 +'vw);';
			$('.filtermain>ul').eq(index).show();
			$('.filtermain>ul').eq(other).hide();
		},
		// 复位选择
		resetSelect: function(e){
			var item = e.currentTarget;
			var check = item.getElementsByClassName('iconfont')[0];
			var checkClass = check.getAttribute('class');
			this.resetFilter = item.getAttribute('fname');		// 选中的滤芯
			// console.log(item);
			// 初始化
			for(var i=0; i<$('.filteritem>.iconfont').length; i++){
				$('.filteritem>.iconfont').eq(i).attr('class','iconfont icon-emptycircle');
			}
			// 选中，取消选择
			if(checkClass.indexOf('emptycircle') > -1){
				check.setAttribute('class','iconfont icon-selectcircle fblue');

			}else{
				check.setAttribute('class','iconfont icon-emptycircle');
				this.resetFilter = '';
			}
		},
		// 滤芯复位
		filterReset: function(){
			var that = this;
			var text = '复位';
			var filter = this.resetFilter;
			if(!this.resetFilter){
				noticeFn({text:'请选择需要复位的滤芯！'});
				return
			}
			// 确认取消框
			confirmFn(text + "滤芯" + this.resetFilter, function(res){
				if(res){ 
					home.ajson['DeviceID'] = home.deviceId;
					home.ajson['PackType'] = 'SetData';
					home.ajson['type'] = '复位中';
					
                    home.ajson['ReFlowFilter'+ filter ] = home.filterList[filter].reflow;
                    home.ajson['ReDayFilter'+ filter ] = home.filterList[filter].reday;
                    home.ajson['FlowLifeFilter'+ filter ] = home.filterList[filter].allFlow;
                    home.ajson['DayLifeFiter'+ filter ] = home.filterList[filter].allLife;
					console.log('home.ajson: ',home.ajson);
					// 点击确定
					// 发送数据包
					that.sendMSG(home.ajson);
				}else{
					// 点击取消
					noticeFn({text:'取消！'});
				}
			})
		},
		sendMSG: function(mode){
			// mode: 1   //开机	 2关机  3冲洗  4取消冲洗  5复位滤芯  "Pram":[1,2] 滤芯级数
			console.log('mode: ',mode);
			if(mode == 3 && home.switchText == 0){
				// 关机状态不允许冲洗
				layuiHint('关机状态不允许冲洗');
				return
			}
			// 发送数据（websocket发送）
			// sendmsg(mode);
			var option = {
				url: 'http://devicecloud.dianqiukj.com/api/device/deviceAction',
				type: 'post',
				dataType: 'jsonp',
				jsonp: 'jsoncallback',
				success: function(res){
					console.log('res: ',res);
					if(res.status == 200){
						// 成功
					}else{
						noticeFn({text: res.msg});
					}
				},
				error: function(err){
					console.log('err: ',err);
					noticeFn({text: '系统出错，请稍后再试'});
				}
			}
			if(mode == 5){
				// 复位
				option['data'] = {
					mode: mode,
					deviceID: home.deviceId,
					data: ''// 几级滤芯（多个的时候用下划线分割‘_’）
				}
			}else{
				option['data'] = {
					mode: mode,
					deviceID: home.deviceId
				}
			}
			// 调用后端接口
			$.ajax(option);
		},
		// 分享
		shareFn: function(){
			// type: 1 发送给微信好友，2 发送到微博，3 分享到朋友圈，
			// 4 分享到QQ，5 分享到QQ空间，6 添加到微信收藏
			wxShare(home.code);
		}
	}
})