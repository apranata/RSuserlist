<?php

include "../../../include/db.php";
include "../../../include/authenticate.php"; if (!checkperm("s")) {exit ("Permission denied.");}
include "../../../include/general.php";
include "../../../include/header.php";	
$q = "SELECT a.id, a.ref, a.user,b.fullname, a.datestamp, a.content, a.statusemail FROM report_resource a inner join user b on (a.user = b.ref)
	order by datestamp desc";
	//print $q;
	$res = sql_query($q);
		
if (count($res) > 0){ ?>
	<table>
    <tr>
    <td colspan="3"><a href="../../pages/team/team_home.php">Back to team center home</a></td>
   </tr>
   <tr>
    <td colspan="3"><h1>Report Summary</h1></td>
   </tr> 	
   </table> 
  <div class="BasicsBox"> 
    <table width="90%" border="1" bordercolorlight="#CCCCCC" bordercolor="#CCCCCC" cellspacing="0" cellpadding="0" class="ListviewStyle">
      <tr class="ListviewTitleStyle">
        <td height="47" align="center"><strong>User</strong></td>
        <td align="center"><strong>Date</td>
        <td align="center"><strong>Content</strong></td>
        <td align="center"><strong>Status Email</strong></td>
        <td align="center"><strong>Results Reported</strong></td>
      </tr> 
      <?php
      $e=0;
      foreach($res as $fed){ ?>
      <tr>
        <td align="left" valign="top"><div class="ListTitle"><?php echo $fed["fullname"];?></div></td>
        <td align="right"><?php echo $fed["datestamp"];?>&nbsp;</td>
        <td align="left"><?php echo $fed["content"];?>&nbsp;</td>
        <td align="center"><?php if($fed["statusemail"]== "1") { echo "sent"; }else{ echo "pending";}?>&nbsp;</td>
        <td align="center"><a href="<?php echo $baseurl;?>/pages/view.php?ref=<?php echo $fed["ref"];?>">View Resource</a>&nbsp;</td>
        
      </tr>
      <?php } ?>
    </table>
    </div>
<?php 
	}


?>