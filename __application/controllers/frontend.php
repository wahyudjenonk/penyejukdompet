<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class frontend extends JINGGA_Controller {
	
	function __construct(){
		parent::__construct();
	}
	
	function index(){
		$data_tingkatan = $this->mfrontend->getdata('cl_tingkatan', 'result_array');
		$this->nsmarty->assign('data_tingkatan', $data_tingkatan);		
		$this->nsmarty->assign('konten', 'beranda');		
		$this->nsmarty->display( 'frontend/main-index.html');		
	}
	
	function getdisplay($type="", $p1="", $p2="", $p3=""){
		switch($type){
			case "main_page":
				$data_tingkatan = $this->mfrontend->getdata('cl_tingkatan', 'result_array');
				$this->nsmarty->assign('data_tingkatan', $data_tingkatan);		
				$this->nsmarty->assign( 'konten', $p1);
				if(isset($p2)){
					$this->nsmarty->assign('param2', $p2);	
				}
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
					case "kategori":
						$temp = "frontend/modul/kategori-page.html";
						$id_tingkatan = $this->db->get_where('cl_tingkatan', array('tingkatan'=>strtoupper($p2)))->row_array();
						$data_buku = $this->mfrontend->getdata('data_buku_tingkatan', 'result_array', $id_tingkatan['id']);
						foreach($data_buku as $k=>$v){
							$data_buku[$k]['judul_buku'] = $this->lib->cutstring($v['judul_buku'], 20);
						}
						$data_tingkatan = $this->db->get('cl_tingkatan')->result_array();
						$data_kategori = $this->db->get('cl_kategori')->result_array();
						$data_pengguna = $this->db->get('cl_group_sekolah')->result_array();
						
						$this->nsmarty->assign('data_tingkatan', $data_tingkatan);
						$this->nsmarty->assign('data_kategori', $data_kategori);
						$this->nsmarty->assign('data_pengguna', $data_pengguna);
						$this->nsmarty->assign('nama_kategori', strtoupper($p2));
						$this->nsmarty->assign('data_buku', $data_buku);
					break;
				}		
				
				if(isset($temp)){
					$template = $this->nsmarty->fetch($temp);
				}else{
					$template = $this->nsmarty->fetch("konstruksi.html");
				}
				
				$array_page = array(
					'loadbalancedt' => md5('Ymd'),
					'loadbalancetm' => md5('H:i:s'),
					'loadtmr' => md5('YmdHis'),
					'page' => $template 
				);
				
				echo json_encode($array_page);
			break;
		}
	}
	
	
}
