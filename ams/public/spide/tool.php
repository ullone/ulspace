<?php

class Tool {
  public static function config() {
    $cfgPath = __DIR__ . '/config.php';
    if(file_exists($cfgPath)) $contents = 'hehe';
      // $contents = require($cfgPath);
    return $contents ? $contents : [];
  }

  // public static function doCurl($url, $cookie) {
  //   $ch = curl_init($url);
  //   //初始化会话
  //   curl_setopt($ch, CURLOPT_HEADER, 0);
  //   curl_setopt($ch, CURLOPT_COOKIE, $cookie);
  //   //设置请求COOKIE
  //   curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
  //   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //   //将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
  //   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  //   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//跳过ssl验证，当没有敏感信息时
  //   $result = curl_exec($ch);
  //   return $result;  //抓取的结果
  // }

  // public static function getUrlByHtml($html, $url) {
  //   $pattern = "'<\s*a\s.*?href\s*=\s*([\"\'])?(?(1) (.*?)\\1 | ([^\s\>]+))'isx";
  //   preg_match_all($pattern, $str, $match);
  //   $match = array_merge($match[2], $match[3]);
  //   $hrefs = array_flip(array_flip(array_filter($match)));
  //   foreach ($hrefs as $key => $href) {
  //       $url = 'https://www.zhihu.com';
  //       //若存在其他host，正则获取域名部分
  //       $reg = '/[a-z]*\.[a-z]*\.[a-z]*/';
  //       if(preg_match($reg, $href, $match)) {
  //         $url = 'https://'.$match[0];
  //         //正则替换掉域名部分，获取相对路径
  //         $href = preg_replace('/(https:\/\/){0,1}\/*[a-z]*\.{0,1}[a-z]*\.[a-z]*/i', '', $href);
  //       }
  //       $hrefs[$key] = self::formatUrl($href, $url);
  //   }
  //   return array_flip(array_flip(array_filter($hrefs)));
  // }

  // public static function formatUrl($l1, $l2) {
  //   if (strlen($l1) > 0) {
  //       $I1 = str_replace([chr(34), chr(39)], '', $l1);
  //   } else {
  //       return $l1;
  //   }
  //   $url_parsed = parse_url($l2);
  //   $scheme = $url_parsed['scheme'];
  //   if ($scheme != '') {
  //       $scheme .= '://';
  //   }
  //   $host = $url_parsed['host'];
  //   $l3 = $scheme . $host;
  //   if (strlen($l3) == 0) {
  //       return $l1;
  //   }
  //   if(isset($url_parsed['path'])) {
  //     $path = dirname($url_parsed['path']);
  //     if ($path[0] == '\\') {
  //         $path = '';
  //     }
  //   } else $path = '';
  //   $pos = strpos($I1, '#');
  //   if ($pos > 0) {
  //       $I1 = substr($I1, 0, $pos);
  //   }
  //   //判断类型
  //   if (preg_match("/^(http|https|ftp):(\/\/|\\\\)(([\w\/\\\+\-~`@:%])+\.)+([\w\/\\\.\=\?\+\-~`@\':!%#]|(&)|&)+/i", $I1)) {
  //       return $I1;
  //   } elseif ($I1[0] == '/') {
  //       return $I1 = $l3 . $I1;
  //   } elseif (substr($I1, 0, 3) == '../') {
  //       //相对路径
  //       while (substr($I1, 0, 3) == '../') {
  //           $I1 = substr($I1, strlen($I1) - (strlen($I1) - 3), strlen($I1) - 3);
  //           if (strlen($path) > 0) {
  //               $path = dirname($path);
  //           }
  //       }
  //       return $I1 = $path == '/' ? $l3 . $path . $I1 : $l3 . $path . "/" . $I1;
  //   } elseif (substr($I1, 0, 2) == './') {
  //       return $I1 = $l3 . $path . substr($I1, strlen($I1) - (strlen($I1) - 1), strlen($I1) - 1);
  //   } elseif (strtolower(substr($I1, 0, 7)) == 'mailto:' || strtolower(substr($I1, 0, 11)) == 'javascript:') {
  //       return false;
  //   } else {
  //       return $I1 = $l3 . $path . '/' . $I1;
  //   }
  // }
}
