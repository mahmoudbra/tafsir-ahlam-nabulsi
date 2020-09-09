<?php 
/*======================================================================*\
|| ###################################################################### ||
|| # Auto Clean Uploads Mirror version 1.0                              # || 
|| # $Date: 23-10-2012                                                  # || 
|| # ----------------------------------------------------------------   # ||
|| # Copyright ©2012-2012 qooymirrors.com. All Rights Reserved.         # ||
|| # This file may not be redistributed in whole or significant part.   # ||
|| # ---------------- qooy mirrors are NOT FREE SOFTWARE -------------- # ||
|| # http://www.qooymirrors.com                                         # ||
|| ###################################################################### ||
\*======================================================================*/
$files = glob('uploads/*.*');
foreach($files as $file) {
    unlink($file);
}
?>
