<?php

namespace Tests\fixtures\Papers;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use Schrojf\Papers\Contents\FormContent;
use Schrojf\Papers\Paper;

class MultiActionTestPaper extends Paper
{
    protected ?string $rendered = null;

    public function sections(?string $page = null): array
    {
        if ($page === 'done') {
            return [
                'Result' => fn () => session('greeting', 'Nothing was done.'),
            ];
        }

        return [
            'Form' => fn () => FormContent::make(static::actionUrl('greet'))
                ->text('name', 'Name')
                ->checkbox('loud', 'Loud', true)
                ->submit('Greet'),
            'Rendered' => fn () => $this->rendered ?? 'Nothing was rendered.',
        ];
    }

    /**
     * The action name is passed after the dependencies, whatever its parameter is called.
     */
    public function action(Request $request, Repository $config, string $name): mixed
    {
        return match ($name) {
            'greet' => $this->greet($request, $config),
            'render' => $this->render(),
            default => abort(404),
        };
    }

    protected function greet(Request $request, Repository $config): mixed
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:10'],
        ]);

        return static::redirect('done')
            ->with('greeting', 'Hello, '.$validated['name'].' from '.$config->get('app.name').'!');
    }

    protected function render(): mixed
    {
        $this->rendered = 'Rendered by the render action.';

        return null;
    }
}
