<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class backend extends MY_Controller {
	
	function __construct(){
		parent::__construct();
		if(!$this->auth){
			return $this->nsmarty->display('backend/login.html');
			exit;
		}
		$this->temp="backend/";
		$this->load->model('mbackend');
	}
	
	function index(){
		//print_r($this->auth);exit;
		
		if($this->auth){
			$this->nsmarty->display( 'backend/main-index.html');
		}else{
			$this->nsmarty->display( 'backend/login.html');
		}
	}
	function get_grid($mod){
		$temp=$this->temp.'modul/grid.html';
		$this->nsmarty->assign('mod',$mod);
		//$this->nnsmarty->assign('table',$table);
		if(!file_exists($this->config->item('appl').APPPATH.'views/'.$temp)){$this->nsmarty->display('konstruksi.html');}
		else{$this->nsmarty->display($temp);}
	}
	function getdata($p1,$p2="",$p3=""){
		echo $this->mbackend->getdata($p1,'json',$p3);
	}
	function get_form($mod){
			$temp=$this->temp.'form/'.$mod.".html";
			$sts=$this->input->post('editstatus');
			$this->nsmarty->assign('sts',$sts);
			switch($mod){
				case "produk":
					//$kelas=$this->mbackend->getdata('cl_kelas','get');
					//$this->nsmarty->assign('kelas',$kelas);
					if($sts=='edit'){
						$data=$this->mbackend->getdata('cl_mata_pelajaran');
						$this->nsmarty->assign('data',$data);
					}
				break;
				
			}
			$this->nsmarty->assign('mod',$mod);
			$this->nsmarty->assign('temp',$temp);
		
			if(!file_exists($this->config->item('appl').APPPATH.'views/'.$temp)){$this->nsmarty->display('konstruksi.html');}
			else{$this->nsmarty->display($temp);}
		
	}
	
}
