<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class frontend extends JINGGA_Controller {
	
	function __construct(){
		parent::__construct();
		
	}
	
	function index(){
		$this->nsmarty->display( 'frontend/main-index.html');		
	}
}
