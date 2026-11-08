<?php

namespace App\Papers;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Schrojf\Papers\Contents\AlertContent;
use Schrojf\Papers\Contents\ButtonContent;
use Schrojf\Papers\Contents\DataPanelContent;
use Schrojf\Papers\Contents\FormContent;
use Schrojf\Papers\Contents\LinkContent;
use Schrojf\Papers\Contents\TableContent;
use Schrojf\Papers\Paper;

class MultiActionPaper extends Paper
{
    public static $description = 'Task list with several actions and pages. Every action redirects to a result page, so refreshing it does not run the action again.';

    public static $keywords = ['form', 'button', 'link', 'redirect', 'tasks', 'todo'];

    /**
     * The most tasks the list may have.
     */
    protected const LIMIT = 8;

    protected const PRIORITIES = [
        'low' => 'Low',
        'normal' => 'Normal',
        'high' => 'High',
    ];

    /**
     * Get the sections of the task list, or of its named page: /papers/multi-action-paper/{page}.
     */
    public function sections(?string $page = null): array
    {
        return match ($page) {
            null => $this->tasksPage(),
            'done' => $this->resultPage(),
            'history' => $this->historyPage(),
            default => abort(404),
        };
    }

    /**
     * Run the named action: POST /papers/multi-action-paper/{action}.
     */
    public function action(Request $request, string $action): RedirectResponse
    {
        $result = match ($action) {
            'create' => $this->create($request),
            'complete' => $this->markDone($request, true),
            'reopen' => $this->markDone($request, false),
            'delete' => $this->delete($request),
            'complete-all' => $this->completeAll(),
            'clear' => $this->clearCompleted(),
            'reset' => $this->reset(),
            default => abort(404),
        };

        $this->log($action, $result);

        // The result page is loaded by a GET request, so refreshing it doesn't submit the action again.
        return static::redirect('done')->with('result', $result);
    }

    protected function tasksPage(): array
    {
        $tasks = $this->tasks();
        $show = in_array(request()->query('show'), ['open', 'done'], true) ? request()->query('show') : 'all';

        return [
            'Tasks' => function () use ($tasks, $show) {
                $counts = [
                    'all' => count($tasks),
                    'open' => count(array_filter($tasks, fn (array $task) => ! $task['done'])),
                    'done' => count(array_filter($tasks, fn (array $task) => $task['done'])),
                ];

                $filters = array_map(
                    fn (string $filter, string $label) => LinkContent::make("{$label} ({$counts[$filter]})", static::url(query: $filter === 'all' ? [] : ['show' => $filter]))
                        ->button($filter === $show ? 'primary' : 'secondary'),
                    ['all', 'open', 'done'],
                    ['All', 'Open', 'Done'],
                );

                $visible = array_filter($tasks, fn (array $task) => match ($show) {
                    'open' => ! $task['done'],
                    'done' => $task['done'],
                    default => true,
                });

                if (empty($visible)) {
                    return [...$filters, AlertContent::info('No tasks to show here. Add one below, or reset the demo.')];
                }

                return [...$filters, TableContent::withHeaders(['#', 'Task', 'Priority', 'Status', '', ''])
                    ->rows(array_map(fn (array $task) => [
                        $task['id'],
                        $task['title'],
                        self::PRIORITIES[$task['priority']],
                        $task['done'] ? 'Done' : 'Open',
                        ButtonContent::make($task['done'] ? 'Reopen' : 'Complete', static::actionUrl($task['done'] ? 'reopen' : 'complete'))
                            ->hidden('task', $task['id']),
                        ButtonContent::make('Delete', static::actionUrl('delete'))
                            ->hidden('task', $task['id'])
                            ->danger()
                            ->confirm("Delete the task \"{$task['title']}\"?"),
                    ], $visible))
                    ->fit(),
                ];
            },

            'New task' => fn () => FormContent::make(static::actionUrl('create'))
                ->text('title', 'Title', attributes: ['placeholder' => 'Water the plants', 'autocomplete' => 'off'])
                ->select('priority', 'Priority', self::PRIORITIES, 'normal')
                ->checkbox('done', 'Already done')
                ->submit('Add task'),

            'All tasks' => fn () => [
                ButtonContent::make('Complete all', static::actionUrl('complete-all'))->primary(),
                ButtonContent::make('Delete completed', static::actionUrl('clear'))
                    ->danger()
                    ->confirm('Delete all completed tasks?'),
                ButtonContent::make('Reset demo', static::actionUrl('reset'))
                    ->confirm('Replace all tasks with the demo tasks?'),
            ],

            'How it works' => fn () => [
                'Forms, buttons and links get their URLs from the paper, so the URLs are never built by hand. '
                .'Click "Complete all" twice to see the result page of a failed action, then refresh it.',
                DataPanelContent::make()
                    ->item('static::url()', static::url())
                    ->item("static::url(query: ['show' => 'open'])", static::url(query: ['show' => 'open']))
                    ->item("static::url('history')", static::url('history'))
                    ->item("static::actionUrl('create')", static::actionUrl('create'))
                    ->item("static::redirect('done')", 'Redirect to '.static::url('done')),
                LinkContent::make('Show the activity of the actions', static::url('history')),
            ],
        ];
    }

    /**
     * The page the actions redirect to, showing the result they flashed to the session.
     */
    protected function resultPage(): array
    {
        $result = session('result');

        return [
            'Result' => function () use ($result) {
                if (! is_array($result)) {
                    return AlertContent::info('The result of an action is shown only once, right after it runs. Refreshing this page doesn\'t run the action again.')
                        ->title('Nothing new to show');
                }

                return $result['success']
                    ? AlertContent::success($result['message'])->title('Done')
                    : AlertContent::error($result['message'])->title('The action failed');
            },

            'Next' => fn () => [
                LinkContent::make('Back to tasks', static::url())->button('primary'),
                LinkContent::make('Activity', static::url('history'))->button(),
            ],
        ];
    }

