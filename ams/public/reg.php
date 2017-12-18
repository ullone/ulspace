<?php
  $cookie = '_zap=1fa026af-cdd0-4580-a130-86a7e8da7e2d; q_c1=f56e1b558b3249ea89356d39d33a5f9d|1507881479000|1502159631000; d_c0="AADCenPaowyPTvEMJHTK_ZWt0MTltvNRxWE=|1509934440"; q_c1=f56e1b558b3249ea89356d39d33a5f9d|1509934495000|1502159631000; aliyungf_tc=AQAAAECa92/JJQ0ASfoPt+4ND9u4Kyyo; _xsrf=2bf13ac82f80310499797146d4fb834e; r_cap_id="OTI1YWRlYzUxYjA4NGM0ODgyMjE5NWE3ODNhN2UyMmQ=|1512554925|6761cc365ed97c4f6a8c862121fa2a519a02044b"; cap_id="YmZlZGU1YWVkZWFiNDNjZmExOTkxYTFhMGFiNjcxY2Y=|1512554925|784d0c1336ed48520d991c88fb97b9f87863cd7c"; z_c0=Mi4xZUM5SkF3QUFBQUFBQU1KNmM5cWpEQmNBQUFCaEFsVk52UThWV3dDSGE4YTgyZ2pXcjJJNkg2NWlzR2s5YjE1SmlR|1512554941|ecec90d9e69dc6a6ae8d725ba9c6ffea291dbe5a; __utma=155987696.1153555567.1512555225.1512555225.1512555225.1; __utmb=155987696.0.10.1512555225; __utmc=155987696; __utmz=155987696.1512555225.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); _xsrf=2bf13ac82f80310499797146d4fb834e';
  $str = doCurl('https://www.zhihu.com', $cookie);
  $pattern = "'<\s*a\s.*?href\s*=\s*([\"\'])?(?(1) (.*?)\\1 | ([^\s\>]+))'isx";
  preg_match_all($pattern, $str, $match);
  $match = array_merge($match[2], $match[3]);
  $hrefs = array_flip(array_flip(array_filter($match)));
  foreach ($hrefs as $key => $href) {
      $url = 'https://www.zhihu.com';
      //若存在其他host，正则获取域名部分
      $reg = '/[a-z]*\.[a-z]*\.[a-z]*/';
      if(preg_match($reg, $href, $match)) {
        $url = 'https://'.$match[0];
        //正则替换掉域名部分，获取相对路径
        $href = preg_replace('/(https:\/\/){0,1}\/*[a-z]*\.{0,1}[a-z]*\.[a-z]*/i', '', $href);
      }
      $hrefs[$key] = formatUrl($href, $url);
  }
  return array_flip(array_flip(array_filter($hrefs)));

  function formatUrl($l1, $l2)
  {
      if (strlen($l1) > 0) {
          $I1 = str_replace([chr(34), chr(39)], '', $l1);
      } else {
          return $l1;
      }
      $url_parsed = parse_url($l2);
      $scheme = $url_parsed['scheme'];
      if ($scheme != '') {
          $scheme .= '://';
      }
      $host = $url_parsed['host'];
      $l3 = $scheme . $host;
      if (strlen($l3) == 0) {
          return $l1;
      }
      if(isset($url_parsed['path'])) {
        $path = dirname($url_parsed['path']);
        if ($path[0] == '\\') {
            $path = '';
        }
      } else $path = '';
      $pos = strpos($I1, '#');
      if ($pos > 0) {
          $I1 = substr($I1, 0, $pos);
      }
      //判断类型
      if (preg_match("/^(http|https|ftp):(\/\/|\\\\)(([\w\/\\\+\-~`@:%])+\.)+([\w\/\\\.\=\?\+\-~`@\':!%#]|(&)|&)+/i", $I1)) {
          return $I1;
      } elseif ($I1[0] == '/') {
          return $I1 = $l3 . $I1;
      } elseif (substr($I1, 0, 3) == '../') {
          //相对路径
          while (substr($I1, 0, 3) == '../') {
              $I1 = substr($I1, strlen($I1) - (strlen($I1) - 3), strlen($I1) - 3);
              if (strlen($path) > 0) {
                  $path = dirname($path);
              }
          }
          return $I1 = $path == '/' ? $l3 . $path . $I1 : $l3 . $path . "/" . $I1;
      } elseif (substr($I1, 0, 2) == './') {
          return $I1 = $l3 . $path . substr($I1, strlen($I1) - (strlen($I1) - 1), strlen($I1) - 1);
      } elseif (strtolower(substr($I1, 0, 7)) == 'mailto:' || strtolower(substr($I1, 0, 11)) == 'javascript:') {
          return false;
      } else {
          return $I1 = $l3 . $path . '/' . $I1;
      }
  }

  function doCurl($url = 'https://www.zhihu.com', $cookie) {
    $ch = curl_init($url);
    //初始化会话
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    //设置请求COOKIE
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//跳过ssl验证，当没有敏感信息时
    $result = curl_exec($ch);
    return $result;  //抓取的结果
  }

?>
