<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class backend extends JINGGA_Controller {
	
	function __construct(){
		parent::__construct();
		if(!$this->auth){
			$this->nsmarty->display('backend/login.html');
			exit;
		}
		$this->nsmarty->assign('acak', md5(date('H:i:s')) );
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
		$this->nsmarty->assign('table',$this->input->post('table'));
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
					$tingkat=$this->mbackend->getdata('cl_tingkatan','result_array');
					$group=$this->mbackend->getdata('cl_group_sekolah','result_array');
					$kat=$this->mbackend->getdata('cl_kategori','result_array');
					//$kelas=$this->mbackend->getdata('cl_kelas','get');
					$this->nsmarty->assign('tingkat',$tingkat);
					$this->nsmarty->assign('group',$group);
					$this->nsmarty->assign('kat',$kat);
					if($sts=='edit'){
						$data=$this->mbackend->getdata('tbl_buku','row_array');
						$this->nsmarty->assign('data',$data);
						//print_r($data);
					}
				break;
				
			}
			$this->nsmarty->assign('mod',$mod);
			$this->nsmarty->assign('temp',$temp);
		
			if(!file_exists($this->config->item('appl').APPPATH.'views/'.$temp)){$this->nsmarty->display('konstruksi.html');}
			else{$this->nsmarty->display($temp);}
		
	}
	function get_combo(){
		$mod=$this->input->post('v');
		$val=$this->input->post('v3');
		$bind=$this->input->post('v2');
		$data=$this->mbackend->getdata($mod,'result_array');
		$opt="<option value=''>--Pilih--</option>";
		
		foreach($data as $v){
			if($v['id']==$val)$sel="selected"; else $sel="";
			$opt .="<option value='".$v['id']."' ".$sel.">".$v['txt']."</option>";
		}
		echo $opt;
	}
	function simpandata($p1="",$p2=""){
		if($this->input->post('mod'))$p1=$this->input->post('mod');
		$post = array();
        foreach($_POST as $k=>$v){
			if($this->input->post($k)!=""){
				$post[$k] = $this->db->escape_str($this->input->post($k));
				//$post[$k] = $this->input->post($k);
			}
			
		}
		if(isset($post['editstatus'])){$editstatus = $post['editstatus'];unset($post['editstatus']);}
		else $editstatus = $p2;
		echo $this->mbackend->simpandata($p1, $post, $editstatus);
	}
}
