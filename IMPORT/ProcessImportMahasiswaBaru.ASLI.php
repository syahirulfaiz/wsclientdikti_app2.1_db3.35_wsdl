<?php
//UPDATE 20171018 : TAMBAH KOLOM SAMAKAN DENGAN WS BARU.
/*
yang BELUM (!!!):
-id_jns_tinggal
-id_alat_transport
-skhun
-no_peserta_ujian
-a_pernah_paud
-a_pernah_tk
-sert_prof
-a_pindah_mhs_asing
-id_pt_asal
-id_prodi_asal
-id_jalur_masuk
-id_pembiayaan

*/
//GANTI $this->proxy .... $proxy
//GANTI $this->session('idsp')....$id_sp
//GANTI $this->token .... $token
//GANTI print_r(xxx,true);

include('../initsoap.php');

$nama_file_LOG = $_REQUEST['nama_file_LOG'];

$row = $_REQUEST['row'];

$data = json_decode(stripslashes($_POST['key']),true); // Pakai TRUE agar return array bersih

$kode_prodi = $data['kode_prodi']; 
$mulai_smt = $data['mulai_smt'];

$path_directory_LOG = '../LOG/'.$mulai_smt.'/'.$kode_prodi;

if (!file_exists($path_directory_LOG)) {
    mkdir($path_directory_LOG, 0777, true);
}

$location_of_LOG=$path_directory_LOG."/".$mulai_smt."-".$kode_prodi."-".$nama_file_LOG;

if ($row==0) $file_LOG = fopen($location_of_LOG, "w") or die("Unable to open file!");

$LOG =""; // start of LOG

$table = 'mahasiswa';
$table1 = 'mahasiswa_pt';			


$nm_pd= $data['nm_pd']; 
$tmpt_lahir= $data['tmpt_lahir'];
$jk= $data['jk']; 
$tgl_lahir= date('Y-m-d',strtotime($data['tgl_lahir']));
$id_agama= $data['id_agama']; 
$nm_ibu_kandung= $data['nm_ibu_kandung']; 
$nik= $data['nik'];
$kewarganegaraan= $data['kewarganegaraan'];
$ds_kel= $data['ds_kel']; 
$id_wil= $data['id_wil']; 
$id_kebutuhan_khusus_ayah= $data['id_kebutuhan_khusus_ayah']; 
$id_kebutuhan_khusus_ibu= $data['id_kebutuhan_khusus_ibu']; 
$id_kk= $data['id_kk']; 
$a_terima_kps= $data['a_terima_kps']; 

$nipd = $data['nipd']; 
$id_jns_daftar =$data['id_jns_daftar'];
$tgl_masuk_sp = date('Y-m-d',strtotime($data['tgl_masuk_sp']));

$sks_diakui= $data['sks_diakui']; 
$nisn= $data['nisn']; 
$jln= $data['jln'];
$rt= $data['rt']; 
$rw= $data['rw']; 
$nm_dsn= $data['nm_dsn']; 
$kode_pos= $data['kode_pos']; 
$no_tel_rmh= $data['no_tel_rmh']; 
$no_hp= $data['no_hp']; 
$email = $data['email']; 

$no_kps = $data['no_kps'];
$npwp = $data['npwp']; 

$nm_ayah= $data['nm_ayah']; 
$nik_ayah= $data['nik_ayah']; 
$tgl_lahir_ayah= date('Y-m-d',strtotime($data['tgl_lahir_ayah']));
$id_jenjang_pendidikan_ayah= $data['id_jenjang_pendidikan_ayah'];

$filter_id_jenjang_pendidikan_ayah = "nm_jenj_didik ilike '%".trim($id_jenjang_pendidikan_ayah)."%' ";
	$temp_filter_id_jenjang_pendidikan_ayah = $proxy->GetRecord($token,'jenjang_pendidikan',$filter_id_jenjang_pendidikan_ayah);
	if($temp_filter_id_jenjang_pendidikan_ayah['result']){
		$id_jenjang_pendidikan_ayah = $temp_filter_id_jenjang_pendidikan_ayah['result']['id_jenj_didik'];	
	}
	
$id_pekerjaan_ayah= $data['id_pekerjaan_ayah']; 

$filter_id_pekerjaan_ayah = "nm_pekerjaan ilike '%".trim($id_pekerjaan_ayah)."%' ";
	$temp_filter_id_pekerjaan_ayah = $proxy->GetRecord($token,'pekerjaan',$filter_id_pekerjaan_ayah);
	if($temp_filter_id_pekerjaan_ayah['result']){
		$id_pekerjaan_ayah = $temp_filter_id_pekerjaan_ayah['result']['id_pekerjaan'];	
	}	

$id_penghasilan_ayah= $data['id_penghasilan_ayah']; 

