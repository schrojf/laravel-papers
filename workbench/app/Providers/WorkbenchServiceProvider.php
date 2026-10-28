<?php

namespace App\Providers;

use App\Papers\DataPanelContentPaper;
use App\Papers\EmptyPaper;
use App\Papers\FullWidthPaper;
use App\Papers\HeavyStressTableContentTest;
use App\Papers\LocalizedNamePaper;
use App\Papers\PaperWithAction;
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
            PaperWithAction::class,
            PaperWithRuntimeException::class,
            DataPanelContentPaper::class,
            TableContentPaper::class,
            HeavyStressTableContentTest::class,
            FullWidthPaper::class,
            LocalizedNamePaper::class,
        ]);

        Papers::favicon("data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📄</text></svg>");

        Papers::head(fn () => view('papers-head'));

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
