<?php

namespace App\Http\Middleware;

use App\Models\Utility;
use Closure;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class XSS
{
    use \RachidLaasri\LaravelInstaller\Helpers\MigrationsHelper;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::check())
        {
            \App::setLocale(Auth::user()->lang);

            if(Auth::user()->type == 'admin')
            {
                $migrations             = $this->getMigrations();
                $messengerMigration     = Utility::get_messenger_packages_migration();
                $dbMigrations           = $this->getExecutedMigrations();
                $numberOfUpdatesPending = (count($migrations) + $messengerMigration) - count($dbMigrations);

                if($numberOfUpdatesPending > 0)
                {
                    Artisan::call('cache:forget spatie.permission.cache');
                    Artisan::call('cache:clear');

                    return redirect()->route('LaravelUpdater::welcome');
                }
            }
        }

        $input = $request->all();
        array_walk_recursive(
            $input, function (&$input){
            $input = strip_tags($input);
        }
        );
        $request->merge($input);

        return $next($request);
    }
}
