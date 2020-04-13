<?php 
require_once '../../admin_include/loader.php';
$d = new SessionDataAdmin();
$d->reset();
$v = View::engine();


// DRAW PAGE

$v->simplePage('jumbo', 'You have logged out<br/><small><a href="email.php">Sign in again</a></small>');
