<?php
$this->register(
	//Name
	'HelloAsso',
	// Short description
	'Register payments made through HelloAsso',
	// Author
	'Florian Hatat',
	// Version
	'1',
	// Galette version compatibility
	'0.9.6',
	// Routing name and translation domain
	'helloasso'
	// Date
	'2022-07-05',
	// Routes ACL
	[
		'helloasso_checkout' => 'member',
		'helloasso_payment_return' => 'member',
		'helloasso_payment_error' => 'member',
		'helloasso_settings' => 'admin',
		'helloasso_settings_save' => 'admin',
	]
);

$this->setCsrfExclusions([
	    '/helloasso_notify/',
]);
