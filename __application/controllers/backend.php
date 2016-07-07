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
						$foto=$this->mbackend->getdata('tbl_foto_buku','result_array');
						$this->nsmarty->assign('data',$data);
						$this->nsmarty->assign('foto',$foto);
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
	function upload(){
		//print_r($_POST);exit;
		//echo microtime();exit;
		$t = microtime(true);
		$micro = sprintf("%06d",($t - floor($t)) * 1000000);
		$d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
		$mod=$this->input->post('mod');
		$data=array('create_date'=>date('Y-m-d H:i:s'),
					'create_by'=>$this->auth['username']
		);
		switch($mod){
			case "tbl_foto_buku":
				$id=$this->input->post('tbl_buku_id');
				
				$upload_path='__repository/produk/';
				$data['tbl_buku_id']=$id;
				$tbl="tbl_foto_buku";
				
				$object='file_nya';
				if(!file_exists($upload_path))mkdir($upload_path, 0777, true);
				$upload_path .=$this->input->post('tingkat')."/";
				if(!file_exists($upload_path))mkdir($upload_path, 0777, true);
				$upload_path .=$this->input->post('kelas')."/";
				if(!file_exists($upload_path))mkdir($upload_path, 0777, true);
				$upload_path .=$this->input->post('group')."/";
				if(!file_exists($upload_path))mkdir($upload_path, 0777, true);
				$upload_path .=$this->input->post('kat')."/";
				if(!file_exists($upload_path))mkdir($upload_path, 0777, true);
				if(isset($_FILES['file_nya'])){
					$file=$_FILES['file_nya']['name'];
					$nameFile =$d->format("YmdHisu");// $this->string_sanitize(pathinfo($file, PATHINFO_FILENAME));
						$upload=$this->lib->uploadnong($upload_path, $object, $nameFile);
						if($upload){
							$data['foto_buku']=$upload;
							echo $this->mbackend->simpandata($tbl,$data,'add');
						}else{
							echo 2;
						}
				}
			break;
			
		}
		
		
		
		//echo $upload;
	}
	function hapus_file(){
		if($this->auth){
			$mod=$this->input->post('mod');
			switch($mod){
				case "foto_buku":
					$data=$this->mbackend->getdata('tbl_foto_buku','row_array');
					if(isset($data['foto_buku'])){
						$path='__repository/produk/'.$data['tingkatan'].'/'.$data['kelas'].'/'.$data['nama_group'].'/'.$data['nama_kategori'].'/';
						chmod($path.$data['foto_buku'],0777);
						unlink($path.$data['foto_buku']);
						echo $this->mbackend->simpandata('tbl_foto_buku',$data,'delete');
					}
				break;
			}
		}
	}
}
