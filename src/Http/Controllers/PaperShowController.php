<?php

namespace Schrojf\Papers\Http\Controllers;

use Illuminate\Contracts\View\View;
use ReflectionMethod;
use Schrojf\Papers\Http\Requests\PaperRequest;
use Schrojf\Papers\Papers;

class PaperShowController
{
    /**
     * Handle the incoming request.
     *
     * The optional page name of the URL is passed to the paper's sections()
     * method. Papers whose sections() doesn't accept it have no named pages,
     * so their URLs with a page name are not found.
     */
    public function __invoke(PaperRequest $request): View
    {
        $papers = Papers::all();
        $paper = $request->paper();
        $page = $request->route('page');

        if (! is_null($page) && (new ReflectionMethod($paper, 'sections'))->getNumberOfParameters() === 0) {
            Papers::paperNotFound($request);
        }

        $content = (new $paper)->resolveContent($page);

        return view('papers::pages.paper', [
            'papers' => $papers,
            'paper' => $paper,
            'content' => $content,
        ]);
    }
}