$filter_id_penghasilan_ayah = "batas_atas <= ".$id_penghasilan_ayah." AND ".$id_penghasilan_ayah." <= batas_bawah";
	$temp_filter_id_penghasilan_ayah = $proxy->GetRecord($token,'penghasilan',$filter_id_penghasilan_ayah);
	if($temp_filter_id_penghasilan_ayah['result']){
		$id_penghasilan_ayah = $temp_filter_id_penghasilan_ayah['result']['id_penghasilan'];	
	}	


$nik_ibu= $data['nik_ibu']; 
$tgl_lahir_ibu= date('Y-m-d',strtotime($data['tgl_lahir_ibu']));
$id_jenjang_pendidikan_ibu= $data['id_jenjang_pendidikan_ibu'];

$filter_id_jenjang_pendidikan_ibu = "nm_jenj_didik ilike '%".trim($id_jenjang_pendidikan_ibu)."%' ";
	$temp_filter_id_jenjang_pendidikan_ibu = $proxy->GetRecord($token,'jenjang_pendidikan',$filter_id_jenjang_pendidikan_ibu);
	if($temp_filter_id_jenjang_pendidikan_ibu['result']){
		$id_jenjang_pendidikan_ibu = $temp_filter_id_jenjang_pendidikan_ibu['result']['id_jenj_didik'];	
	}
	
$id_pekerjaan_ibu= $data['id_pekerjaan_ibu']; 

$filter_id_pekerjaan_ibu = "nm_pekerjaan ilike '%".trim($id_pekerjaan_ibu)."%' ";
	$temp_filter_id_pekerjaan_ibu = $proxy->GetRecord($token,'pekerjaan',$filter_id_pekerjaan_ibu);
	if($temp_filter_id_pekerjaan_ibu['result']){
		$id_pekerjaan_ibu = $temp_filter_id_pekerjaan_ibu['result']['id_pekerjaan'];	
	}
					 
$id_penghasilan_ibu= $data['id_penghasilan_ibu'];

$filter_id_penghasilan_ibu = "batas_atas <= ".$id_penghasilan_ibu." AND ".$id_penghasilan_ibu." <= batas_bawah";
	$temp_filter_id_penghasilan_ibu = $proxy->GetRecord($token,'penghasilan',$filter_id_penghasilan_ibu);
	if($temp_filter_id_penghasilan_ibu['result']){
		$id_penghasilan_ibu = $temp_filter_id_penghasilan_ibu['result']['id_penghasilan'];	
	}

$nm_wali= $data['nm_wali']; 
$tgl_lahir_wali= date('Y-m-d',strtotime($data['tgl_lahir_wali']));
$id_jenjang_pendidikan_wali= $data['id_jenjang_pendidikan_wali'];

$filter_id_jenjang_pendidikan_wali = "nm_jenj_didik ilike '%".trim($id_jenjang_pendidikan_wali)."%' ";
	$temp_filter_id_jenjang_pendidikan_wali = $proxy->GetRecord($token,'jenjang_pendidikan',$filter_id_jenjang_pendidikan_wali);
	if($temp_filter_id_jenjang_pendidikan_wali['result']){
		$id_jenjang_pendidikan_wali = $temp_filter_id_jenjang_pendidikan_wali['result']['id_jenj_didik'];	
	}

$id_pekerjaan_wali= $data['id_pekerjaan_wali']; 

$filter_id_pekerjaan_wali = "nm_pekerjaan ilike '%".trim($id_pekerjaan_wali)."%' ";
	$temp_filter_id_pekerjaan_wali = $proxy->GetRecord($token,'pekerjaan',$filter_id_pekerjaan_wali);
	if($temp_filter_id_pekerjaan_wali['result']){
		$id_pekerjaan_wali = $temp_filter_id_pekerjaan_wali['result']['id_pekerjaan'];	
	}

$id_penghasilan_wali= $data['id_penghasilan_wali']; 

$filter_id_penghasilan_wali = "batas_atas <= ".$id_penghasilan_wali." AND ".$id_penghasilan_wali." <= batas_bawah";
	$temp_filter_id_penghasilan_wali = $proxy->GetRecord($token,'penghasilan',$filter_id_penghasilan_wali);
	if($temp_filter_id_penghasilan_wali['result']){
		$id_penghasilan_wali = $temp_filter_id_penghasilan_wali['result']['id_penghasilan'];	
	}

$array_wilayah = explode(',', $data['id_wil']);
$filter_kab = "nm_wil ilike '%".trim($array_wilayah[1])."%' AND id_level_wil=2";
	$temp_kab = $proxy->GetRecord($token,'wilayah',$filter_kab);
	if($temp_kab['result']){
		$id_kab = $temp_kab['result']['id_wil'];	
	}
	
	$filter_kec = "nm_wil ilike '%".trim($array_wilayah[0])."%' AND id_induk_wilayah='".$id_kab."'";	
	$temp_kec = $proxy->GetRecord($token,'wilayah',$filter_kec);
	if($temp_kec['result']){
		$id_wil = $temp_kec['result']['id_wil'];	
	}
	

