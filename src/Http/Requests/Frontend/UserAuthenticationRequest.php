<?php

declare(strict_types=1);

namespace Cortex\Fort\Http\Requests\Frontend;

use Rinvex\Support\Http\Requests\FormRequest;

class UserAuthenticationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'loginfield' => 'required',
            'password' => 'required',
        ];
    }
}
