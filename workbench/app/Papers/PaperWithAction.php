<?php

namespace App\Papers;

use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
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
            'Form' => fn () => $this->renderForm(),
        ];

        // Set by the action, or flashed to the session by the action's redirect.
        $greeting = $this->greeting ?? session('greeting');

        if ($greeting) {
            $sections['Result'] = fn () => $greeting;
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
            return redirect()->route('papers.show', static::handler())->with('greeting', $greeting);
        }

        // Nothing is returned, so this instance renders the paper and its sections can use the result.
        $this->greeting = $greeting;

        return null;
    }

    protected function renderForm(): HtmlString
    {
        $error = session('errors')?->first('name');
        $name = old('name', request()->input('name'));

        $button = 'padding: 0.5em 1em; border: 1px solid #bfdbfe; border-radius: 0.25em; background: #eff6ff; color: #2563eb; cursor: pointer;';

        return new HtmlString('
            <form method="POST" action="'.e(route('papers.action', static::handler())).'" style="display: flex; flex-direction: column; gap: 0.75em; max-width: 32em;">
                '.csrf_field().'
                <label>
                    <span style="display: block; font-weight: 500;">Name</span>
                    <input type="text" name="name" value="'.e($name).'" style="width: 100%;">
                </label>
                '.($error ? '<p style="color: #b91c1c;">'.e($error).'</p>' : '').'
                <div style="display: flex; flex-wrap: wrap; gap: 0.5em;">
                    <button type="submit" name="intent" value="render" style="'.$button.'">Greet on this page</button>
                    <button type="submit" name="intent" value="redirect" style="'.$button.'">Greet after redirect</button>
                    <button type="submit" name="intent" value="download" style="'.$button.'">Download greeting</button>
                </div>
            </form>
        ');
    }
}
