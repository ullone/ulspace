<?php

require('tool.php');

class RedisQueue {

  public $redis;                  //实例化的redis对象
  private $config;                //redis连接配置

  private $algorithm = 'lp';      //出队列时，lp即lpop表示从头部删除元素，rp即rpop
  private $key;                   //待处理url队列名称
  private $queuedKey;             //已处理URL队列名称
  private $maxQueueSize = 10000;  //队列最大item数10000

  public function __construct($cfg) {
    $this->config    = Tool::config();
    $this->name      = $cfg['name'];
    $this->key       = $cfg['name']."Queue";
    $this->queuedKey = $cfg['name']."Queued";
    $this->algorithm = $cfg['algorithm'];
    //spide实例入队
    $this->getInstance()->sAdd('spide', $this->name);
  }

  public function getInstance() {
    if(!$this->redis) {
      $this->redis = new \Redis();
      $this->redis->connect($this->config['host'], $this->config['port']);
      $this->redis->auth($this->config['password']);
    }
    return $this->redis;
  }

  //任务加入队列
  public function add ($url, $options = []) {
    if(!$url || ($this->count() >= $this->maxQueueSize))
      return ;
    $queue = serialize([
      'url' => $url,
      'options' => $options
    ]);
    //判断该项内容是否已经在队列中
    if($this->isQueued($queue))
      return ;
    if(!isset($options['reserve']) || $options['reserve'] == false)
      $this->getInstance->rpush($this->key, $queue);
    else
      $this->getInstance->lpush($this->key, $queue);
  }

  public function isQueued($queue) {
    //判断任务是否已经执行过
    return $this->getInstance->sIsMember($this->queuedKey, $queue);
  }

  //任务弹出队列
  public function next() {
    if($this->getInstance->lsize($this->key) <= 0)
      return ;
    if($this->algorithm = 'lp')
      $queue = $this->getInstance->lpop();
    else
      $queue = $this->getInstance->rpop();

    //判断若任务已执行过，则取下一个任务，否则返回任务内容
    if($this->isQueued($queue))
      return $this->next;
    else return unserialize($queue);
  }

  //下载完成后，将任务添加到已下载队列中
  public function queued($queue) {
    $this->getInstance->sAdd($queue, serialize($queue));
  }

  public function count() {
    return $this->getInstance->lsize($this->key);
  }

  public function queuedCount() {
    return $this->getInstance->lSize($this->queuedKey);
  }

  public function clean() {
    $this->getInstance->delete($this->key);
    $this->getInstance->delete($this->queuedKey);
  }

}
