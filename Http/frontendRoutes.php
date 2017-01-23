<?php

use Illuminate\Routing\Router;

/** @var Router $router */
$router->get('form/business-information','PublicController@businessInformation');
$router->get('form/business-reference','PublicController@businessReference');
$router->get('form/loan-details','PublicController@loanDetails');
$router->get('form/business-owner','PublicController@businessOwner');
$router->get('form/authorization','PublicController@authorization');
$router->get('form/documents','PublicController@documents');
$router->get('form/personal','PublicController@personal');
$router->get('form/logout','PublicController@logout');
$router->get('form/finish','PublicController@finish');
$router->post('form/contact-us','PublicController@contactUs');
$router->resource('form', 'PublicController');