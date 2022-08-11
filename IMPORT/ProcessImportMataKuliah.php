<?php

//GANTI $this->proxy .... $proxy
//GANTI $this->session('idsp')....$id_sp

include('../initsoap.php');

$nama_file_LOG = $_REQUEST['nama_file_LOG'];

$row = $_REQUEST['row'];

$data = json_decode(stripslashes($_POST['key']),true); // Pakai TRUE agar return array bersih

$kode_prodi = $data['kode_prodi'];
$id_smt = $data['id_smt'];

$path_directory_LOG = '../LOG/'.$id_smt.'/'.$kode_prodi;

if (!file_exists($path_directory_LOG)) {
    mkdir($path_directory_LOG, 0777, true);
}

$location_of_LOG=$path_directory_LOG."/".$id_smt."-".$kode_prodi."-".$nama_file_LOG;

if ($row==0) $file_LOG = fopen($location_of_LOG, "w") or die("Unable to open file!");

$LOG =""; // start of LOG

$table_kurikulum = 'kurikulum';	
$table_matakuliah = 'mata_kuliah';
$table_matakuliah_kurikulum= 'mata_kuliah_kurikulum';


$filter_sms = "id_sp='".$id_sp."' and kode_prodi ilike '%".$kode_prodi."%'";
$temp_sms = $proxy->GetRecord($token,'sms',$filter_sms);
	if($temp_sms['result']){
		$id_sms = $temp_sms['result']['id_sms'];
		$id_jenj_didik = $temp_sms['result']['id_jenj_didik'];
	}



$filter_nm_kurikulum_sp = "id_smt='".$id_smt."'";
$temp_nm_kurikulum_sp = $proxy->GetRecord($token,'semester',$filter_nm_kurikulum_sp);
	if($temp_nm_kurikulum_sp['result']){
		$nm_smt = $temp_nm_kurikulum_sp['result']['nm_smt'];	
		$tgl_mulai = $temp_nm_kurikulum_sp['result']['tgl_mulai'];
		$tgl_selesai = $temp_nm_kurikulum_sp['result']['tgl_selesai'];
	}							
$nm_kurikulum_sp = 'Kurikulum TA '.$nm_smt;

		
$jml_sem_normal=0;
$jml_sks_lulus=0;
$jml_sks_wajib=0;
$jml_sks_pilihan=0;
	
$kode_mk=$data['kode_mk'];
$nm_mk=$data['nm_mk'];
$jns_mk=$data['jns_mk'];
$kel_mk=$data['kel_mk'];
$sks_mk=$data['sks_mk'];
$sks_tm=$data['sks_tm'];
$sks_prak=$data['sks_prak'];
$sks_prak_lap=$data['sks_prak_lap'];
$sks_sim=$data['sks_sim'];
$metode_pelaksanaan_kuliah=$data['metode_pelaksanaan_kuliah'];
$a_sap=$data['a_sap'];
$a_silabus=$data['a_silabus'];
$a_bahan_ajar=$data['a_bahan_ajar'];
$acara_prak=$data['acara_prak'];
$a_diktat=$data['a_diktat'];
$tgl_mulai_efektif= $tgl_mulai;
$tgl_akhir_efektif= $tgl_selesai;	
	
$smt=$data['smt'];
$a_wajib=$data['a_wajib'];	
					
$record['id_sms'] = $id_sms ;
$record['id_jenj_didik'] = $id_jenj_didik;
$record['id_smt'] = $id_smt;
$record['nm_kurikulum_sp'] = $nm_kurikulum_sp;
$record['jml_sem_normal'] = $jml_sem_normal;
$record['jml_sks_lulus'] = $jml_sks_lulus;
$record['jml_sks_wajib'] = $jml_sks_wajib;
$record['jml_sks_pilihan'] = $jml_sks_pilihan;					

$data_kurikulum = $record;//data KURIKULUM

