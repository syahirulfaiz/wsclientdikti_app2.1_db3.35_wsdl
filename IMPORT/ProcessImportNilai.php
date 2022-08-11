<?php

//GANTI $this->proxy .... $proxy
//GANTI $this->session('idsp')....$id_sp
//GANTI $this->token .... $token

//GANTI location_of_LOG 

include('../initsoap.php');

$nama_file_LOG = $_REQUEST['nama_file_LOG'];

$row = $_REQUEST['row'];

$data = json_decode(stripslashes($_POST['key']),true); // Pakai TRUE agar return array bersih

$kode_prodi = $data['kode_prodi'];
$kode_mk = $data['kode_mk'];
$nm_mk = $data['nm_mk'];
$nm_kls = $data['nm_kls'];
$nipd  = $data['nipd'];
$id_smt  = $data['id_smt'];

$nilai_huruf = $data['nilai_huruf'];								
$nilai_indeks = $data['nilai_indeks'];
$nilai_angka = $data['nilai_angka'];

$path_directory_LOG = '../LOG/'.$id_smt.'/'.$kode_prodi;

if (!file_exists($path_directory_LOG)) {
    mkdir($path_directory_LOG, 0777, true);
}

$location_of_LOG=$path_directory_LOG."/".$id_smt."-".$kode_prodi."-".$nama_file_LOG;

if ($row==0) $file_LOG = fopen($location_of_LOG, "w") or die("Unable to open file!");

$LOG =""; // start of LOG

$table = 'nilai';			

$filter_sms = "id_sp='".$id_sp."' and kode_prodi ilike '%".$kode_prodi."%'";
$temp_sms = $proxy->GetRecord($token,'sms',$filter_sms);
if($temp_sms['result']){
	$id_sms = $temp_sms['result']['id_sms'];
	$id_jenj_didik = $temp_sms['result']['id_jenj_didik'];
}	

/*
//filter matakuliah
$id_mk='';
//EXE MATA_KULIAH
$temp_filter = "kode_mk ilike '".$kode_mk."'";	
$temp_query = $proxy->GetRecord($token,'mata_kuliah',$temp_filter);
if($temp_query['result']){
	$id_mk = $temp_query['result']['id_mk'];	
}
*/

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

//Filter Kelas KUliah
/*
$filter_kls = "p.id_mk = '".$id_mk."' and nm_kls ilike '%".$nm_kls."%' and p.id_smt='".$id_smt."'";
$temp_kls = $proxy->GetRecord($token,'kelas_kuliah',$filter_kls);
if($temp_kls['result']){
	$id_kls = $temp_kls['result']['id_kls'];
	$nm_kls = $temp_kls['result']['nm_kls'];
						//$id_mk  = $temp_kls['result']['id_mk'];	
}else $id_kls;
*/
$temp_filter = "p.id_sms='".$id_sms."' AND p.id_smt='".$id_smt."' AND nm_kls ilike '".$nm_kls."' AND id_mk = '".$id_mk."'";
$temp_query = $proxy->GetRecord($token,'kelas_kuliah.raw',$temp_filter);
if($temp_query['result']){
	$id_kls = $temp_query['result']['id_kls'];	
}
					
$tot_kls = count($temp_kls);
//filter Mahasiswa PT
$filter_mhspt = "nipd ilike '%".$nipd."%'";
$temp_mhspt = $proxy->GetRecord($token,'mahasiswa_pt',$filter_mhspt);
if($temp_mhspt['result']){
	$id_reg_pd = $temp_mhspt['result']['id_reg_pd'];	
	$id_pd = $temp_mhspt['result']['id_pd'];	
}else $id_reg_pd='';
$tot_reg_pd = count($temp_mhspt);
					
//Filter Mahasiswa 
$filtermhs = "id_pd='".$id_pd."'";
$tempmhs = $proxy->GetRecord($token,'mahasiswa',$filtermhs);
if($tempmhs['result']){
	$nm_pd = $tempmhs['result']['nm_pd'];	
}else $id_reg_pd='';
					
if($tot_kls != 0 && $tot_reg_pd != 0){
	$id_reg_pd_x = $id_reg_pd;
	$id_kls_x = $id_kls;
}
else {
	$id_reg_pd_x = '';
	$id_kls_x = '';
}

//cari data nilai
$temp_filter = "p.id_reg_pd='".$id_reg_pd."' AND p.id_kls='".$id_kls."'";
$temp_query = $proxy->GetRecord($token,'nilai',$temp_filter);
if($temp_query['result']){
	$nilai_id_reg_pd = $temp_query['result']['id_reg_pd'];
	$nilai_id_kls = $temp_query['result']['id_kls'];		
}

//Key data
$temp_key = array('id_kls' => $nilai_id_kls, 'id_reg_pd' => $nilai_id_reg_pd);
$record['nilai_angka'] = $nilai_angka;
$record['nilai_huruf'] = $nilai_huruf ;
$record['nilai_indeks'] = $nilai_indeks;
$data = $record;
					
$array_nil = array('key' => $temp_key, 'data' => $data);

$insert_nilai='';
$update_nilai = '';	

$error_insert_nilai='';	
$error_update_nilai='';	
	
if($nilai_id_reg_pd!='' && $nilai_id_kls!=''){
	$update_nilai = $proxy->UpdateRecord($token,'nilai',json_encode($array_nil));
	$error_update_nilai=$update_nilai['result']['error_desc'];
}else{
	//$record='';
	$record['id_kls']= $id_kls;
	$record['id_reg_pd']= $id_reg_pd;
	$record['asal_data']= 9;
	$data = $record;
	$insert_nilai = $proxy->InsertRecord($token,'nilai',json_encode($data));
	$error_insert_nilai=$insert_nilai['result']['error_desc'];
}
				
if($error_insert_nilai==NULL && $error_update_nilai==NULL){
	$LOG = $LOG."[SUCCESS] Data NIM / Nama '".$nipd." / ".$nm_pd."' berhasil tersimpan <br>";	
	$REPORT="[SUCCESS] Data NIM / Nama '".$nipd." / ".$nm_pd."' berhasil tersimpan <br>";	
}else{
	$LOG = $LOG."[Error] pada data ( '".$nipd." / ".$kode_mk." / ".$nm_kls."' ) : <br> ERROR INSERT : ".$error_insert_nilai." <br> ERROR UPDATE : ".$error_update_nilai."<br>-----------------------------------------------<br>";
		$REPORT="[Error] pada data ( '".$nipd." / ".$kode_mk." / ".$nm_kls."' ) : <br> ERROR INSERT : ".$error_insert_nilai." <br> ERROR UPDATE : ".$error_update_nilai."<br>-----------------------------------------------<br>";
}
				
$LOG = $row." : ".$LOG;

$file_LOG = fopen($location_of_LOG, "a");					
fwrite($file_LOG, $LOG);	

echo $REPORT;


?>