    protected function historyPage(): array
    {
        $history = $this->store()->get('papers.multi-action-paper.history', []);

        return [
            'Activity' => fn () => empty($history)
                ? 'No action was run yet.'
                : TableContent::withHeaders(['Time', 'Action', 'Result', 'Message'])
                    ->rows(array_map(fn (array $entry) => [
                        $entry['time'],
                        $entry['action'],
                        $entry['success'] ? 'Success' : 'Failure',
                        $entry['message'],
                    ], $history))
                    ->fit(),

            'Next' => fn () => LinkContent::make('Back to tasks', static::url())->button('primary'),
        ];
    }

    protected function create(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:60'],
            'priority' => ['required', 'in:'.implode(',', array_keys(self::PRIORITIES))],
            'done' => ['nullable', 'boolean'],
        ]);

        $tasks = $this->tasks();

        if (count($tasks) >= self::LIMIT) {
            return $this->failure('The list is full, it can have '.self::LIMIT.' tasks. Delete a task before adding another one.');
        }

        $tasks[] = [
            'id' => max([0, ...array_column($tasks, 'id')]) + 1,
            'title' => $validated['title'],
            'priority' => $validated['priority'],
            'done' => $request->boolean('done'),
        ];

        $this->saveTasks($tasks);

        return $this->success("The task \"{$validated['title']}\" was added.");
    }

    protected function markDone(Request $request, bool $done): array
    {
        $id = (int) $request->validate(['task' => ['required', 'integer']])['task'];
        $tasks = $this->tasks();
        $key = array_search($id, array_column($tasks, 'id'), true);

        // A page opened before the task changed, e.g. in another tab or by the back button, may submit it again.
        if ($key === false) {
            return $this->failure("The task #{$id} doesn't exist. It may have been deleted in the meantime.");
        }

        $title = $tasks[$key]['title'];

        if ($tasks[$key]['done'] === $done) {
            return $this->failure($done ? "The task \"{$title}\" is done already." : "The task \"{$title}\" is open already.");
        }

        $tasks[$key]['done'] = $done;

        $this->saveTasks($tasks);

        return $this->success($done ? "The task \"{$title}\" is done." : "The task \"{$title}\" was reopened.");
    }

    protected function delete(Request $request): array
    {
        $id = (int) $request->validate(['task' => ['required', 'integer']])['task'];
        $tasks = $this->tasks();
        $key = array_search($id, array_column($tasks, 'id'), true);

        if ($key === false) {
            return $this->failure("The task #{$id} doesn't exist. It may have been deleted already.");
        }

        $title = $tasks[$key]['title'];

        unset($tasks[$key]);

        $this->saveTasks($tasks);

        return $this->success("The task \"{$title}\" was deleted.");
    }

    protected function completeAll(): array
    {
        $tasks = $this->tasks();
        $open = count(array_filter($tasks, fn (array $task) => ! $task['done']));

        if ($open === 0) {
            return $this->failure('There are no open tasks to complete.');
        }

        $this->saveTasks(array_map(fn (array $task) => ['done' => true] + $task, $tasks));

        return $this->success($open === 1 ? 'The last open task is done.' : "All {$open} open tasks are done.");
    }

    protected function clearCompleted(): array
    {
        $tasks = $this->tasks();
        $open = array_filter($tasks, fn (array $task) => ! $task['done']);
        $deleted = count($tasks) - count($open);

        if ($deleted === 0) {
            return $this->failure('There are no completed tasks to delete.');
        }

        $this->saveTasks($open);

        return $this->success($deleted === 1 ? '1 completed task was deleted.' : "{$deleted} completed tasks were deleted.");
    }

    protected function reset(): array
    {
        $this->saveTasks(self::demoTasks());

        return $this->success('The demo tasks are back.');
    }

    /**
     * @return array{success: true, message: string}
     */
    protected function success(string $message): array
    {
        return ['success' => true, 'message' => $message];
    }

    /**
     * @return array{success: false, message: string}
     */
    protected function failure(string $message): array
    {
        return ['success' => false, 'message' => $message];
    }

    /**
     * Keep the latest results of the actions for the activity page.
     *
     * @param  array{success: bool, message: string}  $result
     */
    protected function log(string $action, array $result): void
    {
        $history = $this->store()->get('papers.multi-action-paper.history', []);

        array_unshift($history, ['time' => now()->format('H:i:s'), 'action' => $action] + $result);

        $this->store()->forever('papers.multi-action-paper.history', array_slice($history, 0, 20));
    }

    /**
     * @return array<int, array{id: int, title: string, priority: string, done: bool}>
     */
    protected function tasks(): array
    {
        return $this->store()->get('papers.multi-action-paper.tasks') ?? self::demoTasks();
    }

    protected function saveTasks(array $tasks): void
    {
        $this->store()->forever('papers.multi-action-paper.tasks', array_values($tasks));
    }

    /**
     * The demo keeps its tasks in the file cache, so it works without a database.
     */
    protected function store(): Repository
    {
        return Cache::store('file');
    }

    protected static function demoTasks(): array
    {
        return [
            ['id' => 1, 'title' => 'Write the quarterly report', 'priority' => 'high', 'done' => false],
            ['id' => 2, 'title' => 'Review open pull requests', 'priority' => 'normal', 'done' => true],
            ['id' => 3, 'title' => 'Update the dependencies', 'priority' => 'low', 'done' => false],
            ['id' => 4, 'title' => 'Plan the team offsite', 'priority' => 'normal', 'done' => false],
        ];
    }
}
