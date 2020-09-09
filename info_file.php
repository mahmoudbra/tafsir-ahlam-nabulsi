<?php

// include header
include("header.php");

/* get file id */
$file_id = str_replace(".html", "", $_GET['info_id']);

// set page name
$page = "infofiles";

    // insert file in to database
$data = mysql_query("SELECT * FROM uploads WHERE file_id = '" . $file_id . "'");

// setup data
$file_name = $data['file_name'];
$del_id = $data['delete_id'];

// generate tiny url
$tiny_url = get_tiny_url($config['site_url'] . "/files/" . $file_id . ".html");								      

/* set template vars */
$tpl->file_id = $file_id;
$tpl->file_name = $file_name;
$tpl->del_id = $del_id;
$tpl->tiny_url = $tiny_url;
                                                                    ;

// include footer
include("footer.php");
?>