<?php

namespace Schrojf\Papers\Contents;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\View;
use Throwable;

class ExceptionContent extends Content implements Renderable
{
    protected Throwable $throwable;

    public function __construct(Throwable $throwable)
    {
        $this->throwable = $throwable;
    }

    public function render()
    {
        return View::make('papers::components.exception', [
            'name' => get_class($this->throwable),
            'message' => $this->throwable->getMessage(),
        ])->render();
    }

    public function toHtml()
    {
        return $this->render();
    }
}
