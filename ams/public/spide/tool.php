<?php

class Tool {

  public function __construct() {

  }

  public function index() {
    echo "haha";die;
  }
}

$test = new Tool();
$test->index();
