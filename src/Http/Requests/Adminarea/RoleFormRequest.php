<?php

declare(strict_types=1);

namespace Cortex\Auth\Http\Requests\Adminarea;

use Illuminate\Foundation\Http\FormRequest;
use Cortex\Foundation\Exceptions\GenericException;

class RoleFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @throws \Cortex\Foundation\Exceptions\GenericException
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $currentUser = $this->user($this->route('guard'));

        if (optional($this->route('role'))->exists && ! $currentUser->can('superadmin') && ! $currentUser->roles->contains($this->route('role'))) {
            throw new GenericException(trans('cortex/auth::messages.action_unauthorized'), route('adminarea.roles.index'));
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }
}
