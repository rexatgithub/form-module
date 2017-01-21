<?php namespace Modules\Form\Providers;

use Illuminate\Support\ServiceProvider;

class FormServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    private function registerBindings()
    {
        $this->app->bind(
            'Modules\Form\Repositories\FormRepository',
            function () {
                $repository = new \Modules\Form\Repositories\Eloquent\EloquentFormRepository(new \Modules\Form\Entities\Form());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Form\Repositories\Cache\CacheFormDecorator($repository);
            }
        );
// add bindings

    }
}
