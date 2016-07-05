<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class frontend extends JINGGA_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library('cart');
	}
	
	function index(){
		//$data_tingkatan = $this->mfrontend->getdata('cl_tingkatan', 'result_array');
		//$this->nsmarty->assign('data_tingkatan', $data_tingkatan);
		
		$this->nsmarty->assign('konten', 'beranda');		
		$this->nsmarty->display( 'frontend/main-index.html');		
	}
	
	function getdisplay($type="", $p1="", $p2="", $p3=""){
		switch($type){
			case "main_page":
				//$data_tingkatan = $this->mfrontend->getdata('cl_tingkatan', 'result_array');
				//$this->nsmarty->assign('data_tingkatan', $data_tingkatan);		
				
				$data_cart = $this->cart->contents();
				$jumlah_item = count($data_cart);
				
				$this->nsmarty->assign( 'tot_item', $jumlah_item );
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
					case "katalog":
						$temp = "frontend/modul/katalog-page.html";
						$id_tingkatan = $this->db->get_where('cl_tingkatan', array('tingkatan'=>strtoupper($p2)))->row_array();
						$data_buku = $this->mfrontend->getdata('data_buku', 'result_array', $id_tingkatan['id']);
						foreach($data_buku as $k=>$v){
							$data_buku[$k]['judul_buku'] = $this->lib->cutstring($v['judul_buku'], 20);
						}
						$data_tingkatan = $this->db->get('cl_tingkatan')->result_array();
						$array_tingkatan = array();
						foreach($data_tingkatan as $k => $v){
							$array_tingkatan[$k]['tingkatan'] = $v['tingkatan'];
							$array_tingkatan[$k]['detil'] = array();
							$getkelas = $this->db->get_where('cl_kelas', array('cl_tingkatan_id'=>$v['id']))->result_array();
							foreach($getkelas as $t => $p){
								$array_tingkatan[$k]['detil'][$t]['nama_kelas'] = "Kelas ".$p['kelas'];
							}
						}
						$data_pengguna = $this->db->get('cl_group_sekolah')->result_array();
						$data_kategori = $this->db->get('cl_kategori')->result_array();
						
						$this->nsmarty->assign('data_tingkatan', $array_tingkatan);
						$this->nsmarty->assign('data_pengguna', $data_pengguna);
						$this->nsmarty->assign('data_kategori', $data_kategori);
						$this->nsmarty->assign('data_buku', $data_buku);
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
					case "detail_produk":
						$id = $this->input->post('isds');
						$temp = "frontend/modul/produkwindow-page.html";
						$data_buku = $this->db->get_where('tbl_buku', array('id'=>$id))->row_array();
						
						$this->nsmarty->assign('combo_buku', $this->lib->fillcombo('zona_pengiriman', 'return'));
						$this->nsmarty->assign('data_buku', $data_buku);
					break;
					case "zona_pengiriman":
						$zona = $this->input->post('znpn');
						if($zona == ''){
							echo "Pilih Zona Pengiriman";
							exit;
						}
						
						$id = $this->input->post('iix');
						$data_harga = $this->mfrontend->getdata('zona_pengiriman', 'row_array', $id, $zona);
						$word = "Rp. ".number_format($data_harga['harga_zona_'.$zona],0,",",".");
						
						echo $word;
						exit;
					break;
					case "keranjangnya":
						$temp = "frontend/modul/keranjangbelanja-page.html";
						$data_cart = $this->cart->contents();
						$this->nsmarty->assign('data_cart', $data_cart);
					break;
				}		
				
				if(isset($temp)){
					$template = $this->nsmarty->fetch($temp);
				}else{
					$template = $this->nsmarty->fetch("konstruksi.html");
				}
				
				//echo $template;exit;
				
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
	
	function keranjang_belanja($type){
		switch($type){
			case "add":
				$id = $this->input->post('iipx');
				$zona = $this->input->post('zn');
				$qty = $this->input->post('yqt');
				$harga = $this->mfrontend->getdata('zona_pengiriman', 'row_array', $id, $zona);
				$data_buku = $this->db->get_where('tbl_buku', array('id'=>$id) )->row_array();
				$data_cart = $this->cart->contents();
				$flag = true;
				
				if($data_cart){
					foreach ($data_cart as $item) {
						if ($item['id'] == $id) {
							$qtyd = $item['qty'] + $qty;
							$data_update = array(
								'rowid' => $item['rowid'],
								'qty' => $qtyd,
								'price' => $harga['harga_zona_'.$zona],
							);
							echo $this->cart->update($data_update);
							$flag = false;
						}
					}
				}
				
				if($flag){
					$data_cart = array(
						'id' => $id,
						'qty' => $qty,
						'price' => $harga['harga_zona_'.$zona],
						'name' => $data_buku['judul_buku'],
						'options' => array('jml_hal' => $data_buku['jml_hal'])
					);
					echo $this->cart->insert($data_cart);
				}
			break;
			case "view":
				$kontent = $this->cart->contents();
				
				echo "<pre>";
				print_r($kontent);exit;
			break;
			case "destroy":
				$this->cart->destroy();
			break;
			
		}
	}
	
	
}
