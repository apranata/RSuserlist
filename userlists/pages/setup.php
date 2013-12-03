<?php
/**
 * Userlist page (part of Team Center)
 
 */
include "../../../include/db.php";
include "../../../include/authenticate.php";
include "../../../include/general.php";if (!checkperm("t")) {exit ("Permission denied.");}
include "../../../include/reporting_functions.php";
include "../../../include/header.php";

$count = sql_value("select count(*) as value from user_userlist",0);


?>
<div class="Listview">
<form name="frm" method="post" enctype="multipart/form-data">
	<div><?php echo $lang["userlistbulkinstructions"];?></div><br><br>
    <div><?php echo $lang["userlistrecords"] . $count;?> records on <?php echo $mysql_db;?> database.</div><br />
    <div><?php echo $lang["userlistbulkfile"] . "</div><div  style=\"margin-left:10px;margin-top:5px;\">" .$lang["userlistcsvformatfile"];?></div><br />
    <div style="float:left"><input type="file" name="bulkcsvfile" /></div>
    <div style="float:left"><input type="submit" value="  <?php echo $lang["userlistsave"];?>  "  name="savebulk"/> &nbsp; <input type="button" value="  <?php echo $lang['userlistback'];?>  " onclick="javascript:CentralSpaceLoad('<?php echo $baseurl?>/pages/team/team_plugins.php')" /></div>
</form>
</div>
<?php
$savebulk = getvalescaped("savebulk",false);

if($savebulk){
	//print_r($_FILES);die;
	
			$tmp_name = $_FILES["bulkcsvfile"]["tmp_name"];
			$name = $_FILES["bulkcsvfile"]["name"];
			move_uploaded_file($tmp_name, "/tmp/$name");
		
// Set path to CSV file
	$csvFile = "/tmp/".$name;
	
	$csv = readCSV($csvFile);
	
	set_time_limit(0);
	echo "<br /><br /><div>Processing. Please Wait.<br />";
	foreach($csv as $dt){
		if(strtolower(trim($dt[0])) == "username"){
			continue;
		}
		ob_start();
		$usrid = sql_value("select ref as value from user where username = '".$dt[0]."'","");
		if($dt[1] != ""){
			$q = "insert into user_userlist(user,userlist_name,userlist_string) values('".$usrid."','". $dt[1] ."','". $dt[2] ."');";
			sql_query($q);
			echo $q."<br />";
		}
		ob_flush();
		ob_clean();
	}
	echo "</div>";
	$finalcount = sql_value("select count(*) as value from user_userlist",0);
	echo "<div><strong>Currently data: $finalcount records</strong></div>";
	set_time_limit(200);
}
function readCSV($csvFile){
	$file_handle = fopen($csvFile, 'r');
	while (!feof($file_handle) ) {
		$line_of_text[] = fgetcsv($file_handle,0,";","\n");
	}
	fclose($file_handle);
	return $line_of_text;
}

include "../../../include/footer.php";
?>