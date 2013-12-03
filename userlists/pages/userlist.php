<?php
/**
 * Userlist page (part of Team Center)
 * 
 * 
 */
include "../../../include/db.php";
include "../../../include/authenticate.php";
include "../../../include/general.php";if (!checkperm("t")) {exit ("Permission denied.");}
include "../../../include/reporting_functions.php";
include "../../../include/header.php";	
$ref = getval("ref","");
$col = getval("group","");
$key = getval("key","");
$limit = getval("limit","101");
$viewres = getval("viewres",false);
$ord = getval("ord","b.fullname");
$res = array();
$qkey = "";
$offset=getvalescaped("offset",0);
if($key != ""){
	$qkey = "where (b.username like '%$key%' or b.fullname like '%$key%' or a.userlist_name like '%$key%')";
}	

$saveform = getval("saveform","");
if($saveform && $key == ""){
	$ref = getval("ref","");
	$userlistname = getval("userlistname","");
	$userliststring = getval("userliststring","");
	$deleterec = getval("deleterec",false);
	if($deleterec){
		$qupd = "delete from user_userlist where ref = '$ref';";
	}else{
		$qupd = "update user_userlist set userlist_name = '$userlistname' , userlist_string ='$userliststring' where ref = '$ref';";
	}
	sql_query($qupd);

}
# pager
$per_page=100;
		
$url=$baseurl ."/plugins/userlists/pages/userlist.php?paging=true&key=".$key;
if(!$viewres){
			$q = "select a.ref as id, a.user as userid, a.userlist_name, a.userlist_string,b.fullname from user_userlist a inner join user b on (a.user = b.ref)
			$qkey
			order by $ord desc
			limit $offset,$per_page
			 ";
			//echo $q;
		$res = sql_query($q);
		if($qkey){
			$srcq ="select count(*) value from user_userlist a inner join user b on (a.user = b.ref) $qkey";
		}else{
			$srcq ="select count(*) value from user_userlist ";
		}
		$results=sql_value($srcq,"0");
		//echo "select count(*) value from user_userlist a inner join user b on (a.user = b.ref) $qkey";
		$totalpages=ceil($results/$per_page);
		$curpage=floor($offset/$per_page)+1;
		$jumpcount=1;
		//
		?>
		
		
			
		<table width="100%" cellpadding="0" cellspacing="5">
		  <tr>
			<td colspan="3"><a href="<?php echo $baseurl;?>/pages/team/team_home.php"><?php echo $lang["backcenterhome"];?></a></td>
		   </tr>
		   <tr>
			<td colspan="3"><h1>Userlist Manage</h1></td>
		   </tr> 	
		   <tr>
			<td colspan="3"><form name="frm" method="post"><input type="text" value="<?php echo $key;?>" name="key" /> <input type="submit" value=" Search " /></form></td>
		   </tr> 	
		   <tr>
			<td colspan="3">
			 <?php     if (count($res) > 0){ ?>
			 <div class="BasicsBox"> 
			 <div style="text-align:right;margin-bottom:10px;"><?php pager(false); ?>  </div>
			<table width="100%" border="1" cellspacing="0" cellpadding="0" class="ListviewStyle">
			  <tr>
				<td style="background-color:#ccc;font-size:14px;font-weight:bold;color:#000;" height="30" align="center"><strong>User</strong></td>
				<td style="background-color:#ccc;font-size:14px;font-weight:bold;color:#000;" align="center"><strong>Userlist Name</strong></td>
				<td style="background-color:#ccc;font-size:14px;font-weight:bold;color:#000;" align="center"><strong>Userlist String</strong></td>
				<td style="background-color:#ccc;font-size:14px;font-weight:bold;color:#000;" align="center"><strong>Actions</strong></td>
			  </tr> 
			
			  <?php
			  $lastcart = 0;
			 
			for($i = 0; $i < count($res); $i++) {
				$a = $res[$i]['fullname'];
				echo '<tr>';
				$rowspan = 0;
				for(
					$j=$i;
					$res[$i]['userid'] !== $lastcart &&
					$j < count($res) && 
					$res[$j]['userid']==$res[$i]['userid'];
					$j++
				){
					$rowspan++ ;
				}
				if($rowspan > 0){
					$lastcart = $res[$i]['userid'] ;
					echo '<td rowspan="'.$rowspan.'" valign="top"><div class="ListTitle"><a href="'.$baseurl.'/pages/team'.'/team_user_edit.php?ref='.$res[$i]["userid"].'&loginas=true">'.$a.'</a></div></td>';
				}
				?>  
				<td align="left"><?php echo $res[$i]["userlist_name"];?>&nbsp;</td>
				<td align="left"><?php echo substr($res[$i]["userlist_string"],0,50);?>&nbsp;...</td>
				<td align="center"><a href="./userlist.php?viewres=true&ref=<?php echo $res[$i]["id"];?>&limit=<?php echo $limit;?>&name=<?php echo $res[$i]["fullname"];?>">Edit</a>&nbsp;</td>
			  </tr>
				<?php
				
				} 
			  ?>
		 
			</table>
		   </div>
		<?php } ?>
			</td>
			</tr>
		</table>

<?php }elseif($viewres){ 
	$name = getval("name","");
	$ref = getval("ref","");
	
      $qres = "select * from user_userlist where ref = '$ref'";
		$view = sql_query($qres);?>	
        <div><h2><?php echo $name;?></h2></div>
        <div><a href="./userlist.php?offset=<?php echo $offset;?>&limit=<?php echo $limit;?>">Back to result</a></div>
        <div class="Listview">
        <form name="frm" method="post">
        <table border="0">
        <?php 
	    $n=0;
		foreach($view as $ger){ ?>
			<tr>
                <td>Userlist Name</td><td valign="top"> <input size="50" type="text" name="userlistname" value="<?php echo $ger["userlist_name"];?>" /></td>
            </tr>
            <tr>    
                <td>Userlist String</td><td valign="top"> <textarea cols="70" rows="5" type="text" name="userliststring"><?php echo $ger["userlist_string"];?></textarea></td>
            </tr>
            <tr>    
                <td>Delete this record?</td><td valign="top"> <input type="checkbox" name="deleterec" value="true"><input type="hidden" name="ref" value="<?php echo $ger["ref"];?>" /><input type="hidden" name="viewres" value="" /></td>
            </tr>    
              
		<?php }
		?>
            <tr>
            	<td></td>
            	<td><input name="saveform" type="submit" value="  <?php echo $lang['userlistsave'];?>  " /> <input type="button" value="  <?php echo $lang['userlistback'];?>  " onclick="javascript:CentralSpaceLoad('<?php echo $url?>&offset=<?php echo $offset?>')" /></td>
             </tr>
        </table>
         </form>
		</div>
<?php 
}
		

function formatBytes($size, $precision = 2)
{
	if($size){
		$base = log($size) / log(1024);
		$suffixes = array('', ' kb', ' Mb', ' Gb', ' Tb');   
	
		return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
	}else{
		return "0";
	}
}
	
	
	?>
</form>
<?php include "../../../include/footer.php"; ?>