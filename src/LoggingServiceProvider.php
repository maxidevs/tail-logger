<?php

namespace Maxidev\Logger;

use Illuminate\Support\ServiceProvider;
use Maxidev\Logger\Commands\TailLogCommand;

class LoggingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->commands([
            TailLogCommand::class,
        ]);
    }

    public function boot(): void
    {
        //
    }
}
