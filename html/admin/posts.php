<?php 
require_once '../../admin_include/loader.php';
$d = new SessionDataAdmin();
$v = View::engine();
if (!$d->isVerified || !$_POST['group_id'] || !$d->checkOrg($_POST['group_id']))
{
	$v->redirect('dashboard.php');
	die();
}

// is signed vendor request form
	if ($_POST['src'] == 'vrequest')
		$d->saveVrequest2airtable();

$d->updateOrg($_POST['group_id']);
$dataset = $d->getPostsView();


// DRAW PAGE

$v->headers();
$v->groupPosts($dataset);
$v->session();
$v->footers();