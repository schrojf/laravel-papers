<?php

namespace Tests\fixtures\Papers;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use Schrojf\Papers\Paper;

class ActionTestPaper extends Paper
{
    protected ?string $greeting = null;

    public function sections(): array
    {
        return [
            'Greeting' => function () {
                return $this->greeting ?? 'No action was handled.';
            },
        ];
    }

    public function action(Request $request, Repository $config): mixed
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'respond' => ['nullable', 'in:redirect,json'],
        ]);

        $greeting = 'Hello, '.$validated['name'].' from '.$config->get('app.name').'!';

        if (($validated['respond'] ?? null) === 'redirect') {
            return redirect('/greeted');
        }

        if (($validated['respond'] ?? null) === 'json') {
            return response()->json(['greeting' => $greeting]);
        }

        $this->greeting = $greeting;

        return null;
    }
}
