<?php
require_once '../../../admin_include/loader.php';
$d = new SessionDataAdmin();
if ($_POST['key'])
	$d->attachmentDelete($_POST['key']);
?>{}