<?php

declare(strict_types=1);

namespace Cortex\Fort\Transformers\Tenantarea;

use League\Fractal\TransformerAbstract;
use Rinvex\Fort\Contracts\UserContract;

class UserTransformer extends TransformerAbstract
{
    /**
     * @return array
     */
    public function transform(UserContract $user)
    {
        return [
            'id' => (int) $user->id,
            'username' => (string) $user->username,
            'first_name' => (string) $user->first_name,
            'last_name' => (string) $user->last_name,
            'email' => (string) $user->email,
            'email_verified' => (bool) $user->email_verified,
            'email_verified_at' => (string) $user->email_verified_at,
            'phone' => (string) $user->phone,
            'phone_verified' => (bool) $user->phone_verified,
            'phone_verified_at' => (string) $user->phone_verified_at,
            'country_code' => (string) $user->country_code ? $user->country->getEmoji().'&nbsp;&nbsp;'.$user->country->getName() : '',
            'created_at' => (string) $user->created_at,
            'updated_at' => (string) $user->updated_at,
        ];
    }
}
