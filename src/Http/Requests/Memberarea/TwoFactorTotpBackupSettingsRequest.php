<?php

declare(strict_types=1);

namespace Cortex\Fort\Http\Requests\Memberarea;

use Rinvex\Fort\Exceptions\GenericException;

class TwoFactorTotpBackupSettingsRequest extends TwoFactorTotpSettingsRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @throws \Rinvex\Fort\Exceptions\GenericException
     *
     * @return bool
     */
    public function authorize()
    {
        parent::authorize();

        $user = $this->user();
        $twoFactor = $user->getTwoFactor();

        if (empty($twoFactor['totp']['enabled'])) {
            throw new GenericException(trans('cortex/fort::messages.verification.twofactor.totp.cant_backup'), route('memberarea.account.settings'));
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}