$record=NULL;
$record['id_sms'] = $id_sms ;
$record['id_jenj_didik'] = $id_jenj_didik;
//$record['kode_mk'] = $kode_mk;
//$record['nm_mk'] = $nm_mk;
$record['jns_mk'] = $jns_mk;
$record['kel_mk'] = $kel_mk;
$record['sks_mk'] = $sks_mk;
$record['sks_tm'] = $sks_tm;
$record['sks_prak'] = $sks_prak;
$record['sks_prak_lap'] = $sks_prak_lap;
$record['sks_sim'] = $sks_sim;
$record['metode_pelaksanaan_kuliah'] = $metode_pelaksanaan_kuliah;
$record['a_sap'] = $a_sap;
$record['a_silabus'] = $a_silabus;
$record['a_bahan_ajar'] = $a_bahan_ajar;
$record['acara_prak'] = $acara_prak;
$record['a_diktat'] = $a_diktat;
$record['tgl_mulai_efektif'] = $tgl_mulai_efektif;
$record['tgl_akhir_efektif'] = $tgl_akhir_efektif;
					
$data_mata_kuliah = $record;//data MATA_KULIAH
		
$error_update_kurikulum ='';
$error_insert_kurikulum = '';
					
$error_update_mata_kuliah ='';
$error_insert_mata_kuliah = '';
					
$error_update_mata_kuliah_kurikulum ='';
$error_insert_mata_kuliah_kurikulum = '';			
			
//EXE KURIKULUM
$id_kurikulum_sp = '';
$temp_filter = "id_sms='".$id_sms."' AND id_jenj_didik='".$id_jenj_didik."' AND id_smt='".$id_smt."'";
$temp_query = $proxy->GetRecord($token,'kurikulum',$temp_filter);
if($temp_query['result']){
	$id_kurikulum_sp = $temp_query['result']['id_kurikulum_sp'];
}	

if($id_kurikulum_sp!=''){
	$temp_key = array('id_sms' => $id_sms,'id_jenj_didik' => $id_jenj_didik,'id_smt' => $id_smt);
	$array_update = array('key'=>$temp_key,'data'=>$data_kurikulum);
	$temp_execution = $proxy->UpdateRecord($token,$table_kurikulum,json_encode($array_update));
	//$LOG = $LOG ."UPDATE KURIKULUM (".$kode_mk."-".$nm_mk.") : ".print_r($temp_execution,true)."<br>";
	
	$error_update_kurikulum =$temp_execution['result']['error_desc'];
	
}else{
	$temp_execution = $proxy->InsertRecord($token,$table_kurikulum,json_encode($data_kurikulum));
	//$LOG = $LOG ."INSERT KURIKULUM (".$kode_mk."-".$nm_mk.") : ".print_r($temp_execution,true)."<br>";					
	
	$error_insert_kurikulum =$temp_execution['result']['error_desc'];

}


//Filter MATA_KULIAH
$id_mk='';
$temp_filter = "kode_mk ilike '".$kode_mk."' and nm_mk ilike '%".substr($nm_mk,0,6)."%'";	
$temp_query = $proxy->GetRecord($token,'mata_kuliah',$temp_filter);
if($temp_query['result']){
	$id_mk = $temp_query['result']['id_mk'];	
}

if($id_mk!=''){
	$temp_key = array('id_sms'=>$id_sms,'id_jenj_didik'=>$id_jenj_didik,'kode_mk'=>$kode_mk);
	$array_update = array('key'=>$temp_key,'data'=>$data_mata_kuliah);
	$temp_execution = $proxy->UpdateRecord($token,$table_matakuliah,json_encode($array_update));
	$update_mata_kuliah = $temp_execution;
	//$LOG = $LOG ."UPDATE MATA_KULIAH (".$kode_mk."-".$nm_mk.") : ".print_r($temp_execution,true)."<br>";
	
	$error_update_mata_kuliah =$temp_execution['result']['error_desc'];
	

}else{
	$record['kode_mk'] = $kode_mk;
	$record['nm_mk'] = $nm_mk;
						
	$temp_execution = $proxy->InsertRecord($token,$table_matakuliah,json_encode($record));
	$insert_mata_kuliah = $temp_execution;
	//$LOG = $LOG ."INSERT MATA_KULIAH (".$kode_mk."-".$nm_mk.") : ".print_r($temp_execution,true)."<br>";	
	
	$error_insert_mata_kuliah =$temp_execution['result']['error_desc'];

}



