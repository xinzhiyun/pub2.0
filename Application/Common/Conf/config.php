<?php
return array(
	/* 数据库设置 */
    'DB_TYPE'                => 'mysql', // 数据库类型
    'DB_PORT'                => '3306', // 端口
    'DB_PREFIX'              => '', // 数据库表前缀
    'DB_PARAMS'              => array(), // 数据库连接参数
    'DB_DEBUG'               => true, // 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDS_CACHE'        => true, // 启用字段缓存
    'DB_CHARSET'             => 'utf8', // 数据库编码默认采用utf8
    'DB_DEPLOY_TYPE'         => 0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE'         => false, // 数据库读写是否分离 主从式有效
    'DB_MASTER_NUM'          => 1, // 读写分离后 主服务器数量
    'DB_SLAVE_NO'            => '', // 指定从服务器序号


    //定义特定的数据连接 云端配置
    'SUPERVISE_DB' => array(
        'DB_TYPE' => 'mysql',
        'DB_USER' => 'root',
        'DB_PWD' =>  'root',
        'DB_HOST' => '120.27.12.1',
        'DB_PORT' => '3306',
        'db_name' => 'devicecloud',
        'db_charset' => 'utf8',
        'DB_PREFIX' => '',    // 数据库表前缀
    ),
);
