<?php

/* include header */
include("header.php");

/* set page name */
$page = "download";

/* get file id */
$file_id = str_replace(".html", "", $_GET['file_id']);

/* get file info based on vars passed */
$query = mysql_query("SELECT * FROM uploads WHERE file_id = '".$file_id."' LIMIT 1");
$file = mysql_fetch_array($query);

/* check if file exists */
if(mysql_num_rows($query) == 0){ $file_exists = 0; }else{ $file_exists = 1; }

/* generate a list of all available premium packages */
$premium = mysql_query("SELECT package_id, package_name, package_price FROM packages");

/* assign template vars */
$tpl->file = $file;
$tpl->file_exists = $file_exists;
$tpl->premium = $premium;

/* include footer */
include("footer.php");

?>