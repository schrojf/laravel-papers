<?php

namespace Schrojf\Papers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Schrojf\Papers\Papers;

class PaperRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }

    /**
     * Get the class name of the paper page being requested.
     *
     * @return class-string<\Schrojf\Papers\Paper>
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function paper(): string
    {
        $paper = once(function () {
            return Papers::paperForHandler($this->route('paper'));
        });

        if (is_null($paper)) {
            Papers::paperNotFound($this);
        }

        return $paper;
    }
}
