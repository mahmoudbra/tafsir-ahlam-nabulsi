<?php 
ini_set('memory_limit', 32 * 1024 * 1024); 
ini_set("max_execution_time", "480"); 
$PHP_SELF = $_SERVER['PHP_SELF']; 

if ($_GET[xfer]) { 
	if ($_POST[from] == "")	{ 
		print "You forgot to enter a url."; 
	} else { 
	
	$source = "$_POST[from]";
	$destination = "uploads/$_POST[to]";

	$data = file_get_contents($source);

	$handle = fopen($destination, "w");
	fwrite($handle, $data);
	fclose($handle);
 
	$size = round((filesize($_POST[to])/1000000), 3); 
	print "transfer complete.<br> 
	<a><a href=\"$_POST[from]\">$_POST[from]</a><br> 
	<a><a href=\"uploads/$_POST[to]\">$_POST[to]</a> : $size MB"; 
	} 
} else { 
print "<form action=\"$PHP_SELF?xfer=true\" method=post> 
from(http://): <input name='from' value=''><br> 
to(filename): <input name='to'><br> 
<input type=submit value=\"transload\">"; 
} 
?>