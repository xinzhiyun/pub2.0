/**
 * @description {设备操作函数}
 * @param {object} data 参数
 * @param {string} data.mode 模式
 * @param {string} data.deviceId 设备id
 */
function deviceAction(data, callback){
    // mode: 1   //开机	 2关机  3冲洗  4取消冲洗  5复位滤芯  "Pram":[1,2] 滤芯级数
    var option = {
        url: 'http://devicecloud.dianqiukj.com/api/device/deviceAction',
        type: 'post',
        dataType: 'jsonp',
        jsonp: 'jsoncallback',
        success: function(res){
            console.log('deviceAction_res: ',res);
            callback(res);
        },
        error: function(err){
            console.log('deviceAction_err: ',err);
            alert('系统出错，请稍后再试');
        }
    }
    // 复位滤芯
    if(data.mode == 5){
        // 复位
        option['data'] = {
            mode: data.mode,
            deviceID: data.deviceId,
            data: data.filter // 几级滤芯（多个的时候用下划线分割‘_’）
        }
    }else{
        option['data'] = {
            mode: data.mode,
            deviceID: data.deviceId
        }
    }
    // 调用后端接口
    $.ajax(option);
}

/**
 * @description {绑定websocket}
 * @param {object}  data 参数
 * @param {string}  data.client_id: websocketid,
 * @param {string}  data.deviceID: 设备id,
 * @param {string}  data.token: '',
 * @return {function} callback 回调函数
 */
function bindWebsocket(data, callback){
    $.ajax({
        url: "http://devicecloud.dianqiukj.com/api/device/bind",
        type: 'get',
        dataType: 'jsonp',
        jsonp: 'jsoncallback',
        data: data,
        success: function(res){
            console.log('bindWebsocket_res: ',res);
            callback(res);
        },
        error: function(err){
            console.log('bindWebsocket_err: ',err);
            alert('系统出错，请稍后再试');
        }
    })
}