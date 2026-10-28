<?php

namespace Schrojf\Papers\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Schrojf\Papers\Paper;
use Schrojf\Papers\Papers;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * @return array<string, ValidationRule|array<mixed>|string>
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
     * @return class-string<Paper>
     *
     * @throws NotFoundHttpException
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
