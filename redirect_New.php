<?php

require 'includes/db.inc.php';
require 'includes/configs.inc.php';
function sanitize($input){
    if(is_array($input)){
        foreach($input as $k=>$i){
            $output[$k]=sanitize($i);
        }
    }
    else{
        $output=mysql_real_escape_string($input);
    }   
   
    return $output;
}
$_GET=sanitize($_GET);

if (isset($_GET['uid']) && isset($_GET['hostid'])) {
	
$file_id = $_GET['uid'];
$host_id = $_GET['hostid'];
	
if ( eregi ( $site_domain, $_SERVER['HTTP_REFERER'] ) ) {



$data = mysql_query("SELECT url FROM mirror WHERE uid = '$file_id' AND hostid = '$host_id'") or die(mysql_error());
$mirror_info = mysql_fetch_array($data);
$url = $mirror_info['url'];

mysql_query("UPDATE mirror SET hits=hits+1 WHERE uid = '$file_id' AND hostid = '$host_id'") or die(mysql_error());
mysql_close();
if (isset($url)) {
	echo "
	
<html>
<frameset rows=\"0,*\" border=\"0\">
	<frame name=\"header\" scrolling=\"no\" noresize target=\"main\" src=\"\" >
	<frame name=\"main\" src=\"$url\">
	<noframes>
	<body>

	<p>This page uses frames, but your browser doesn't support them.</p>

	</body>
	</noframes>
</frameset>

</html>";


}
else
		header("Location: http://www.$site_domain/");
}

else {
  header("Location: http://www.$site_domain/download.php?uid=$file_id");
}
	
}


else {
	header("Location: http://www.$site_domain/");
}	

?>