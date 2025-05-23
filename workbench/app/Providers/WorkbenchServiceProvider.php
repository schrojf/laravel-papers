<?php

namespace App\Providers;

use App\Papers\EmptyPaper;
use App\Papers\SimplePaper;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Schrojf\Papers\Papers;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Papers::register([
            SimplePaper::class,
            EmptyPaper::class,
        ]);

        Papers::handlePaperNotFound(function (Request $request) {
            $papers = Papers::all();

            abort(response()
                ->view('papers::pages.404', ['papers' => $papers])
                ->setStatusCode(404)
            );
        });
    }
}
