<?php

namespace Schrojf\Papers\Http\Controllers;

use Illuminate\Contracts\View\View;
use Schrojf\Papers\Http\Requests\PaperRequest;
use Schrojf\Papers\Papers;

class PaperIndexController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(PaperRequest $request): View
    {
        $papers = Papers::all();

        return view('papers::pages.index', ['papers' => $papers]);
    }
}
