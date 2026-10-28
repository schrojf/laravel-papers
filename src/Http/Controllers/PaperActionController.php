<?php

namespace Schrojf\Papers\Http\Controllers;

use Schrojf\Papers\Http\Requests\PaperRequest;
use Schrojf\Papers\Papers;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class PaperActionController
{
    /**
     * Handle the incoming request.
     *
     * Papers handle actions by defining an optional action() method. Its
     * dependencies are resolved from the container. Whatever it returns is
     * sent as the response; when it returns nothing, the paper page is
     * rendered by the same instance, so sections can show its result.
     */
    public function __invoke(PaperRequest $request): mixed
    {
        $papers = Papers::all();
        $paper = $request->paper();

        if (! method_exists($paper, 'action')) {
            throw new MethodNotAllowedHttpException(['GET', 'HEAD'], 'Paper does not handle actions.');
        }

        $instance = new $paper;

        $response = app()->call([$instance, 'action']);

        if (! is_null($response)) {
            return $response;
        }

        return view('papers::pages.paper', [
            'papers' => $papers,
            'paper' => $paper,
            'content' => $instance->resolveContent(),
        ]);
    }
}
