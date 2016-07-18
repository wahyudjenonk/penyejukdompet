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
				$zona_pilihan = $this->session->userdata('zonaxtreme');
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
						if(isset($zona_pilihan)){
							$flag_window = "oye"; // kgk nongol maning
						}else{
							$flag_window = "uye"; // nongol window pilihan zona nya
						}
						
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
						$this->nsmarty->assign('flag_window', $flag_window);
					break;
					case "combo_zona":
						$temp = "frontend/modul/combozona-page.html";
						$this->nsmarty->assign('combo_zona', $this->lib->fillcombo('cl_provinsi', 'return'));
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
						$estimasi = $this->db->get_where('cl_provinsi', array('kode_prov'=>$zona_pilihan['kode_provinsi']))->row_array();
						$harga_buku = number_format($data_buku['harga_zona_'.$zona_pilihan['zona_pilihan']],0,",",".");
						
						for($i = 1; $i <= 5; $i++){
							$data_buku['harga_zona_'.$i] = "Rp. ".number_format($data_buku['harga_zona_'.$i],0,",",".");
							$data_buku['harga_pemerintah_zona_'.$i] = "Rp. ".number_format($data_buku['harga_pemerintah_zona_'.$i],0,",",".");
						}
						
						$this->nsmarty->assign('zona', $zona_pilihan['zona_pilihan']);
						$this->nsmarty->assign('estimasi', $estimasi['estimasi_lama_pengiriman']);
						$this->nsmarty->assign('provinsi', $zona_pilihan['provinsi']);
						$this->nsmarty->assign('harga_buku', $harga_buku);
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
						foreach($data_cart as $key => $v){
							$data_cart[$key]['price'] = number_format($v['price'],0,",",".");
							$data_cart[$key]['subtotal'] = number_format($v['subtotal'],0,",",".");
						}
						$this->nsmarty->assign('data_cart', $data_cart);
					break;
					case "total_item";
						$data_cart = $this->cart->contents();
						$jumlah_item = count($data_cart);
						echo $jumlah_item;
						exit;
					break;
					case "checkout_belanja":
						$temp = "frontend/modul/checkout-page.html";
						$data_cart = $this->cart->contents();
						foreach($data_cart as $key => $v){
							$data_cart[$key]['price'] = number_format($v['price'],0,",",".");
							$data_cart[$key]['subtotal'] = number_format($v['subtotal'],0,",",".");
						}
						$this->nsmarty->assign('combo_jasa_pengiriman', $this->lib->fillcombo('cl_jasa_pengiriman', 'return') );
						$this->nsmarty->assign('combo_metode_pembayaran', $this->lib->fillcombo('cl_metode_pembayaran', 'return') );						
						$this->nsmarty->assign('data_cart', $data_cart);
					break;
					case "form_isian_checkout":
						$temp = "frontend/modul/isian_checkout-page.html";
						$type_form = $this->input->post('tpefrm');
						if($type_form = 'skull'){
							$npsn = $this->input->post('npp');
							$cek_data = $this->db->get_where('tbl_registrasi', array('npsn'=>$npsn) )->row_array();
						}elseif($type_form = 'umu'){
							$email = $this->input->post('em');
							$cek_data = $this->db->get_where('tbl_registrasi', array('email'=>$email) )->row_array();
						}
						
						if($cek_data){
							$this->nsmarty->assign('data', $cek_data);
							$this->nsmarty->assign('status_data', "ADA");
						}
						$this->nsmarty->assign('combo_prov', $this->lib->fillcombo('cl_provinsi', 'return', (isset($cek_data) ? $cek_data['cl_provinsi_kode'] : $zona_pilihan['kode_provinsi'] ) ));
						$this->nsmarty->assign('combo_kab', $this->lib->fillcombo('cl_kab_kota', 'return', (isset($cek_data) ? $cek_data['cl_kab_kota_kode'] : "" ), (isset($cek_data) ? $cek_data['cl_provinsi_kode'] : $zona_pilihan['kode_provinsi'] ) ));
						$this->nsmarty->assign('combo_kec', $this->lib->fillcombo('cl_kecamatan', 'return', (isset($cek_data) ? $cek_data['cl_kecamatan_kode'] : "" ), (isset($cek_data) ? $cek_data['cl_kab_kota_kode'] : "" ) ));						
						$this->nsmarty->assign('type_form', $type_form);
					break;
					case "combo_kab_kota":
						$v2 = $this->input->post('v2');
						echo $this->lib->fillcombo('cl_kab_kota', 'return', '', $v2);
						exit;
					break;
					case "combo_kecamatan":
						$v2 = $this->input->post('v2');
						echo $this->lib->fillcombo('cl_kecamatan', 'return', '', $v2);
						exit;
					break;
					case "finish_semuanya":
						$temp = "frontend/modul/finish-page.html";
					break;
					
					case "konfirmasi_pemb":
						$temp = "frontend/modul/konfirmasi-page.html";
					break;
					case "konfrom":
						$temp = "frontend/modul/konfirmasiform-page.html";
						$invno = $this->input->post('inv');
						$data_invoice = $this->mfrontend->getdata('header_pesanan', 'row_array', $invno);
						if($data_invoice){
							$data_customer = $this->db->get_where('tbl_registrasi', array('id'=>$data_invoice['tbl_registrasi_id']))->row_array();
							$this->nsmarty->assign('data_customer', $data_customer);
							if($data_invoice['status'] == 'F'){
								$data_konfirmasi = $this->db->get_where('tbl_konfirmasi', array('tbl_h_pemesanan_id'=>$data_invoice['id']))->row_array();
								$data_konfirmasi['total_pembayaran'] = number_format($data_konfirmasi['total_pembayaran'],0,",",".");
								$this->nsmarty->assign('data_konfirmasi', $data_konfirmasi);
							}
							$data_invoice['sub_total'] = number_format($data_invoice['sub_total'],0,",",".");
							$data_invoice['pajak'] = number_format($data_invoice['pajak'],0,",",".");
							$data_invoice['grand_total'] = number_format($data_invoice['grand_total'],0,",",".");
						}
						$this->nsmarty->assign('data_inv', $data_invoice);
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
		//$zona_pilihan = $this->session->userdata("zonaxtreme");
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
	
	function cruddata($type, $p1="", $p2=""){
		$post = array();
        foreach($_POST as $k=>$v){
			if($this->input->post($k)!=""){
				$post[$k] = $this->db->escape_str($this->input->post($k));
			}
		}
		
		if($type == 'session_zona'){
			$data_zona = $this->db->get_where('cl_provinsi', array('kode_prov'=>$post['kdprv']))->row_array();
			$sess = array();
			$sess['zona_pilihan'] = $data_zona['zona'];
			$sess['provinsi'] = $data_zona['provinsi'];
			$sess['kode_provinsi'] = $data_zona['kode_prov'];
			$this->session->set_userdata("zonaxtreme", $sess);
			echo 1;
		}else{
			if(isset($post['editstatus'])){$editstatus = $post['editstatus'];unset($post['editstatus']);}
			//else $editstatus = null;
			
			echo $this->mfrontend->simpansavedata($p1, $post, $editstatus);
		}
	}
	
	function test(){
		$this->session->sess_destroy();
		//$sess = $this->session->userdata("zonaxtreme");
		//echo $sess['zona_pilihan'];
		//$this->session->sess_destroy();
		/*
		$data_cart = $this->cart->contents();
		$array_batch = array();
		foreach($data_cart as $s => $r){
			$array_insert = array(
				'tbl_h_pemesanan_id' => 1,
				'tbl_buku_id' => $r['id'],
				'harga' => $r['price'],
				'qty' => $r['qty'],
				'subtotal' => $r['subtotal'],
				'create_date' => date('Y-m-d H:i:s'),
			);
			array_push($array_batch, $array_insert);	
			
			$data_cart[$s]['price'] = number_format($r['price'],0,",",".");
			$data_cart[$s]['subtotal'] = number_format($r['subtotal'],0,",",".");
		}
				
		$array_email = array(
			'pemesan' => "SD XXXX",
			'no_order' => "ORD-87978SSS",
			'tot' => "999",
			'pajak' => "888",
			'grand_total' => "777",
		);
		
		$this->nsmarty->assign('data_cart', $data_cart);
		$this->nsmarty->assign('penunjang', $array_email);
		$this->nsmarty->display('frontend/modul/email_invoice.html');
		
		
		$data_cart = $this->cart->contents();
		$array_email = array(
			'pemesan' => "SD XXXX",
			'no_order' => "ORD-87978SSS",
			'tot' => "700.000.000",
			'pajak' => "70.000.000",
			'grand_total' => "770.000.000",
		);
		foreach($data_cart as $s => $r){
			$data_cart[$s]['price'] = number_format($r['price'],0,",",".");
			$data_cart[$s]['subtotal'] = number_format($r['subtotal'],0,",",".");
		}
		
		*/
		$this->lib->kirimemail('email_konfirmasi', "triwahyunugroho11@gmail.com");
	}
	
}
