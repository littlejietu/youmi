<?php
class Testt {
  // private $appId;
  // private $appSecret;
  public $appId;
  public $appSecret;

  public function __construct() {
  }

  public function init($a1, $b1){
  	$this->appId = $a1;
  	$this->appSecret = $b1;
  }

  public function abab(){
  	echo $this->appId.'----'.$this->appSecret;
  }

}