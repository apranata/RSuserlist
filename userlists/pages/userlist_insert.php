<?php
	
	include "../../../include/db.php";
	include "../../../include/authenticate.php"; if (!checkperm("s")) {exit ("Permission denied.");}
	include "../../../include/general.php";
	include "../config/config.php";
	$ref = 0;
	$user = 0;
	$content = '';
	$date = date('Y-m-d H:i:s');
	
	if(isset($_POST['ref'])) $ref = $_POST['ref'];
	if(isset($_POST['userref'])) $user = $_POST['userref'];
	if(isset($_POST['rptcontent'])) $content = $_POST['rptcontent'];	
	$reportername = $userfullname;
	$q = "insert into report_resource set ref = '$ref',user = '$user',datestamp = '$date', content = '$content',statusemail = '0'";
	
	sql_query($q);
	
	$id = sql_value("select max(id) as value  from report_resource",0);
	$headers  = 'MIME-Version: 1.0' . "\n";
	$headers .= 'Content-type:text/html;charset=iso-8859-1' . "\n";
	$headers .= 'From: DragonTales Webmaster <webmaster@jisedu.or.id>' . "\n";
		
	$subject = $lang['reportemailsubject']; 
	
	// --------- Sending Email for Admin------------
		$to = $emailto;
		
		$emailbody = ucfirst($reportername)." has reported resource #<a href='$baseurl/index.php?url=$baseurl/pages/view.php?ref=$ref' target='_blank'>$ref</a>.<br />This is the content:<br />
		$content
		<br />";
		$rwe = mail($to,$subject,$emailbody,$headers);
		
		//print $to."<br />".$emailbody;
	// --------- End Email for admin---------
	//die;
	
	//  --------- Sending email for owner --------- 
		$userownerref = sql_value("SELECT user as value FROM collection_log WHERE resource =$ref AND TYPE = 'a' order by date desc",0);
		
		$emailowner = sql_value("SELECT email as value FROM `user` WHERE ref =  $userownerref","");
		$to = $emailowner;
		$emailbody = "Your resource #<a href='$baseurl/index.php?url=$baseurl/pages/view.php?ref=$ref' target='_blank'>$ref</a>, has been reported to the system admin.  Our system shows you are the owner of this resource.<br />
To find the above resource, simply log into DragonTales and type $ref in the search box.<br />
If you believe that the placement of this resource in DragonTales is appropriate, then you may email <a href=\"mailto:$emailto\">$emailto</a> explaining why.<br />
If you believe it should be removed, then please delete it.  To delete, simply hit the \"Delete\" link in the \"Resource Tools\" box beside the image of the resource.<br /><br />
John<br />
x  80618
";
		$rwe .= mail($to,$subject,$emailbody,$headers);
	//  --------- end email for owner --------- 
			//print $to."<br />".$emailbody."<br /><br /><br /><br />";

	
	
	//  --------- Sending email for reporter --------- 
		$emailreporter = sql_value("select email as value from user where ref = '$user'",'');
		$to = $emailreporter;
		$emailbody = "Thank-you for reporting resource #<a href='$baseurl/index.php?url=$baseurl/pages/view.php?ref=$ref' target='_blank'>$ref</a>
The owner of the resource has been sent an email indicating that the resource has been reported. <br /> 
Depending upon what the nature of the resource is,  the following may happen:<br />
<ul type=\"square\">
	<li> the owner may be given one week to either remove the resource or justify its presence on DragonTales</li>
<li> it may be removed by the system admin</li>
<li> it may be sent to divisional administration for follow up.</li>
</ul>
<br />
Thank-you for taking the time to report this resource.<br /><br />
John<br />
x  80618
";
		$rwe .= mail($to,$subject,$emailbody,$headers);
	//  --------- end email for reporter --------- 
	
		if(!substr_count($rwe,"0")){
			$qupdate = "update report_resource set statusemail = 1 where id = '$id'";
			sql_query($qupdate);
		}
		//print $to."<br />".$emailbody."<br /><br /><br /><br />";
		//die();
	
/*
$emailreporter = sql_value("select email as value from user where ref = '$user'",'');
	
	
	
	
	$id = sql_value("select max(id) as value  from report_resource",0);
	
	if ($emailtoadmin){
		
		$email = "Dear ".$ownerfullname.",<br />";
		$email .= "Your resource <strong>#".$ref."</strong>, has been reported to the system admin.  Our system shows you are the owner of this resource.<br /><br />


To find the above resource, simply log into DragonTales and type ".$ref." in the search box.<br />

If you believe that the placement of this resource in DragonTales is appropriate, then you may email ".$emailto." explaining why.<br />

If you believe it should be removed, then please delete it.  To delete, simply hit the \"Delete\" link in the \"Resource Tools\" box beside the image of the resource.<br /><br />


John<br />
x  80618";
		
		$to = $emailowner;
		$cc = $emailto.$emailreporter.";";
		$subject = $lang['reportemailsubject']; 
		$headers  = 'MIME-Version: 1.0' . "\n";
		$headers .= 'Content-type:text/html;charset=iso-8859-1' . "\n";
		$headers .= 'To: '.$to. "\n";
		$headers .= 'Cc: '.$cc. "\n";		
		$headers .= 'From: DragonTales Webmaster <webmaster@jisedu.or.id>' . "\n";

		$headers = "From: JIS DragonTales Admin <webmaster@jisedu.or.id>\n"; //optional headerfields
		
		print $to."<br />". $subject."<br />" . $email."<br />";
		die;
		if(mail($to,$subject,$email,$headers) && $id != 0){
			$qupdate = "update report_resource set statusemail = 1 where id = '$id'";
			sql_query($qupdate);
		}
					
	}*/
	
    header("location: ".$_GET['urlreferer'].'&back=true');
?>