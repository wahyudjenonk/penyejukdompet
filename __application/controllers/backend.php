<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

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
		$filter=$this->combo_option($mod);
		$this->nsmarty->assign('mod',$mod);
		$this->nsmarty->assign('data_select',$filter);
		$this->nsmarty->assign('table',$this->input->post('table'));
		if(!file_exists($this->config->item('appl').APPPATH.'views/'.$temp)){$this->nsmarty->display('konstruksi.html');}
		else{$this->nsmarty->display($temp);}
	}
	
	function combo_option($mod){
		$opt="";
		switch($mod){
			case "produk":
				$opt .="<option value='A.judul_buku'>Judul Buku</option>";
				$opt .="<option value='A.deskripsi_buku'>Desc. Buku</option>";
				$opt .="<option value='B.kelas'>Kelas</option>";
				$opt .="<option value='C.nama_group'>Group</option>";
				$opt .="<option value='D.nama_kategori'>Kategori</option>";
			break;
			case "tingkatan":
				$opt .="<option value='A.tingkatan'>Tingkatan</option>";
				$opt .="<option value='A.deskripsi'>Desc. Tingkatan</option>";
			break;
			case "kelas":
				$opt .="<option value='B.tingkatan'>Tingkatan</option>";
				$opt .="<option value='A.kelas'>Kelas</option>";
			break;
			case "group_sekolah":
				$opt .="<option value='A.nama_group'>Group</option>";
			break;
			case "kategori":
				$opt .="<option value='A.nama_kategori'>Nama Kategori</option>";
				$opt .="<option value='A.deskripsi'>Deskripsi</option>";
			break;
			case "invoice":
				$opt .="<option value='A.no_order'>No Order</option>";
				$opt .="<option value='B.nama_sekolah'>Nama Sekolah</option>";
			break;
			case "konfirmasi":
				$opt .="<option value='A.konfirmasi_no'>No Konfirmasi</option>";
				$opt .="<option value='B.no_order'>No Order</option>";
				$opt .="<option value='C.nama_lengkap'>PIC</option>";
				$opt .="<option value='C.nama_sekolah'>Nama Sekolah</option>";
			break;
			case "gudang_konfirmasi":
			case "gudang_kirim":
				$opt .="<option value='A.no_gudang'>No Gudang</option>";
				$opt .="<option value='B.konfirmasi_no'>No Konfirmasi</option>";
				$opt .="<option value='C.no_order'>No Order</option>";
				$opt .="<option value='D.nama_lengkap'>PIC</option>";
				$opt .="<option value='D.nama_sekolah'>Nama Sekolah</option>";
			break;
			case "member_sekolah":
				$opt .="<option value='A.nama_lengkap'>Nama Lengkap</option>";
				$opt .="<option value='A.npsn'>NPSN</option>";
				$opt .="<option value='A.email'>Email</option>";
			break;
			case "member_umum":
				$opt .="<option value='A.nama_lengkap'>Nama Lengkap</option>";
				$opt .="<option value='A.email'>Email</option>";
			break;
			
		}
		return $opt;
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
				case "tingkatan":
					if($sts=='edit'){
						$data=$this->mbackend->getdata('cl_tingkatan','row_array');
						$this->nsmarty->assign('data',$data);
					}
				break;
				case "kelas":
					$tingkat=$this->mbackend->getdata('cl_tingkatan','result_array');
					$this->nsmarty->assign('tingkat',$tingkat);
					if($sts=='edit'){
						$data=$this->mbackend->getdata('cl_kelas','row_array');
						$this->nsmarty->assign('data',$data);
					}
				break;
				case "group_sekolah":
					if($sts=='edit'){
						$data=$this->mbackend->getdata('cl_group_sekolah','row_array');
						$this->nsmarty->assign('data',$data);
					}
				break;
				case "kategori":
					if($sts=='edit'){
						$data=$this->mbackend->getdata('cl_kategori','row_array');
						$this->nsmarty->assign('data',$data);
					}
				break;
				case "remark":
					$modul=$this->input->post('mod');
					$id=$this->input->post('id');
					$this->nsmarty->assign('modul',$modul);
					$this->nsmarty->assign('id',$id);
					//$id=$this
				break;
			}
			$this->nsmarty->assign('mod',$mod);
			$this->nsmarty->assign('temp',$temp);
		
			if(!file_exists($this->config->item('appl').APPPATH.'views/'.$temp)){$this->nsmarty->display('konstruksi.html');}
			else{$this->nsmarty->display($temp);}
		
	}
	function get_konten(){
		$mod=$this->input->post('mod');
		$this->nsmarty->assign('mod',$mod);
		$temp="backend/modul/".$mod.".html";
		switch($mod){
			case "gudang_konfirmasi":
			case "invoice":
				if($mod=='gudang_konfirmasi'){$temp="backend/modul/invoice.html";}
				$data=$this->mbackend->getdata('get_pemesanan','result_array');
				$this->nsmarty->assign('data',$data);
			break;
		}
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
	
	function cetak(){
		$mod=$this->input->post('mod');
			switch($mod){
				case "cetak_bast":
					$data=$this->mbackend->getdata('get_bast');
					$tgl=$this->konversi_tgl(date('Y-m-d'));
					$file_name=$data['header']['konfirmasi_no'];
					$this->hasil_output('pdf',$mod,$data,$file_name,'BERITA ACARA SERAH TERIMA BUKU',$data['header']['konfirmasi_no'],$tgl);
				break;
			}
	}
	function hasil_output($p1,$mod,$data,$file_name,$judul_header,$nomor="",$param=""){
		switch($p1){
			case "pdf":
				$this->load->library('mlpdf');	
				//$data=$this->mhome->getdata('cetak_voucher');
				$pdf = $this->mlpdf->load();
				$this->nsmarty->assign('param', $param);
				$this->nsmarty->assign('judul_header', $judul_header);
				$this->nsmarty->assign('nomor', $nomor);
				$this->nsmarty->assign('data', $data);
				$this->nsmarty->assign('mod', $mod);
				
				$htmlcontent = $this->nsmarty->fetch("backend/template/temp_pdf.html");
				$htmlheader = $this->nsmarty->fetch("backend/template/header.html");
				
				//echo $htmlcontent;exit;
				
				$spdf = new mPDF('', 'A4', 0, '', 12.7, 12.7, 33, 20, 5, 2, 'P');
				$spdf->ignore_invalid_utf8 = true;
				// bukan sulap bukan sihir sim salabim jadi apa prok prok prok
				$spdf->allow_charset_conversion = true;     // which is already true by default
				$spdf->charset_in = 'iso-8859-1';  // set content encoding to iso
				$spdf->SetDisplayMode('fullpage');		
				$spdf->SetHTMLHeader($htmlheader);
				//$spdf->keep_table_proportions = true;
				$spdf->useSubstitutions=false;
				$spdf->simpleTables=true;
				
				$spdf->SetHTMLFooter('
					<div style="font-family:arial; font-size:8px; text-align:center; font-weight:bold;">
						<table width="100%" style="font-family:arial; font-size:8px;">
							<tr>
								<td width="30%" align="left">
									
								</td>
								<td width="40%" align="center">
									
								</td>
								<td width="30%" align="right">
									Hal. {PAGENO} dari {nbpg}
								</td>
							</tr>
						</table>
					</div>
				');				
				//$file_name = date('YmdHis');
				$spdf->SetProtection(array('print'));				
				$spdf->WriteHTML($htmlcontent); // write the HTML into the PDF
				//$spdf->Output('repositories/Dokumen_LS/LS_PDF/'.$filename.'.pdf', 'F'); // save to file because we can
				//$spdf->Output('repositories/Billing/'.$filename.'.pdf', 'F');
				$spdf->Output($file_name.'.pdf', 'I'); // view file	
			break;
		}
	}
	function konversi_tgl($date){
		$this->load->helper('terbilang');
		$data=array();
		$timestamp = strtotime($date);
		$day = date('D', $timestamp);
		$day_angka = date('d', $timestamp);
		$month = date('m', $timestamp);
		$years = date('Y', $timestamp);
		switch($day){
			case "Mon":$data['hari']='Senin';break;
			case "Tue":$data['hari']='Selasa';break;
			case "Wed":$data['hari']='Rabu';break;
			case "Thu":$data['hari']='Kamis';break;
			case "Fri":$data['hari']='Jumat';break;
			case "Sat":$data['hari']='Sabtu';break;
			case "Sun":$data['hari']='Minggu';break;
		}
		switch($month){
			case "01":$data['bulan']='Januari';break;	
			case "02":$data['bulan']='Februari';break;	
			case "03":$data['bulan']='Maret';break;	
			case "04":$data['bulan']='April';break;	
			case "05":$data['bulan']='Mei';break;	
			case "06":$data['bulan']='Juni';break;	
			case "07":$data['bulan']='Juli';break;	
			case "08":$data['bulan']='Agustus';break;	
			case "09":$data['bulan']='September';break;	
			case "10":$data['bulan']='Oktober';break;	
			case "11":$data['bulan']='November';break;	
			case "12":$data['bulan']='Desember';break;	
		}
		$data['tahun']=ucwords(number_to_words($years));
		$data['tgl_text']=ucwords(number_to_words($day_angka));
		return $data;
	}
	function set_flag(){
		$mod=$this->input->post('mod');
		switch($mod){
			case "kirim_gudang":
				$sts='add';
				$data_konfirmasi=$this->mbackend->getdata('get_bast');
				$no_gudang=$this->mbackend->getdata('get_no_gudang');
				$data=array('tbl_konfirmasi_id'=>$data_konfirmasi['header']['id'],
							'tbl_h_pemesanan_id'=>$data_konfirmasi['header']['tbl_h_pemesanan_id'],
							'no_gudang'=>$no_gudang,
							'tgl_masuk'=>date('Y-m-d H:i:s'),
							'remark'=>$this->input->post('remark'),
							'flag'=>'P',
							'create_date'=>date('Y-m-d H:i:s'),
							'create_by'=>$this->auth['username']
				);
			break;
			case "set_packing":
				$sts='edit';
				$data=array('id'=>$this->input->post('id'),
							'flag'=>'PK',
							'packing_date'=>date('Y-m-d H:i:s'),
							'packing_by'=>$this->auth['username'],
							'packing_remark'=>$this->input->post('remark')
				);
			break;
			case "set_kirim":
				$sts='edit';
				$data=array('id'=>$this->input->post('id'),
							'flag'=>'F',
							'kirim_date'=>date('Y-m-d H:i:s'),
							'kirim_by'=>$this->auth['username'],
							'kirim_remark'=>$this->input->post('remark')
				);
			break;
			case "cancel_pesanan":
				$sts='edit';
				$data=array('id'=>$this->input->post('id'),
							'flag'=>'C',
							'cancel_date'=>date('Y-m-d H:i:s'),
							'cancel_by'=>$this->auth['username'],
							'cancel_remark'=>$this->input->post('remark')
				);
				echo $this->mbackend->simpandata('tbl_konfirmasi',$data,$sts);
				exit;
			break;
		}
		
		echo $this->mbackend->simpandata('tbl_gudang',$data,$sts);
	}
}
