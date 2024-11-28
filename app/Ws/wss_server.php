<?php

use Swoole\WebSocket\Server;
use Swoole\WebSocket\Frame;

// 创建 Redis 客户端实例
$redis = new Redis();
$redis->connect('127.0.0.1', 6379); //端口号见Redis软件设置，默认是6379
$redis->auth('xxxx');  //这里如果redis设置里密码，则增加这项

// 创建 WebSocket 服务器
$ws = new Server("0.0.0.0", 8040);

// 设置服务器配置
$ws->set([
    'heartbeat_check_interval' => 60,
    'heartbeat_idle_time' => 600,
]);

// 监听 Worker 启动事件
$ws->on('WorkerStart', function (Server $server, int $workerId) {
    // 可以在这里做一些初始化工作
});

// 监听连接事件
$ws->on('open', function (Server $server, $request) use (&$redis) {
    // 当新用户连接时，记录用户信息
    $user_id = $request->get['user_id'] ?? null;
    $fd = $request->fd;
    if ($user_id) {
        // 将用户 ID 和 fd 存储到 Redis 哈希表
        //可以理解为类似数组中，online_users表示数组名称，$request->fd 表示key值，$user_id表示value值
        $redis->hSet('online_users', $request->fd, $user_id);
        //在服务器后端显示哪个用户上线了
        $msgs = [
            "from_user" => $user_id,    //显示消息是来自哪个用户
            "messages"=>"我上线啦！"
        ];
        //给所有用户广播谁上线了
        broadcast($server,$redis, json_encode($msgs),$request->fd);
        //var_dump($redis);
    } else {
        //echo "未提供用户 ID，fd: $fd\n";
    }
});

// 监听消息事件
$ws->on('message', function (Server $server, Frame $frame) use (&$redis) {
    //将收到的数据进行转换
    $data = json_decode($frame->data, true);
    if (isset($data['message'])) {
        $msgs = [
            //从哈希表中获取用户
            "from_user" => $redis->hGet('online_users',$frame->fd),     //显示消息是来自哪个用户
            "messages"=>$data['message']
        ];
        // 当用户发送消息时，广播给所有在线用户
        broadcast($server,$redis, json_encode($msgs), $frame->fd);
    }
});

// 监听关闭事件
$ws->on('close', function (Server $server, $fd) use (&$redis) {
    // 当用户断开连接时，从在线用户列表中移除该用户
    $msgs = [
        "from_user" => $redis->hGet('online_users',$fd),         //显示消息是来自哪个用户
        "messages"=>"我下线啦！"
    ];
    //从redis的哈希表中删除用户
    $redis->hDel('online_users', $fd);
    // 用户离线时，广播给所有在线用户
    broadcast($server,$redis, json_encode($msgs),$fd);
});


// 广播消息给所有在线用户
function broadcast(Server $server,$redis, $msg, $excludeFd = null) {
    // 从redis的哈希表中获取所有在线用户的 FD
    $onlineUsers = $redis->hKeys('online_users');
    // 遍历在线用户并发送消息
    foreach ($onlineUsers as $fds) {
        //判断不是当前用户，同时用户在线时推送信息
        if ($fds != $excludeFd && $server->isEstablished($fds)) {
            //给对应fds用户发送消息
            $server->push($fds, $msg);
        }
    }
}
// 启动服务器
$ws->start();