<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class mbackend extends CI_Model{
	function __construct(){
		parent::__construct();
		$this->auth = unserialize(base64_decode($this->session->userdata('4ld33334zzzzzzt')));
	}
	
	function getdata($type="", $balikan="", $p1="", $p2="",$p3="",$p4=""){
		$where = " WHERE 1=1 ";
		if($this->input->post('key')){
				$where .=" AND ".$this->input->post('kat')." like '%".$this->db->escape_str($this->input->post('key'))."%'";
		}
		switch($type){
			case "detil_invoice":
				$data=array();
				$sql="SELECT A.*,B.zona 
						FROM tbl_d_pemesanan A
						LEFT JOIN tbl_h_pemesanan B ON A.tbl_h_pemesanan_id=B.id 
						WHERE A.id=".$this->input->post('id');
				$data['detil']=$this->db->query($sql)->row_array();
				$sql="SELECT A.*,A.harga_zona_".$data['detil']['zona']." as harga,A.judul_buku as text 
						FROM tbl_buku A ";
				$data['buku']=$this->db->query($sql)->result_array();
				return $data;
				
			break;
			case "tbl_komplain":
				$sql="SELECT A.*,B.no_order 
					  FROM tbl_komplain A 
					  LEFT JOIN tbl_h_pemesanan B ON A.tbl_h_pemesanan_id=B.id ".$where;
			break;
			case "buku_laris":
				$sql="SELECT B.judul_buku,sum(A.qty)as jml 
						FROM tbl_d_pemesanan A
						LEFT JOIN tbl_buku B ON A.tbl_buku_id=B.id
						GROUP BY B.judul_buku
						ORDER BY sum(A.qty) DESC
						limit 0,10";
			break;
			case "admin":
				if($balikan=='row_array'){
					$where .=" AND id=".$this->input->post('id');
				}
				$sql = "
					SELECT *
					FROM admin ".$where;				
			break;
			
			case "get_lap_rekap":
				$tgl_mulai=$this->input->post('tgl_mulai');
				$tgl_akhir=$this->input->post('tgl_akhir');
				$kat=$this->input->post('kat');
				$mod=$this->input->post('mod');
				if($mod=='rekap_penjualan_SEKOLAH' || $mod=='rekap_penjualan_UMUM'){
					$sql="SELECT A.*,B.nama_sekolah,B.nama_kepala_sekolah as pic,B.npsn,B.nama_lengkap,
							B.alamat_pengiriman,B.no_telp_sekolah,B.no_hp_kepsek,B.email,E.kab_kota,
							C.provinsi,F.jml_buku,G.total_pembayaran
							FROM tbl_h_pemesanan A
							LEFT JOIN tbl_registrasi B ON A.tbl_registrasi_id=B.id
							LEFT JOIN cl_provinsi C ON B.cl_provinsi_kode=C.kode_prov
							LEFT JOIN cl_kab_kota E ON B.cl_kab_kota_kode=E.kode_kab_kota
							LEFT JOIN (
								SELECT A.tbl_h_pemesanan_id,SUM(A.qty)as jml_buku 
								from tbl_d_pemesanan A 
								LEFT JOIN tbl_h_pemesanan B ON A.tbl_h_pemesanan_id=B.id
								LEFT JOIN tbl_registrasi C ON B.tbl_registrasi_id=C.id
								WHERE C.jenis_pembeli='".$kat."' 
								AND B.create_date BETWEEN '".$tgl_mulai."' AND '".$tgl_akhir." 23:59:00'
								GROUP BY A.tbl_h_pemesanan_id
							)AS F ON F.tbl_h_pemesanan_id=A.id
							LEFT JOIN tbl_konfirmasi G ON G.tbl_h_pemesanan_id=A.id
							WHERE B.jenis_pembeli='".$kat."' 
							AND A.create_date BETWEEN '".$tgl_mulai."' AND '".$tgl_akhir." 23:59:00'";
					//echo $sql;
				}else if($mod=='detil_penjualan_SEKOLAH' || $mod=='detil_penjualan_UMUM'){
					$sql="SELECT A.tbl_h_pemesanan_id,SUM(A.qty)as jml_buku,
							CONCAT(C.nama_sekolah,' [',C.npsn,']')as sekolah,C.nama_lengkap,
							B.no_order,B.sub_total,B.pajak,B.grand_total,B.id as id_header  
							from tbl_d_pemesanan A 
							LEFT JOIN tbl_h_pemesanan B ON A.tbl_h_pemesanan_id=B.id 
							LEFT JOIN tbl_registrasi C ON B.tbl_registrasi_id=C.id 
							WHERE C.jenis_pembeli='".$kat."' 
							AND B.tgl_order BETWEEN '".$tgl_mulai."' AND '".$tgl_akhir." 23:59:00'
							GROUP BY A.tbl_h_pemesanan_id ";
							
					$data=array();
					$res=$this->db->query($sql)->result_array();
					if(count($res)>0){
						foreach($res as $x=>$v){
							$data[$x]=array();
							$data[$x]['no_order']=$v['no_order'];
							$data[$x]['sekolah']=$v['sekolah'];
							$data[$x]['nama_lengkap']=$v['nama_lengkap'];
							$data[$x]['sub_total']=$v['sub_total'];
							$data[$x]['pajak']=$v['pajak'];
							$data[$x]['grand_total']=$v['grand_total'];
							$data[$x]['jml_buku']=$v['jml_buku'];
							$sql="SELECT A.*,B.no_order,CONCAT(D.nama_sekolah,' (',D.npsn,')')as sekolah,D.nama_lengkap,C.judul_buku
									FROM tbl_d_pemesanan A
									LEFT JOIN tbl_h_pemesanan B ON A.tbl_h_pemesanan_id=B.id
									LEFT JOIN tbl_buku C ON A.tbl_buku_id=C.id
									LEFT JOIN tbl_registrasi D ON B.tbl_registrasi_id=D.id
									WHERE D.jenis_pembeli='".$kat."' AND A.tbl_h_pemesanan_id=".$v['id_header'];
							$det=$this->db->query($sql)->result_array();
							//print_r($det);exit;
							if(count($det)>0){
								$data[$x]['detil']=$det;
							}
							
						}
					}
					//echo "<pre>";print_r($data);exit;
					return $data;
				}else if($mod=='lap_bast_SEKOLAH' || $mod=='lap_bast_UMUM'){
					$sql="SELECT A.*,D.nama_sekolah,D.nama_lengkap,D.npsn,B.no_konfirmasi,B.total_pembayaran,C.no_order,C.grand_total
							FROM tbl_bast A
							LEFT JOIN tbl_konfirmasi B ON A.tbl_konfirmasi_id=B.id
							LEFT JOIN tbl_h_pemesanan C ON B.tbl_h_pemesanan_id=C.id
							LEFT JOIN tbl_registrasi D ON C.tbl_registrasi_id=D.id
							WHERE D.jenis_pembeli='".$kat."' 
							AND A.create_date BETWEEN '".$tgl_mulai."' AND '".$tgl_akhir." 23:59:00'";
					
				}else if($mod=='lap_kwitansi_SEKOLAH' || $mod=='lap_kwitansi_UMUM'){
					$sql="SELECT A.*,D.nama_sekolah,D.nama_lengkap,D.npsn,B.no_konfirmasi,B.total_pembayaran,C.no_order,C.grand_total
							FROM tbl_kwitansi A
							LEFT JOIN tbl_konfirmasi B ON A.tbl_konfirmasi_id=B.id
							LEFT JOIN tbl_h_pemesanan C ON B.tbl_h_pemesanan_id=C.id
							LEFT JOIN tbl_registrasi D ON C.tbl_registrasi_id=D.id
							WHERE D.jenis_pembeli='".$kat."' 
							AND A.create_date BETWEEN '".$tgl_mulai."' AND '".$tgl_akhir." 23:59:00'";
				}else if($mod=='dashboard_penjualan'){
					$sql="SELECT A.*,B.nama_sekolah 
						  FROM tbl_h_pemesanan A 
						  LEFT JOIN tbl_registrasi B ON A.tbl_registrasi_id=B.id
						  WHERE B.jenis_pembeli='SEKOLAH' 
						  ORDER BY A.tgl_order DESC
						  LIMIT 0,10
					";
				}else if($mod=='dashboard_penjualan_umum'){
					$sql="SELECT A.*,B.nama_lengkap 
						  FROM tbl_h_pemesanan A 
						  LEFT JOIN tbl_registrasi B ON A.tbl_registrasi_id=B.id
						  WHERE B.jenis_pembeli='UMUM' 
						  ORDER BY A.tgl_order DESC
						  LIMIT 0,10
					";
				}else if($mod=='dashboard_penjualan_zona'){
					$sql='SELECT CONCAT("Zona ",A.zona)as zona,SUM(A.grand_total)as total
							FROM tbl_h_pemesanan A 
							WHERE A.zona='.$p1.'
							GROUP BY A.zona
					';
				}
			break;
			case "tbl_monitor":
				$sql="SELECT A.no_order,A.`status` as status_order,B.flag as status_konfirmasi,
						C.flag as status_gudang,D.`status` as status_kirim,D.no_resi
						FROM tbl_h_pemesanan A
						LEFT JOIN (
							SELECT A.tbl_h_pemesanan_id,A.flag,A.id FROM tbl_konfirmasi A
						)AS B ON B.tbl_h_pemesanan_id=A.id
						LEFT JOIN (
							SELECT A.tbl_h_pemesanan_id,A.tbl_konfirmasi_id,A.flag 
							FROM tbl_gudang A
						)AS C ON (C.tbl_h_pemesanan_id=A.id AND C.tbl_konfirmasi_id=B.id)
						LEFT JOIN (
							SELECT A.tbl_h_pemesanan_id,A.`status`,A.no_resi 
							FROM tbl_tracking_pengiriman A
						)AS D ON D.tbl_h_pemesanan_id=A.id ".$where;
			break;
			case "tbl_registrasi":
			case "tbl_registrasi_umum":
				if($type=="tbl_registrasi")$where .=" AND A.jenis_pembeli='SEKOLAH'";
				else $where .=" AND A.jenis_pembeli='UMUM'";
				$sql="SELECT A.*,CONCAT('PROV. ',B.provinsi,', ',C.kab_kota,', KEC. ',D.kecamatan)as prov_kota
					FROM tbl_registrasi A
					LEFT JOIN cl_provinsi B ON A.cl_provinsi_kode=B.kode_prov
					LEFT JOIN cl_kab_kota C ON A.cl_kab_kota_kode=C.kode_kab_kota
					LEFT JOIN cl_kecamatan D ON A.cl_kecamatan_kode=D.kode_kecamatan 
					".$where;
			break;
			case "get_no_gudang":
				$sql=" SELECT *  FROM tbl_gudang ";
				$res=$this->db->query($sql)->result_array();
				if(count($res)>0){
					$sql=" SELECT max(no_gudang)+1 as nogud_baru  FROM tbl_gudang ";
					$qry=$this->db->query($sql)->row();
					$id=$qry->nogud_baru;
				}else{
					$id=1;
				}
				if($id<10){$id_baru='0000'.$id;}
				if($id<100 && $id >=10){$id_baru='000'.$id;}
				if($id<1000 && $id >=100){$id_baru='00'.$id;}
				if($id<10000 && $id >=1000){$id_baru='0'.$id;}
				return $id_baru;
			break;
			case "tbl_gudang":
				$sql="SELECT A.*,B.no_konfirmasi,B.tgl_konfirmasi,B.total_pembayaran,
						C.no_order,C.tgl_order,C.zona,D.nama_sekolah,D.nama_lengkap,D.jenis_pembeli,
						C.id as id_pemesanan,D.nama_kepala_sekolah,D.alamat_pengiriman,E.jasa_pengiriman  
						FROM tbl_gudang A 
						LEFT JOIN tbl_konfirmasi B ON A.tbl_konfirmasi_id=B.id
						LEFT JOIN tbl_h_pemesanan C ON (A.tbl_h_pemesanan_id=C.id AND B.tbl_h_pemesanan_id=C.id)
						LEFT JOIN tbl_registrasi D ON C.tbl_registrasi_id=D.id
						LEFT JOIN cl_jasa_pengiriman E ON C.cl_jasa_pengiriman_id=E.id
						".$where." 
						 ORDER BY A.tgl_masuk DESC"; 
			break;
			case "tbl_konfirmasi":
				
				$sql="SELECT A.*,B.no_order,B.tgl_order,B.zona,C.nama_sekolah,C.nama_lengkap,
						B.id as id_pemesanan,C.jenis_pembeli,C.nama_kepala_sekolah 
						FROM tbl_konfirmasi A 
						LEFT JOIN tbl_h_pemesanan B ON A.tbl_h_pemesanan_id=B.id
						LEFT JOIN tbl_registrasi C ON B.tbl_registrasi_id=C.id
					  ".$where."
					  ORDER BY A.tgl_konfirmasi DESC";
			
			break;
			case "get_bast":
				$data=array();
				$id=$this->input->post('id');
				if($id)$where .=" AND A.id=".$id;
				$sql="SELECT A.*,B.no_order,B.tgl_order,B.zona,C.nama_sekolah,
						C.nama_kepala_sekolah,C.nip,C.npsn,C.alamat_pengiriman,
						C.no_telp_sekolah,C.email,C.no_hp_kepsek,D.no_bast
						FROM tbl_konfirmasi A 
						LEFT JOIN tbl_h_pemesanan B ON A.tbl_h_pemesanan_id=B.id
						LEFT JOIN tbl_registrasi C ON B.tbl_registrasi_id=C.id
						LEFT JOIN tbl_bast D ON D.tbl_konfirmasi_id=A.id
					  ".$where;
				$data['header']=$this->db->query($sql)->row_array();
				$sql="SELECT A.*,B.judul_buku,(A.qty*A.harga)as total,E.kelas,F.nama_group
					  FROM tbl_d_pemesanan A 
					  LEFT JOin tbl_buku B ON A.tbl_buku_id=B.id
					  LEFT JOIN tbl_h_pemesanan C ON A.tbl_h_pemesanan_id=C.id
					  LEFT JOIN tbl_konfirmasi D ON D.tbl_h_pemesanan_id=C.id
					  LEFT JOIN cl_kelas E ON B.cl_kelas_id=E.id
					  LEFT JOIN cl_group_sekolah F ON B.cl_group_sekolah=F.id
					  WHERE  D.id=".$id;
				$data['detil']=$this->db->query($sql)->result_array();
				return $data;
			break;
			case "get_pemesanan":
				$data=array();
				$id=$this->input->post('id');
				if($id)$where .=" AND A.id=".$id;
				$sql="SELECT A.*,B.nama_sekolah,B.nama_lengkap,B.jenis_pembeli 
					  FROM tbl_h_pemesanan A 
					  LEFT JOIN tbl_registrasi B ON A.tbl_registrasi_id=B.id ".$where;
				$data['header']=$this->db->query($sql)->row_array();
				$sql="SELECT A.*,B.judul_buku,(A.qty*A.harga)as total
					  FROM tbl_d_pemesanan A 
					  LEFT JOin tbl_buku B ON A.tbl_buku_id=B.id
					  WHERE A.tbl_h_pemesanan_id=".$id;
				$data['detil']=$this->db->query($sql)->result_array();
				return $data;
			break;
			case "data_login":
				$sql = "
					SELECT *
					FROM admin
					WHERE username = '".$p1."'
				";				
			break;
			case "tbl_buku":
				if($balikan=='row_array'){
					$where .=" AND A.id=".$this->input->post('id');
				}
				/*if($this->input->post('key')){
					$where .=" AND ".$this->input->post('kat')." like '%".$this->db->escape_str($this->input->post('key'))."%'";
				}*/
				$sql="SELECT A.*,B.kelas,C.nama_group,D.nama_kategori,E.id as id_tingkatan,E.tingkatan 
					FROM tbl_buku A
					LEFT JOIN cl_kelas B ON A.cl_kelas_id=B.id
					LEFT JOIN cl_group_sekolah C ON A.cl_group_sekolah=C.id
					LEFT JOIN cl_kategori D ON A.cl_kategori_id=D.id 
					LEFT JOIN cl_tingkatan E ON B.cl_tingkatan_id=E.id ".$where;
					//echo $sql;
			break;
			case "tbl_h_pemesanan":
			case "tbl_h_pemesanan_umum":
				if($type=='tbl_h_pemesanan')$where .=" AND B.jenis_pembeli='SEKOLAH'";
				if($type=='tbl_h_pemesanan_umum')$where .=" AND B.jenis_pembeli='UMUM'";
				
				$sql="SELECT A.*,B.nama_sekolah,B.nama_lengkap 
					  FROM tbl_h_pemesanan A 
					  LEFT JOIN tbl_registrasi B ON A.tbl_registrasi_id=B.id 
					  ".$where."
					  ORDER BY A.tgl_order DESC";
			
			break;
			case "cl_tingkatan":
				if($balikan=='row_array'){
					$where .=" AND A.id=".$this->input->post('id');
				}
				/*if($this->input->post('key')){
					$where .=" AND ".$this->input->post('kat')." like '%".$this->db->escape_str($this->input->post('key'))."%'";
				}*/
				$sql="SELECT A.*,A.tingkatan as text FROM cl_tingkatan A  ".$where;
				//echo $sql;
			break;
			case "cl_group_sekolah":
				if($balikan=='row_array'){
					$where .=" AND A.id=".$this->input->post('id');
				}
				/*if($this->input->post('key')){
					$where .=" AND ".$this->input->post('kat')." like '%".$this->db->escape_str($this->input->post('key'))."%'";
				}*/
				$sql="SELECT A.*,A.nama_group as text 
					  FROM cl_group_sekolah A ".$where;
			break;
			case "cl_kategori":
				if($balikan=='row_array'){
					$where .=" AND A.id=".$this->input->post('id');
				}
				/*if($this->input->post('key')){
					$where .=" AND ".$this->input->post('kat')." like '%".$this->db->escape_str($this->input->post('key'))."%'";
				}*/
				$sql="SELECT A.*,A.nama_kategori as text
					  FROM cl_kategori A ".$where;
			break;
			case "cl_edisi":
				if($balikan=='row_array'){
					$where .=" AND A.id=".$this->input->post('id');
				}
				/*if($this->input->post('key')){
					$where .=" AND ".$this->input->post('kat')." like '%".$this->db->escape_str($this->input->post('key'))."%'";
				}*/
				$sql="SELECT A.*,A.nama_edisi as text
					  FROM cl_edisi A ".$where;
			break;
			case "cl_kelas":
				if($balikan=='row_array'){
					$where .=" AND A.id=".$this->input->post('id');
				}
				if($balikan=='result_array'){
					$where .=" AND A.cl_tingkatan_id=".$this->input->post('v2');
				}
				/*if($this->input->post('key')){
					$where .=" AND ".$this->input->post('kat')." like '%".$this->db->escape_str($this->input->post('key'))."%'";
				}*/
				$sql="SELECT A.*, B.tingkatan,kelas as txt 
					  FROM cl_kelas A 
					  LEFT JOIN cl_tingkatan B ON A.cl_tingkatan_id=B.id ".$where;
			break;
			case "tbl_foto_buku":
				if($balikan=='row_array'){
					 $where .=" AND A.id=".$this->input->post('id');
				}else{
					 $where .=" AND A.tbl_buku_id=".$this->input->post('id');
				}
				$sql="SELECT A.*,C.kelas,D.nama_group,E.nama_kategori,F.tingkatan 
					FROM tbl_foto_buku A 
					LEFT JOIN tbl_buku B ON A.tbl_buku_id=B.id
					LEFT JOIN cl_kelas C ON B.cl_kelas_id=C.id
					LEFT JOIN cl_group_sekolah D ON B.cl_group_sekolah = D.id
					LEFT JOIN cl_kategori E ON B.cl_kategori_id=E.id 
					LEFT JOIN cl_tingkatan F ON C.cl_tingkatan_id=F.id ".$where;
			break;
			case "l_kelas":
				$filter=$this->input->post('v2');
				$sql="SELECT id as id,kelas as txt FROM cl_kelas ".$where;
				if($filter){$sql .=" AND cl_tingkatan_id=".$filter;}
				else{ $sql .=" AND cl_tingkatan_id=-1";}
				//return $this->result_query($sql);
			break;
		}
		
		if($balikan == 'json'){
			return $this->lib->json_grid($sql);
		}elseif($balikan == 'row_array'){
			return $this->db->query($sql)->row_array();
		}elseif($balikan == 'result'){
			return $this->db->query($sql)->result();
		}elseif($balikan == 'result_array'){
			return $this->db->query($sql)->result_array();
		}
		
	}
	
	function get_combo($type="", $p1="", $p2=""){
		switch($type){
			case "cl_kategori":
			break;
			case "c_kelas":
				$sql = "
					SELECT id, kelas as txt
					FROM 
				";
			break;
		}
		
		return $this->db->query($sql)->result_array();
	}
	
	function simpandata($table,$data,$sts_crud){ //$sts_crud --> STATUS NYEE INSERT, UPDATE, DELETE
		$this->db->trans_begin();
		if(isset($data['id'])){
			$id = $data['id'];
			unset($data['id']);
		}
		
		switch($table){
			
			case "admin":
				//print_r($data);exit;
				if($sts_crud=='add')$data['password']=$this->encrypt->encode($data['password']);
				if(!isset($data['status'])){$data['status']=0;}
			break;
			case "tbl_buku":
				//print_r($data);exit;
				unset($data['tingkatan']);
				if($sts_crud=='delete'){
					$data_foto=$this->getdata('tbl_foto_buku','result_array');
					if(count($data_foto)>0){
						foreach($data_foto as $v){
							if(isset($v['foto_buku'])){
								$path='__repository/produk/'.$v['tingkatan'].'/'.$v['kelas'].'/'.$v['nama_group'].'/'.$v['nama_kategori'].'/';
								chmod($path.$v['foto_buku'],0777);
								unlink($path.$v['foto_buku']);
								$this->mbackend->simpandata('tbl_foto_buku',$v,'delete');
							}
						}
					}
				}
			break;
			case "cl_group_sekolah":
			case "cl_kelas":
			case "cl_edisi":
			
				$data['create_date']=date('Y-m-d H:i:s');
				$data['create_by']=$this->auth['username'];
			break;
			case "cl_kategori":
				if($sts_crud=='add'){
					$data['tgl_buat']=date('Y-m-d H:i:s');
					$data['user_create']=$this->auth['username'];
				}else if($sts_crud=='edit'){
					$data['tgl_update']=date('Y-m-d H:i:s');
					$data['user_update']=$this->auth['username'];
				}
			break;
			case "tbl_gudang":
				if($this->input->post('mod')=='kirim_gudang'){
					$sql="UPDATE tbl_konfirmasi set flag='F' WHERE id=".$data['tbl_konfirmasi_id'];
					$this->db->query($sql);
				}else if($this->input->post('mod')=='set_kirim'){
					$sql="SELECT * FROM tbl_gudang where id=".$id;
					$id_pemesanan=$this->db->query($sql)->row('tbl_h_pemesanan_id');
					$data_kirim=array('tbl_h_pemesanan_id'=>$id_pemesanan,
									  'no_resi'=>$this->input->post('no_resi'),
									  'status'=>'F',
									  'create_date'=>date('Y-m-d H:i:s'),
									  'create_by'=>$this->auth['username']
					);
					$this->db->insert('tbl_tracking_pengiriman',$data_kirim);
				}
			break;
			case "tbl_konfirmasi":
				$sql="SELECT * FROM tbl_konfirmasi WHERE id=".$id;
				$id_pemesanan=$this->db->query($sql)->row('tbl_h_pemesanan_id');
				$sql="UPDATE tbl_h_pemesanan SET status='C' WHERE id=".$id_pemesanan;
				$this->db->query($sql);
			break;
		}
		
		switch ($sts_crud){
			case "add":
				$this->db->insert($table,$data);
				if($table=="tbl_buku"){
					$id=$this->db->insert_id();
				}
			break;
			case "edit":
				$this->db->update($table, $data, array('id' => $id) );
			break;
			case "delete":
				$this->db->delete($table, array('id' => $id));
			break;
		}
		
		if($this->db->trans_status() == false){
			$this->db->trans_rollback();
			return 0;
		} else{
			if($table=="tbl_buku"){
				if($sts_crud !='delete'){
					$this->db->trans_commit();
					$js=array('msg'=>1,'data'=>$id);
					return json_encode($js);
				}else{
					return $this->db->trans_commit();
				}
			}else{
				return $this->db->trans_commit();
			}
		}
	
	}
	
}
