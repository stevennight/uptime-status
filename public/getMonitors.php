<?php
$cacheFolder = __DIR__ . '/caches/';
$apiUrl = "https://api.uptimerobot.com/v2/getMonitors";
$apiKeys = [
    'blog' => "ur913496-7955aa06d4a313f72cd24709"
];

$data = json_decode(file_get_contents("php://input"), true);
if(!$data || !$apiKeys[$data['api_key']]){
    echo json_encode([
        "stat" => "fail",
        "error" => [
            "message" => "传参有误"
        ],
    ]);
    exit();
}
$data['api_key'] = $apiKeys[$data['api_key']];

//判断是否返回缓存
if(!file_exists($cacheFolder)){
    mkdir($cacheFolder);
}
$cacheFileName = $cacheFolder.$data['api_key'];
if(file_exists($cacheFileName)){
    $cacheTime = filemtime($cacheFileName);
    if(time() < $cacheTime + 60) { 	//缓存一分钟。
        echo file_get_contents($cacheFileName);
        exit();
    }
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$res = curl_exec($ch);
curl_close($ch);

//处理数据
$resArray = json_decode($res, true);
if($resArray['stat'] === "fail"){
    echo $res;
    exit();
}
foreach ($resArray['monitors'] as &$monitor){
    unset($monitor['url']);
    unset($monitor['port']);
    unset($monitor['http_password']);
    unset($monitor['http_username']);
}
$output = json_encode($resArray);
file_put_contents($cacheFileName,$output);
echo $output;