<?php

namespace Schrojf\Papers\Contents;

use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\HtmlString;
use Illuminate\Support\ViewErrorBag;
use InvalidArgumentException;
use Stringable;

/**
 * A form submitting a POST request to an action, such as a paper's actionUrl().
 *
 * It has a CSRF token, and when the action fails validation, its fields show
 * the old input and the errors of the request, as anywhere else in Laravel.
 */
class FormContent extends Content implements Renderable
{
    /**
     * The input types which have methods of their own.
     */
    protected const RESERVED_TYPES = ['checkbox', 'radio', 'hidden', 'submit', 'button', 'reset', 'image'];

    protected string $action;

    /**
     * @var array<int, array<string, mixed>>
     */
    protected array $fields = [];

    /**
     * @var array<string, mixed>
     */
    protected array $hidden = [];

    /**
     * @var array<int, array<string, mixed>>
     */
    protected array $buttons = [];

    protected ?string $confirm = null;

    public function __construct(string $action)
    {
        $this->action = $action;
    }

    public static function make(string $action): self
    {
        return new self($action);
    }

    /**
     * Add an input of the given type, such as text, number, email, date or file.
     *
     * @param  array<string, mixed>  $attributes  Other attributes, such as placeholder, required, min or max.
     */
    public function input(string $name, string|Stringable $label, string $type = 'text', mixed $value = null, array $attributes = []): self
    {
        if (in_array($type, self::RESERVED_TYPES, true)) {
            throw new InvalidArgumentException("Input type [{$type}] is not supported. Use the {$type}() method or button() instead.");
        }

        return $this->field($type, $name, $label, $value, $attributes);
    }

    /**
     * Add a text input.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function text(string $name, string|Stringable $label, mixed $value = null, array $attributes = []): self
    {
        return $this->field('text', $name, $label, $value, $attributes);
    }

    /**
     * Add a number input.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function number(string $name, string|Stringable $label, mixed $value = null, array $attributes = []): self
    {
        return $this->field('number', $name, $label, $value, $attributes);
    }

    /**
     * Add a multi-line text input.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function textarea(string $name, string|Stringable $label, mixed $value = null, array $attributes = []): self
    {
        return $this->field('textarea', $name, $label, $value, $attributes);
    }

    /**
     * Add a select of the given options, keyed by their values.
     *
     * @param  array<array-key, string|Stringable>  $options
     * @param  array<string, mixed>  $attributes
     */
    public function select(string $name, string|Stringable $label, array $options, mixed $value = null, array $attributes = []): self
    {
        return $this->field('select', $name, $label, $value, $attributes, ['options' => $options]);
    }

    /**
     * Add a checkbox. It sends "1" when checked, and nothing otherwise.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function checkbox(string $name, string|Stringable $label, bool $checked = false, array $attributes = []): self
    {
        return $this->field('checkbox', $name, $label, $checked, $attributes);
    }

    /**
     * Send the given value with the form.
     */
    public function hidden(string $name, mixed $value): self
    {
        $this->hidden[$name] = $value;

        return $this;
    }

    /**
     * Add a submit button. A name and a value tell the action which button submitted the form.
     */
    public function submit(string|Stringable $label, ?string $name = null, ?string $value = null, string $style = 'primary'): self
    {
        $this->buttons[] = [
            'label' => $label,
            'name' => $name,
            'value' => $value,
            'style' => ButtonContent::validStyle($style),
        ];

        return $this;
    }

    /**
     * Ask the user to confirm the form with the given message before it is submitted.
     */
    public function confirm(?string $message): self
    {
        $this->confirm = $message;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $extra
     */
    protected function field(string $type, string $name, string|Stringable $label, mixed $value, array $attributes, array $extra = []): self
    {
        $this->fields[] = [
            'type' => $type,
            'name' => $name,
            'label' => $label,
            'value' => $value,
            'attributes' => $attributes,
        ] + $extra;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'action' => $this->action,
            'fields' => $this->fields,
            'hidden' => $this->hidden,
            'buttons' => $this->buttons,
            'confirm' => $this->confirm,
        ];
    }

    /**
     * Get the fields with the old input and the errors of the request they are rendered for.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function resolveFields(): array
    {
        $request = request();
        $errors = $request->hasSession() ? $request->session()->get('errors') : null;
        $hasOldInput = ! empty($request->old());

        return array_map(function (array $field) use ($request, $errors, $hasOldInput) {
            // Inputs named like "items[0]" are found by "items.0" in the old input and the errors.
            $key = rtrim(str_replace(['[]', '[', ']'], ['', '.', ''], $field['name']), '.');

            if ($field['type'] === 'checkbox') {
                // An unchecked checkbox isn't sent, so it is missing from the old input.
                $field['value'] = $hasOldInput ? (bool) $request->old($key) : $field['value'];
            } elseif ($field['type'] !== 'file') {
                $field['value'] = $request->old($key, $field['value']);
            }

            $field['error'] = $errors instanceof ViewErrorBag || $errors instanceof MessageBag
                ? $errors->first($key)
                : null;

            $field['attributes'] = static::renderAttributes($field['attributes']);

            return $field;
        }, $this->fields);
    }

    /**
     * Render the given attributes of a field. True renders a boolean attribute, false or null nothing.
     *
     * @param  array<string, mixed>  $attributes
     */
    protected static function renderAttributes(array $attributes): HtmlString
    {
        $html = [];

        foreach ($attributes as $name => $value) {
            if ($value === false || is_null($value)) {
                continue;
            }

            $html[] = $value === true ? e($name) : e($name).'="'.e($value).'"';
        }

        return new HtmlString(implode(' ', $html));
    }

    public function render()
    {
        return view('papers::components.form', array_merge($this->toArray(), [
            'fields' => $this->resolveFields(),
            'multipart' => in_array('file', array_column($this->fields, 'type'), true),
        ]))->render();
    }

    public function toHtml()
    {
        return $this->render();
    }
}
