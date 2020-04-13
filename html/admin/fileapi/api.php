<?php
require_once '../../../admin_include/loader.php';
$d = new SessionDataAdmin();
foreach ($_FILES as $fv=>$files)
	echo json_encode($d->attachmentUpload($fv, $files));
