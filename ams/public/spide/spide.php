<?php
exit('haha');
//require('./tool.php');
require('Workerman/Worker.php');
//require('./Workerman/Lib/Timer.php');

class Spide {

  public  $id;              //worker实例id
  public  $name;            //worker实例名称
  public  $logFile;         //日志文件路径
  public  $count;           //worker进程数
  public  $interval;        //定时爬取时间间隔
  public  $timeout;         //爬取请求超时时间
  public  $seed;            //爬取起始网址
  public  $max = 100000;    //队列最大个数
  public  $timer_id;        //定时服务id
  public  $options = [];    //入队item参数
  public  $daemonize;
  public  $urlFilter = [
    '/^https:\/\/www\.zhihu\.com\/question\/\d{1,}\/answer\/d{1,}$/',
    '/^(https:\/\/)[a-z]{1,}\.zhihu\.com\/p\/(\d{1,})$/'
  ];                        //网址匹配规则

  private $queueArgs = [];  //队列规则参数
  private $worker;
  private $url;             //正在处理的url
  private $page;            //从URL爬取的正在处理的页面内容
  private $queues;          //队列实例
  private $commands;
  private $hooks = [
      'startWorkerHooks',
      'beforeDownloadPageHooks',
      'downloadPageHooks',
      'afterDownloadPageHooks',
      'discoverUrlHooks',
      'afterDiscoverHooks',
      'stopWorkerHooks',
  ];                        //爬取网页要执行的一系列的钩子函数

  public function __construct($config = []) {
    global $argv;
    $this->commands = $argv;

    $this->setQueue();
    $this->setLog([$this, 'fileLog']);
    $this->setDownloader();
  }

  //爬虫启动函数
  public function start() {
    if(!isset($this->commands)) {
      $this->daemonize = false;
    }

    if($this->daemonize) {
      //启动守护进程
      $this->check();

      $worker = new Worker;
      $worker->count = $this->count;
      $worker->name  = $this->name;//worker实例名称
      $worker->onWorkerStart = [$this, 'onWorkerStart'];
      $worker->onWorkerStop  = [$this, 'onWorkerStop'];
      $this->worker = $worker;

      Worker::$daemonize = true;
      Worker::$stdoutFile = $this->logFile;

      $this->queueArgs['name'] = $this->name;
      $this->initHooks();
      $this->command();
      self::run();
    } else {
      $this->initHooks();
      $this->seed = (array) $this->seed;
      while (count($this->seed)) {
          $this->crawler();
      }
    }
  }

  // worker进程启动时执行
  public function onWorkerStart() {
    foreach($this->startWorkerHooks as $hook) {
      call_user_func($hook, $this);
    }
  }

  //worker进程停止时执行
  public function onWorkerStop() {
    foreach($this->stopWorkerHooks as $hook) {
      call_user_func($hook, $this);
    }
  }

  //初始化一系列钩子函 数并规定执行顺序
  public function initHooks() {
    //写日志
    $this->startWorkerHooks[] = function ($spide) {
        $spide->id = $spide->worker->id;
        $spide->log("Beanbun worker {$spide->id} is starting ...");
    };

    //自定义处理函数
    if ($this->startWorker) {
        $this->startWorkerHooks[] = $this->startWorker;
    }
    //设置队列大小
    $this->startWorkerHooks[] = function ($spide) {
        $spide->queue()->maxQueueSize = $spide->max;
        //定时调用一次$this->hooks里的函数，一次爬取五条url
        $spide->timer_id = Spide::timer($spide->interval, [$spide, 'crawler']);
    };

    $this->beforeDownloadPageHooks[] = [$this, 'defaultBeforeDownloadPage'];

    if($this->beforeDownloadPage) {
      $this->beforeDownloadPageHooks[] = $this->beforeDownloadPage;
    }

    if($this->downloadPage) {
      $this->downloadPageHooks[] = $this->downloadPage;
    } else {
      $this->downloadPageHooks[] = [$this, 'defaultDownloadPage'];
    }

    if($this->afterDownloadPage) {
      $this->afterDownloadPageHooks[] = $this->afterDownloadPage;
    }

    if($this->discoverUrl) {
      $this->discoverUrlHooks[] = $this->discoverUrl;
    } else {
      $this->discoverUrlHooks[] = [$this, 'defaultDiscoverUrl'];
    }

    if($this->afterDiscover) {
      $this->afterDiscoverHooks[] = $this->afterDiscover;
    }

    if($this->daemonize) {
      $this->afterDiscoverHooks[] = function ($spide) {
        if($spide->options['reserve'] == false) {
          $spide->queue()->queued($spide->queue);
        }
      };
    }

    if($this->stopWorker) {
      $this->stopWorkerHooks[] = $this->stopWorker;
    }
  }

  public static function timer($interval, $callBack, $args = [], $persistent = true) {
    /*
    * 调用php页面运行时间检测类，定时执行某个函数或者类方法。
    * @param $interval   执行间隔
    * @param $callback   执行函数，若是类的方法，必须是类的公有方法
    * @param $args       回调函数的参数，必须是数组
    * @param $persistent 是否持久，若只想执行一次则传false,只执行一次的任务在执行完毕后会自动销毁，不必调用Timer::del()
    */
    reuturn Timer::add($interval, $callBack, $args, $persistent);
  }

  public static function timerDel($timer_id) {
    Timer::del($timer_id);
  }

  public static function run() {
    Worker::runAll();
  }

