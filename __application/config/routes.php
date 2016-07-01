<?php defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'frontend';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['backoffice'] = 'backend';
$route['backoffice-masuk'] = 'login';
$route['backoffice-keluar'] = 'login/logout';
$route['backoffice-grid/(:any)'] = 'backend/get_grid/$1';
$route['backoffice-form/(:any)'] = 'backend/get_form/$1';



/* Routes Front End Routes */
$route['beranda'] = 'frontend/getdisplay/main_page/beranda';
$route['loading-beranda'] = 'frontend/getdisplay/loading_page/beranda';

$route['tentangkami'] = 'frontend/getdisplay/main_page/tentangkami';
$route['loading-tentangkami'] = 'frontend/getdisplay/loading_page/tentangkami';

$route['tracking'] = 'frontend/getdisplay/main_page/tracking';
$route['loading-tracking'] = 'frontend/getdisplay/loading_page/tracking';

$route['histori'] = 'frontend/getdisplay/main_page/histori';
$route['loading-histori'] = 'frontend/getdisplay/loading_page/histori';

$route['kontak'] = 'frontend/getdisplay/main_page/kontak';
$route['loading-kontak'] = 'frontend/getdisplay/loading_page/kontak';

$route['kategori/(:any)'] = 'frontend/getdisplay/main_page/kategori/$1';
$route['loading-kategori/(:any)'] = 'frontend/getdisplay/loading_page/kategori/$1';




