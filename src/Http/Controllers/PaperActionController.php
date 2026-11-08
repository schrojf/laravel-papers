<?php

namespace Schrojf\Papers\Http\Controllers;

use Illuminate\Support\Reflector;
use ReflectionMethod;
use Schrojf\Papers\Http\Requests\PaperRequest;
use Schrojf\Papers\Papers;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class PaperActionController
{
    /**
     * Handle the incoming request.
     *
     * Papers handle actions by defining an optional action() method. Its
     * dependencies are resolved from the container, and the optional action
     * name of the URL is passed to it. Whatever it returns is sent as the
     * response; when it returns nothing, the paper page is rendered by the
     * same instance, so sections can show its result.
     */
    public function __invoke(PaperRequest $request): mixed
    {
        $papers = Papers::all();
        $paper = $request->paper();

        if (! method_exists($paper, 'action')) {
            throw new MethodNotAllowedHttpException(['GET', 'HEAD'], 'Paper does not handle actions.');
        }

        $parameters = $this->actionParameters($paper, $request->route('action'));

        if (is_null($parameters)) {
            Papers::paperNotFound($request);
        }

        $instance = new $paper;

        $response = app()->call([$instance, 'action'], $parameters);

        if (! is_null($response)) {
            return $response;
        }

        return view('papers::pages.paper', [
            'papers' => $papers,
            'paper' => $paper,
            'content' => $instance->resolveContent(),
        ]);
    }

    /**
     * Get the parameters passing the action name of the URL to the paper's action() method.
     *
     * Like route parameters of controllers, the name is passed to the first parameter
     * which isn't a dependency, whatever its name. There is no such action when the
     * method has no such parameter, or when it requires a name the URL doesn't have.
     *
     * @param  class-string  $paper
     * @return array<string, string|null>|null
     */
    protected function actionParameters(string $paper, ?string $action): ?array
    {
        foreach ((new ReflectionMethod($paper, 'action'))->getParameters() as $parameter) {
            // Classes, and values of attributes like #[Config('app.name')], are resolved from the container.
            if (Reflector::getParameterClassNames($parameter) !== [] || $parameter->getAttributes() !== []) {
                continue;
            }

            if (! is_null($action)) {
                return [$parameter->getName() => $action];
            }

            if ($parameter->isDefaultValueAvailable()) {
                return [];
            }

            return $parameter->allowsNull() ? [$parameter->getName() => null] : null;
        }

        return is_null($action) ? [] : null;
    }
}
