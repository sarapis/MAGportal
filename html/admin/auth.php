<?php 
require_once '../../admin_include/loader.php';
$d = new SessionDataAdmin();
$v = View::engine();
if (!isset($_POST['email']))
{
	$v->redirect('welcome.php');
	die();
}
$token = $d->setEmail($_POST['email']);

$to = $d->isNew ? [$_POST['email'], 'New MAG Portal User'] : [$_POST['email'], $d->data['fields']['name']];
$resp = SendGridMail::authMail($to, $d->isNew, $token);

// DRAW PAGE

$v->session();
$v->simplePage('jumbo', 'Please check your email and click the "Log in" link.');