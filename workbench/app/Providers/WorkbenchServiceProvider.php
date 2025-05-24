<?php

namespace App\Providers;

use App\Papers\DataPanelContentPaper;
use App\Papers\EmptyPaper;
use App\Papers\HeavyStressTableContentTest;
use App\Papers\PaperWithRuntimeException;
use App\Papers\SimplePaper;
use App\Papers\TableContentPaper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
            PaperWithRuntimeException::class,
            DataPanelContentPaper::class,
            TableContentPaper::class,
            HeavyStressTableContentTest::class,
        ]);

        Papers::handlePaperNotFound(function (Request $request) {
            $papers = Papers::all();

            abort(response()
                ->view('papers::pages.404', ['papers' => $papers])
                ->setStatusCode(404)
            );
        });

        Gate::define('viewPapers', function ($user = null) {
            if (is_null($user)) {
                return app()->environment('local');
            }

            return true;
        });
    }
}