//EXE MATA_KULIAH_KURIKULUM	
$temp_filter = "id_sms='".$id_sms."' AND id_jenj_didik='".$id_jenj_didik."' AND id_smt='".$id_smt."'";
$temp_query = $proxy->GetRecord($token,'kurikulum',$temp_filter);
if($temp_query['result']){
	$id_kurikulum_sp = $temp_query['result']['id_kurikulum_sp'];
}
//$LOG = $LOG ."SELECT MATA_KULIAH_KURIKULUM - KURIKULUM (".$kode_mk."-".$nm_mk.") : ".print_r($temp_query,true)."<br>";

//Filter MATA_KULIAH
$id_mk='';
$temp_filter = "kode_mk ilike '".$kode_mk."' and nm_mk ilike '%".substr($nm_mk,0,6)."%'";	
$temp_query = $proxy->GetRecord($token,'mata_kuliah',$temp_filter);
if($temp_query['result']){
	$id_mk = $temp_query['result']['id_mk'];	
}else{
	//jaga-jaga tidak masuk
	$temp_filter = "kode_mk ilike '".$kode_mk."'";	
	$temp_query = $proxy->GetRecord($token,'mata_kuliah',$temp_filter);
	if($temp_query['result']){
		$id_mk = $temp_query['result']['id_mk'];	
	}
}

//$LOG = $LOG ."SELECT MATA_KULIAH_KURIKULUM - MATA_KULIAH (".$kode_mk."-".$nm_mk.") : ".print_r($temp_query,true)."<br>";					


$temp_filter = "id_kurikulum_sp='".$id_kurikulum_sp."' AND id_mk='".$id_mk."'";
$temp_query = $proxy->GetRecord($token,'mata_kuliah_kurikulum.raw',$temp_filter);
$id_mk_mata_kuliah_kurikulum='';
$id_kurikulum_sp_mata_kuliah_kurikulum='';
if($temp_query['result']){
	$id_mk_mata_kuliah_kurikulum = $temp_query['result']['id_mk'];
	$id_kurikulum_sp_mata_kuliah_kurikulum = $temp_query['result']['id_kurikulum_sp'];
}
//$LOG = $LOG ."SELECT MATA_KULIAH_KURIKULUM - MATA_KULIAH_KURIKULUM (".$kode_mk."-".$nm_mk.") : ".print_r($temp_query,true)."<br>";


$record=NULL;					
$record['smt'] = $smt;
$record['sks_mk'] = $sks_mk;
$record['sks_tm'] = $sks_tm;
$record['sks_prak'] = $sks_prak;
$record['sks_prak_lap'] = $sks_prak_lap;
$record['sks_sim'] = $sks_sim;
$record['a_wajib'] = $a_wajib;
					
$data_mata_kuliah_kurikulum = $record;//data_mata_kuliah_kurikulum

//$LOG = $LOG ."<br>ID KURIKULUM :". $id_kurikulum_sp."<br>";
//$LOG = $LOG ."<br>ID MK :". $id_mk."<br>";		

