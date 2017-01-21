<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/form'], function (Router $router) {
    $router->bind('form', function ($id) {
        return app('Modules\Form\Repositories\FormRepository')->find($id);
    });
    $router->get('forms', [
        'as' => 'admin.form.form.index',
        'uses' => 'FormController@index',
        'middleware' => 'can:form.forms.index'
    ]);
    $router->get('forms/create', [
        'as' => 'admin.form.form.create',
        'uses' => 'FormController@create',
        'middleware' => 'can:form.forms.create'
    ]);
    $router->post('forms', [
        'as' => 'admin.form.form.store',
        'uses' => 'FormController@store',
        'middleware' => 'can:form.forms.store'
    ]);
    $router->get('forms/{form}/edit', [
        'as' => 'admin.form.form.edit',
        'uses' => 'FormController@edit',
        'middleware' => 'can:form.forms.edit'
    ]);
    $router->put('forms/{form}', [
        'as' => 'admin.form.form.update',
        'uses' => 'FormController@update',
        'middleware' => 'can:form.forms.update'
    ]);
    $router->delete('forms/{form}', [
        'as' => 'admin.form.form.destroy',
        'uses' => 'FormController@destroy',
        'middleware' => 'can:form.forms.destroy'
    ]);
// append

});