$record['nm_pd']= $nm_pd; 
$record['tmpt_lahir']= $tmpt_lahir; 
$record['jk']= $jk; 
$record['tgl_lahir']= $tgl_lahir; 
$record['id_agama']= $id_agama; 
$record['nm_ibu_kandung']= $nm_ibu_kandung; 
$record['nik']= $nik; 
$record['kewarganegaraan']= $kewarganegaraan; 
$record['ds_kel']= $ds_kel; 
$record['id_wil']= $id_wil;
$record['id_kebutuhan_khusus_ayah']= $id_kebutuhan_khusus_ayah; 
$record['id_kebutuhan_khusus_ibu']= $id_kebutuhan_khusus_ibu; 
$record['id_kk']= $id_kk; 
$record['a_terima_kps']= $a_terima_kps; 
$record['nisn']= $nisn; 
$record['jln']= $jln; 
$record['rt']= $rt; 
$record['rw']= $rw; 
$record['nm_dsn']= $nm_dsn; 
$record['kode_pos']= $kode_pos; 
$record['no_tel_rmh']= $no_tel_rmh;
$record['no_hp']= $no_hp;
$record['email'] = $email;

$record['no_kps'] = $no_kps;
$record['npwp'] = $npwp;

$record['nm_ayah']= $nm_ayah; 
$record['nik_ayah']= $nik_ayah; 
$record['tgl_lahir_ayah']= $tgl_lahir_ayah; 
$record['id_jenjang_pendidikan_ayah']= $id_jenjang_pendidikan_ayah;
$record['id_pekerjaan_ayah']= $id_pekerjaan_ayah;
$record['id_penghasilan_ayah']= $id_penghasilan_ayah;

$record['nik_ibu']= $nik_ibu; 
$record['tgl_lahir_ibu']= $tgl_lahir_ibu; 
$record['id_jenjang_pendidikan_ibu']= $id_jenjang_pendidikan_ibu;
$record['id_pekerjaan_ibu']= $id_pekerjaan_ibu;
$record['id_penghasilan_ibu']= $id_penghasilan_ibu;

if($nm_wali!='-' && $nm_wali!='' ){
	$record['nm_wali']= $nm_wali; 
	$record['tgl_lahir_wali']= $tgl_lahir_wali; 
	$record['id_jenjang_pendidikan_wali']= $id_jenjang_pendidikan_wali;
	$record['id_pekerjaan_wali']= $id_pekerjaan_wali;
	$record['id_penghasilan_wali']= $id_penghasilan_wali; //on progress. tidak bisa handle string kosong
}


$data = $record;
$insert = $proxy->InsertRecord($token,$table,json_encode($data));	

if($insert['result']){
	if($insert['result']['error_desc']==NULL){
		$id_pd = $insert['result']['id_pd'];
		$filter_sms = "id_sp='".$id_sp."' and kode_prodi ilike '%".$kode_prodi."%'";
		$temp_sms = $proxy->GetRecord($token,'sms',$filter_sms);
		if($temp_sms['result']){
			$id_sms = $temp_sms['result']['id_sms'];	
		}
		$record_pt['id_sms'] = $id_sms;
		$record_pt['id_pd'] = $id_pd;
		$record_pt['id_sp'] = $id_sp;
		$record_pt['id_jns_daftar'] = $id_jns_daftar;
		$record_pt['nipd'] = $nipd;
		$record_pt['id_jns_daftar'] = $id_jns_daftar;
		$record_pt['tgl_masuk_sp'] = $tgl_masuk_sp;
		$record_pt['a_pernah_tk'] = 1;
		$record_pt['a_pernah_paud'] = 1;
		$record_pt['mulai_smt'] = $mulai_smt;
		$record_pt['sks_diakui'] = $sks_diakui;
							
		$data_pt = $record_pt;
		$insert_pt = $proxy->InsertRecord($token,$table1,json_encode($data_pt));
		//var_dump($insert_pt);
			if($insert_pt['result']['error_desc']==NULL){
				$LOG=$LOG.'[SUCCESS] Data "'.$nm_pd.' / '.$nipd.'" berhasil di tambahkan <br>';
				$REPORT = '[SUCCESS] Data "'.$nm_pd.' / '.$nipd.'" berhasil di tambahkan <br>';
			}
			else{
				$LOG=$LOG."[Error] pada data ".$nm_pd." / ".$nipd." - ".$insert_pt['result']['error_desc']."<br>";	
				$REPORT = "[Error] pada data ".$nm_pd." / ".$nipd." - ".$insert_pt['result']['error_desc']."<br>";				}
								
			}
		else{
			$LOG=$LOG."[Error] pada data ".$nm_pd." / ".$nipd." - ".$insert['result']['error_desc']."<br>";
			$REPORT="[Error] pada data ".$nm_pd." / ".$nipd." - ".$insert['result']['error_desc']."<br>";				
			}
	}			


$LOG = $row." : ".$LOG;

$file_LOG = fopen($location_of_LOG, "a");					
fwrite($file_LOG, $LOG);	

echo $REPORT;



?>

