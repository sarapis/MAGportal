<?php 
require_once '../../admin_include/loader.php';
$d = new SessionDataAdmin();
$v = View::engine();
if (!$d->isVerified || !$_POST['group_id'] || !$d->checkOrg($_POST['group_id']))
{
	$v->redirect('posts.php');
	die();
}

$d->updateOrg($_POST['group_id']);
$dataset = $d->getGroupView();		// !! orgView=groupView for new request


// DRAW PAGE

$v->headers();
$v->volunteerRequestPage($dataset);
$v->session();
$v->footers();