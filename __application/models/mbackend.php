<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class mbackend extends CI_Model{
	function __construct(){
		parent::__construct();
		$this->auth = unserialize(base64_decode($this->session->userdata('4ld33334zzzzzzt')));
	}
	
	function getdata($type="", $balikan="", $p1="", $p2="",$p3="",$p4=""){
		$where = " WHERE 1=1 ";
		
		switch($type){
			case "data_login":
				$sql = "
					SELECT *
					FROM admin
					WHERE username = '".$p1."'
				";				
			break;
			case "cl_tingkatan":
				$sql="SELECT A.*,A.tingkatan as text
					FROM cl_tingkatan A ";
			break;
			case "cl_group_sekolah":
				$sql="SELECT A.*,A.nama_group as text
					FROM cl_group_sekolah A ";
			break;
			case "cl_kategori":
				$sql="SELECT A.*,A.nama_kategori as text
					FROM cl_kategori A ";
			break;
			case "tbl_buku":
				if($balikan=='row_array'){
					$where .=" AND A.id=".$this->input->post('id');
				}
				$sql="SELECT A.*,B.kelas,C.nama_group,D.nama_kategori,E.id as id_tingkatan,E.tingkatan 
					FROM tbl_buku A
					LEFT JOIN cl_kelas B ON A.cl_kelas_id=B.id
					LEFT JOIN cl_group_sekolah C ON A.cl_group_sekolah=C.id
					LEFT JOIN cl_kategori D ON A.cl_kategori_id=D.id 
					LEFT JOIN cl_tingkatan E ON B.cl_tingkatan_id=E.id ".$where;
					//echo $sql;
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
					LEFT JOIN cl_group_sekolah D ON B.cl_group_sekolah=d.id
					LEFT JOIN cl_kategori E ON B.cl_kategori_id=E.id 
					LEFT JOIN cl_tingkatan F ON C.cl_tingkatan_id=F.id ".$where;
			break;
			case "cl_kelas":
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