if($id_kurikulum_sp_mata_kuliah_kurikulum!='' AND $id_mk_mata_kuliah_kurikulum!=''){
	$temp_key = array('id_kurikulum_sp' => $id_kurikulum_sp, 'id_mk' => $id_mk);
	$array_update = array('key'=>$temp_key,'data'=>$data_mata_kuliah_kurikulum);
	$temp_execution = $proxy->UpdateRecord($token,$table_matakuliah_kurikulum,json_encode($array_update));
	$update_mata_kuliah_kurikulum = $temp_execution;
	//$LOG = $LOG ."UPDATE MATA_KULIAH_KURIKULUM - MATA_KULIAH_KURIKULUM (".$kode_mk."-".$nm_mk.") : ".print_r($temp_execution,true)."<br>";
	
	$error_update_mata_kuliah_kurikulum =$temp_execution['result']['error_desc'];
						
}else{
	$record=NULL;
	$record['smt'] = $smt;
	$record['sks_mk'] = $sks_mk;
	$record['sks_tm'] = $sks_tm;
	$record['sks_prak'] = $sks_prak;
	$record['sks_prak_lap'] = $sks_prak_lap;
	$record['sks_sim'] = $sks_sim;
	$record['a_wajib'] = $a_wajib;
	$record['id_kurikulum_sp'] = $id_kurikulum_sp;
	$record['id_mk'] = $id_mk;
						
	$data_mata_kuliah_kurikulum = $record;//data_mata_kuliah_kurikulum
						
	$temp_execution = $proxy->InsertRecord($token,$table_matakuliah_kurikulum,json_encode($data_mata_kuliah_kurikulum));
	$insert_mata_kuliah_kurikulum = $temp_execution;	
	//$LOG = $LOG ."INSERT MATA_KULIAH_KURIKULUM - MATA_KULIAH_KURIKULUM (".$kode_mk."-".$nm_mk.") : ".print_r($temp_execution,true)."<br>";				
	$error_insert_mata_kuliah_kurikulum =$temp_execution['result']['error_desc'];
	
}

//REPORT
if($error_insert_kurikulum==NULL AND $error_update_kurikulum==NULL AND $error_insert_mata_kuliah==NULL AND $error_update_mata_kuliah==NULL AND $error_insert_mata_kuliah_kurikulum==NULL AND $error_update_mata_kuliah_kurikulum==NULL){
	$LOG = $LOG. '[SUCCESS]Data "'.$nm_kurikulum_sp.'" - "'.$kode_mk.'" - "'.$nm_mk.'" berhasil di tambahkan<br>-----------------------------------------------------------<br>';
	$REPORT = '[SUCCESS]Data "'.$nm_kurikulum_sp.'" - "'.$kode_mk.'" - "'.$nm_mk.'" berhasil di tambahkan<br>-----------------------------------------------------------<br>';									
}
else{				
	$LOG = $LOG. "Error pada data ".$nm_kurikulum_sp." - ".$kode_mk." - ".$nm_mk." <br> DESC INSERT KURIKULUM : ".$error_insert_kurikulum." <br> DESC UPDATE KURIKULUM : ".$error_update_kurikulum." <br> DESC INSERT MATA_KULIAH : ".$error_insert_mata_kuliah."<br> DESC UPDATE MATA_KULIAH : ".$error_update_mata_kuliah." <br> DESC INSERT MATA_KULIAH_KURIKULUM : ".$error_insert_mata_kuliah_kurikulum." <br> DESC UPDATE MATA_KULIAH_KURIKULUM : ".$error_update_mata_kuliah_kurikulum."<br>-----------------------------------------------------------<br>";	
	$REPORT = "Error pada data ".$nm_kurikulum_sp." - ".$kode_mk." - ".$nm_mk." <br> DESC INSERT KURIKULUM : ".$error_insert_kurikulum." <br> DESC UPDATE KURIKULUM : ".$error_update_kurikulum." <br> DESC INSERT MATA_KULIAH : ".$error_insert_mata_kuliah."<br> DESC UPDATE MATA_KULIAH : ".$error_update_mata_kuliah." <br> DESC INSERT MATA_KULIAH_KURIKULUM : ".$error_insert_mata_kuliah_kurikulum." <br> DESC UPDATE MATA_KULIAH_KURIKULUM : ".$error_update_mata_kuliah_kurikulum."<br>-----------------------------------------------------------<br>";	
}

$LOG = $row." : ".$LOG;

$file_LOG = fopen($location_of_LOG, "a");					
fwrite($file_LOG, $LOG);	


echo $REPORT;



?>

