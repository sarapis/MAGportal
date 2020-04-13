<?php 
require_once '../../admin_include/loader.php';
$d = new SessionDataAdmin();
$v = View::engine();
// is user authenticated
	if (!$d->isVerified && (!isset($_GET['token']) || !$d->checkAuthToken($_GET['token'])))
	{
		$v->redirect('welcome.php');
		die();
	}

$userDS = $d->getUserView();

// DRAW PAGE

$v->headers();
$v->profileForm($userDS);
$v->session();
$v->footers();