  //命令行控制
  public function command() {
    switch($this->commands[1]) {
      case 'start' :
        foreach($this->seed as $url) {
          if(is_string($url)) {
            $this->queue()->add($url);
          } elseif(is_array($url)) {
            $this->queue()->add($url[0], $url[1]);
          }
        }
        $this->queues = null;//初始化
        $this->log('Spide is starting');
        break;

      case 'clean':
        $this->queue->clean();
        die();
        break;
      case 'stop':
        break;
      default:
        break;
    }
  }

  private function fileLog($msg, $spide) {
    $myFile  = fopen($this->lofFile, "a") or die ('open file failed!');
    $content = date('Y-m-d H:i:s')."$spide->name : $msg\n";
    fwrite($myFile, $content);
    fclose($myFile);
  }

  public function log($msg) {
    call_user_func($this->logFactory, $msg, $this);
  }

  public function setLog($callBack = null) {
    $this->logFactory = $callBack === null ? function($msg, $spide) {
      echo date('Y-m-d H:i:s')."$spide->name : $msg\n";
    } : $callBack;
  }

  //非守护进程下执行，一次爬取五条
  public function crawler() {
    $allHooks = $this->hooks;
    array_shift($allHooks);
    array_pop($allHooks);

    foreach ($allHooks as $hooks) {//爬取5条记录
        foreach ($this->$hooks as $hook) {//完成爬取一条记录的整个流程
            call_user_func($hook, $this);
        }
    }

    // $this->queue = '';
    // $this->url = '';
    // $this->method = '';
    // $this->page = '';
    // $this->options = [];
  }

  public function defaultBeforeDownloadPage() {
    if($this->daemonize) {
      if($this->max > 0 && $this->queue()->queuedCount() >= $this->max) {
        $this->log("Download the url failed for the url has touched the upper limit;The spide worker $this->id has stopped!\n");
        self::timerDel($this->timer_id);
        exit('download upper limit');
      }
      $this->queue = $queue = $this->queue()->next();
    } else {
      $queue = array_shift($this->seed);
    }

    if(is_null($queue) || !$queue) {
      sleep(30);
      exit('queue is empty');
    }

    if(!is_array($queue)) {
      $this->queue = $queue = array(
        'url' => $queue,
        'options' => [],
      );
    }

    $options = array_merge([
        'reserve' => false,
        'timeout' => $this->timeout,
    ], (array) $queue['options']);

    if($this->daemonize && !$options['reserve'] && $this->queue()->isQueued($queue)) {
      exit('error');
    }

    $this->url     = $queue['url'];
    $this->options = $options;
  }

  public function defaultDownloadPage() {
    $this->page = $this->downloader();
    if($this->page) {
      $worker_id = isset($this->id) ? $this->id : '';
      $this->log("Beanbun worker {$worker_id} download {$this->url} success.");
    } else {
      exit('download page failed!');
    }
  }

  public function defaultDiscoverUrl() {
    $countUrlFilter = count($this->urlFilter);

    $urls = Tool::getUrlByHtml($this->page, $this->url);

    if($countUrlFilter > 0) {
      foreach($urls as $url) {
        foreach($this->urlFilter as $pattern) {
          if(preg_match($pattern, $url)) {
            $this->queue()->add($url);
          }
        }
      }
    } else {
      foreach($urls as $url) {
        $this->queue()->add($url);
      }
    }
  }

  public function setDownloader($callBack = null) {
    $this->downloadFactory = ($callBack === null) ? [$this, 'downloadPage'] : $callBack;
  }

  public function downloader() {
    call_user_func($this->downloadFactory, $this->url);
  }

  private function downloadPage() {
    $cookie = Tool::config()['cookie'];
    return Tool::doCurl($this->url, $cookie);
  }

  public function queue() {
    if($this->queues) {
      $this->queues = call_user_func($this->queueFactory, $this->queueArgs);
    }
    return $this->queues;
  }

  public function setQueue($arr = array('algorithm' => 'lp', 'name' => $this->name)) {
    $this->queueFactory = function($arr) {
      return new RedisQueue($arr);
    }
    $this->queueArgs = $arr;
  }

  public function check()
  {
      $error = false;
      $text = '';
      $version_ok = $pcntl_loaded = $posix_loaded = true;
      if (!version_compare(phpversion(), "5.3.3", ">=")) {
          $text .= "PHP Version >= 5.3.3                 \033[31;40m [fail] \033[0m\n";
          $error = true;
      }

      if (!in_array("pcntl", get_loaded_extensions())) {
          $text .= "Extension posix check                \033[31;40m [fail] \033[0m\n";
          $error = true;
      }

      if (!in_array("posix", get_loaded_extensions())) {
          $text .= "Extension posix check                \033[31;40m [fail] \033[0m\n";
          $error = true;
      }

      $check_func_map = array(
          "stream_socket_server",
          "stream_socket_client",
          "pcntl_signal_dispatch",
      );

      if ($disable_func_string = ini_get("disable_functions")) {
          $disable_func_map = array_flip(explode(",", $disable_func_string));
      }

      foreach ($check_func_map as $func) {
          if (isset($disable_func_map[$func])) {
              $text .= "\033[31;40mFunction " . implode(', ', $check_func_map) . "may be disabled. Please check disable_functions in php.ini\033[0m\n";
              $error = true;
              break;
          }
      }

      if ($error) {
          echo $text;
          exit;
      }
  }

  public function shutdown()
  {
      $master_pid = is_file(Worker::$pidFile) ? file_get_contents(Worker::$pidFile) : 0;
      $master_pid && posix_kill($master_pid, SIGINT);
      $timeout = 5;
      $start_time = time();
      while (1) {
          $master_is_alive = $master_pid && posix_kill($master_pid, 0);
          if ($master_is_alive) {
              if (time() - $start_time >= $timeout) {
                  exit;
              }
              usleep(10000);
              continue;
          }
          exit(0);
          break;
      }
  }
}
