<?php

namespace App\Papers;

use Illuminate\Http\Request;
use Schrojf\Papers\Contents\AlertContent;
use Schrojf\Papers\Contents\FormContent;
use Schrojf\Papers\Paper;

class PaperWithAction extends Paper
{
    public static $description = 'Paper with a form submitted to its action() method by a POST request.';

    /**
     * The greeting made by the action for the page rendered right after it.
     */
    protected ?string $greeting = null;

    public function sections(): array
    {
        $sections = [
            'Form' => fn () => FormContent::make(static::actionUrl())
                // Keeps the name on the page rendered right after the action, which has no old input.
                ->text('name', 'Name', request()->input('name'))
                ->submit('Greet on this page', 'intent', 'render')
                ->submit('Greet after redirect', 'intent', 'redirect', 'secondary')
                ->submit('Download greeting', 'intent', 'download', 'secondary'),
        ];

        // Set by the action, or flashed to the session by the action's redirect.
        $greeting = $this->greeting ?? session('greeting');

        if ($greeting) {
            $sections['Result'] = fn () => AlertContent::success($greeting);
        }

        return $sections;
    }

    public function action(Request $request): mixed
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'intent' => ['required', 'in:render,redirect,download'],
        ]);

        $greeting = 'Hello, '.$validated['name'].'!';

        // A returned response is sent to the browser as it is.
        if ($validated['intent'] === 'download') {
            return response()->streamDownload(function () use ($greeting) {
                echo $greeting;
            }, 'greeting.txt');
        }

        // The paper is loaded again by a GET request, so refreshing it does not repeat the action.
        if ($validated['intent'] === 'redirect') {
            return static::redirect()->with('greeting', $greeting);
        }

        // Nothing is returned, so this instance renders the paper and its sections can use the result.
        $this->greeting = $greeting;

        return null;
    }
}
