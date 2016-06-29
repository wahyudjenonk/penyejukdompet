<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class backend extends MY_Controller {
	
	function __construct(){
		parent::__construct();
		
	}
	
	function index(){
		//print_r($this->auth);exit;
		
		if($this->auth){
			$this->nsmarty->display( 'backend/main-index.html');
		}else{
			$this->nsmarty->display( 'backend/login.html');
		}
	}
	
	
	
	
}
