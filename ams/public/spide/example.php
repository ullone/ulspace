<?php
require("spide.php");

class Example {

  public function __construct() {
  }

  public function spide($seedUrls, $cfg) {
    if(!isset($seedUrls) || empty($seedUrls)) {
      echo "Failed , there is no urls to spide!";
      die();
    }
    $spide           = new Spide;
    $spide->seed     = $seedUrls;
    $spide->count    = isset($cfg['count']) ? $cfg['count'] : 5;
    $spide->interval = isset($cfg['interval']) ? $cfg['interval'] : 2;
    $spide->timeout  = isset($cfg['timeout']) ? $cfg['timeout'] : 10;
    $spide->name     = isset($cfg['name']) ? $cfg['name'] : 'spide';
    $spide->logFile  = isset($cfg['logFile']) ? $cfg['logFile'] : __DIR__ . "/" . date('Y-m-d H:i:s')."_spide.log";
    $spide->start();
  }
}

$seedUrls = ['https://www.zhihu.com',];
$example  = new Example();
$example->spide($seedUrls,[]);
