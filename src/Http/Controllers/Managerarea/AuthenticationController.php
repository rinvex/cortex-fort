<?php

declare(strict_types=1);

namespace Cortex\Auth\Http\Controllers\Managerarea;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Cortex\Auth\Http\Requests\Managerarea\AuthenticationRequest;
use Cortex\Foundation\Http\Controllers\UnauthenticatedController;

class AuthenticationController extends UnauthenticatedController
{
    /**
     * {@inheritdoc}
     */
    protected $middlewareWhitelist = [
        'logout',
    ];

    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function form()
    {
        return view('cortex/auth::managerarea.pages.authentication');
    }

    /**
     * Process to the login form.
     *
     * @param \Cortex\Auth\Http\Requests\Managerarea\AuthenticationRequest $request
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function login(AuthenticationRequest $request)
    {
        // Prepare variables
        $loginField = $request->input('loginfield');
        $credentials = [
            'is_active' => true,
            get_login_field($loginField) => $loginField,
            'password' => $request->input('password'),
        ];

        if (auth()->guard(app('request.guard'))->attempt($credentials, $request->filled('remember'))) {
            return $this->sendLoginResponse($request);
        }

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Logout currently logged in user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $this->processLogout($request);

        return intend([
            'url' => route('managerarea.home'),
            'with' => ['warning' => trans('cortex/auth::messages.auth.logout')],
        ]);
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $twofactor = app('request.user')->getTwoFactor();
        $totpStatus = $twofactor['totp']['enabled'] ?? false;
        $phoneStatus = $twofactor['phone']['enabled'] ?? false;

        $request->session()->regenerate();

        // Enforce TwoFactor authentication
        if ($totpStatus || $phoneStatus) {
            $this->processLogout($request);

            $request->session()->put('cortex.auth.twofactor', ['user_id' => app('request.user')->getKey(), 'remember' => $request->filled('remember'), 'totp' => $totpStatus, 'phone' => $phoneStatus]);

            $route = $totpStatus
                ? route('managerarea.cortex.auth.account.verification.phone.verify')
                : route('managerarea.cortex.auth.account.verification.phone.request');

            return intend([
                'url' => $route,
                'with' => ['warning' => trans('cortex/auth::messages.verification.twofactor.totp.required')],
            ]);
        }

        return intend([
            'intended' => route('managerarea.home'),
            'with' => ['success' => trans('cortex/auth::messages.auth.login')],
        ]);
    }

    /**
     * Get the failed login response instance.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws ValidationException
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'loginfield' => [trans('cortex/auth::messages.auth.failed')],
        ]);
    }

    /**
     * Process logout.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    protected function processLogout(Request $request): void
    {
        auth()->guard(app('request.guard'))->logoutCurrentDevice();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
    }
}
