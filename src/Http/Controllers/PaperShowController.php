<?php

namespace Schrojf\Papers\Http\Controllers;

use Illuminate\Contracts\View\View;
use Schrojf\Papers\Http\Requests\PaperRequest;
use Schrojf\Papers\Papers;

class PaperShowController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(PaperRequest $request): View
    {
        $papers = Papers::all();
        $paper = $request->paper();

        return view('papers::pages.paper', [
            'papers' => $papers,
            'paper' => $paper,
        ]);
    }
}
