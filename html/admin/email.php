<?php 
require_once '../../admin_include/loader.php';
$d = new SessionDataAdmin();
$v = View::engine();
if ($d->isVerified)
{
	$v->redirect('dashboard.php');
	die();
}
$d->reset();

// DRAW PAGE

$v->simplePage('emailForm');