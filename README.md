# wsclientdikti_app2.1_db3.35_wsdl
<h1>A wsdl version of the obsolete webservice client importer for the academic data to the Ministry of HE of Rep of Indonesia</h1> <br/><hr/>

<h3>Importer could transfer following data:</h3><br/>
<ul>
  <li>student's data</li>
  <li>curriculum for each cohort</li>
  <li>modules (inside each course(s)) </li>
  <li>student's classes</li>
  <li>student's academic history</li>
  <li>Semester Grade Point Average</li>
  <li>Cumulative Grade point average</li>
</ul>

<p>
PDDIKTI Feeder provides services that can be used by universities to be able to carry out interoperability of information systems that are already running within their respective higher education institutions. Data sources used to meet PDDIKTI's needs can come from an information system or multiple information systems, where data originating from these systems needs to be mapped first to suit the standards set by PDDIKTI.
</p>

<p>
<b>List of available Web Service (WSDL):</b>
</p>
<ol>
<li>GetToken</li>
<li>GetRecord</li>
<li>GetRecordset</li>
<li>InsertRecord</li>
<li>InsertRecordset</li>
<li>UpdateRecord</li>
<li>UpdateRecordset</li>
<li>DeleteRecord</li>
<li>DeleteRecordset</li>
<li>GetDictionary</li>
<li>ListTable</li>
<li>CheckDeveloperMode</li>
<li>GetVersion</li>
</ol>

<hr/>
<p>
<b>How to set up the username & password:</b>
</p>
<code>
require ‘nusoap/nusoap.php’;
require ‘nusoap/class.wsdlcache.php’;
$wsdl = ‘http://localhost:8082/ws/live.php?wsdl’;
$client = new nusoap_client($wsdl, true);
$proxy = $client->getProxy();
$username = ‘your-username’;
$password = ‘your-password’;
$result = $proxy->GetToken($username, $password);
$token = $result[‘result’];
print $token; // token for next request 
</code>
