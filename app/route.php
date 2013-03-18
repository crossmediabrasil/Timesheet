<?php

// Protected routes
$_protected_routes = Array(
		'/'
);

$_public_routes = Array(
		'users/register'
);

if (in_array(System::getRoute(), $_protected_routes)) {
		System::redirect('users/signin');	
}
