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
	
// is pending email update
	if ($d->delayedEmailUpdate)						///
	{
		$d->setDelayedEmail();
		$d->updateUser();
	}	

// is signed profile
	if ($_POST['src'] == 'profile')
	{
		$d->setProfile($_POST);
		if ($d->delayedEmailUpdate)					///
		{
			$d->isVerified = false;
			$v->simplePage('emailUpdate', $_POST['email']);
			die();
		}
		if ($d->delayedEmailIsDouble)					///
		{
			$d->delayedEmailIsDouble = false;
			$v->simplePage('emailUpdateFailed', $_POST['email']);
			die();
		}
	}	
	
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

// DRAW PAGE

$v->headers();
$v->dashboard($d->getUserView());
$v->session();
$v->intercom($d->getIntercomView());
$v->footers();
