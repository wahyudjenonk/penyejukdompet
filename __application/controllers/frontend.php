<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class frontend extends JINGGA_Controller {
	
	function __construct(){
		parent::__construct();
	}
	
	function index(){
		$this->nsmarty->assign( 'konten', 'beranda');		
		$this->nsmarty->display( 'frontend/main-index.html');		
	}
	
	function getdisplay($type="", $p1="", $p2="", $p3=""){
		switch($type){
			case "main_page":
				$this->nsmarty->assign( 'konten', $p1);		
				$this->nsmarty->display( 'frontend/main-index.html');	
			break;
			case "loading_page":
				switch($p1){
					case "beranda":
						$temp = "frontend/modul/beranda-page.html";
					break;
					case "tentangkami":
						$temp = "frontend/modul/tentangkami-page.html";
					break;
					case "kontak":
						$temp = "frontend/modul/kontak-page.html";
					break;
				}		
				
				if(isset($temp)){
					$template = $this->nsmarty->fetch($temp);
				}else{
					$template = $this->nsmarty->fetch("konstruksi.html");
				}
				
				$array_page = array(
					'page' => $template 
				);
				
				echo json_encode($array_page);
			break;
		}
	}
	
	
}
