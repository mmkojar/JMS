<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
// $route['students'] = 'students/students';
// $route['student/add'] = 'students/add_student';
// $route['student/update'] = 'students/update_student';
$route['users/edit/(:any)'] = 'auth/edit_user/$1';
$route['users'] = 'auth/index';
$route['users/add'] = 'auth/create_user';
$route['receipts'] = 'payment/receipts';
$route['revenue'] = 'payment/revenue';
$route['invoices'] = 'payment/invoices';
$route['reports/area'] = 'reports/area_reports';
$route['reports/surname'] = 'reports/surname_reports';
$route['reports/outstanding'] = 'reports/outstanding_reports';
$route['reports/user_outstanding'] = 'reports/user_outstanding_reports';
$route['reports/receipts'] = 'reports/receipts_reports';
$route['reports/death'] = 'reports/death_reports';
$route['reports/divorce'] = 'reports/divorce_reports';

$route['default_controller'] = 'Dashboard';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
