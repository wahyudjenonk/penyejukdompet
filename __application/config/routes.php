<?php defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'frontend';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['backoffice'] = 'backend';
$route['backoffice-masuk'] = 'login';
$route['backoffice-keluar'] = 'login/logout';
$route['backoffice-grid/(:any)'] = 'backend/get_grid/$1';
$route['backoffice-form/(:any)'] = 'backend/get_form/$1';


