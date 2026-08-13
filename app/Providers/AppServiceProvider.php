<?php

namespace App\Providers;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use App\Models\ContactInfo;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        
        // Share contact info with all views
        try {
            $contactInfo = ContactInfo::first();
            View::share('contactInfo', $contactInfo);
        } catch (\Exception $e) {
            // Log error or ignore if table doesn't exist yet (e.g. during migration)
        }
        
        // Dynamic Meta from JSON config
        try {
            $metaPath = resource_path('meta/website-meta.json');
            if (file_exists($metaPath)) {
                $config = json_decode(file_get_contents($metaPath), true);
                if (is_array($config)) {
                    $routeName = Route::currentRouteName();
                    $entry = $config[$routeName] ?? ($config['default'] ?? null);
                    if ($entry) {
                        View::share('meta_title', $entry['title'] ?? null);
                        View::share('meta_description', $entry['description'] ?? null);
                        View::share('meta_keywords', $entry['keywords'] ?? null);
                    }
                }
            }
        } catch (\Exception $e) {
        }
    }
}
