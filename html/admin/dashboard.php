<?php 
require_once '../../admin_include/loader.php';
$d = new SessionDataAdmin();
$v = View::engine();
// is user authenticated
	if (!$d->isVerified && (!isset($_GET['token']) || !$d->checkAuthToken($_GET['token'])))
	{
		if ($d->embeddedAuth)
		{	
			$d->embeddedAuth = false;
			$v->redirect('https://mutualaid.nyc/manage-your-group/');
		}
		else
			$v->redirect('welcome.php');
	}	
	$d->isVerified = true;

// embedded authentication
	if ($d->embeddedAuth)
	{	
		$d->embeddedAuth = false;
		$v->redirect('https://mutualaid.nyc/manage-your-group/');
	}
	
// is signed profile
	if ($_POST['src'] == 'profile')
		$d->setProfile($_POST);
	
// is user just registered - sign profile
	if ($d->userIsNew())
		$v->redirect('profile.php');
	
// is signed organization form
	if ($_POST['src'] == 'organization')
		$d->save2airtable();

// is deleted organization
	if ($_POST['src'] == 'delete_org')
		$d->deleteOrg($_POST['delete_group_id']);
	

$d->updateUser();
$userDS = $d->getUserView();

// DRAW PAGE

$v->headers();
$v->dashboard($userDS);
$v->session();
$v->footers();
