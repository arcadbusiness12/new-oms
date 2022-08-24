<?php

namespace Illuminate\Foundation\Auth;

use App\Models\Oms\OmsAccountSummaryModel;
use App\Models\Oms\OmsAccountTransactionModel;
use App\Models\Oms\OmsUserGroupModel;
use App\Models\Oms\OmsUserModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Session;

trait AuthenticatesUsers
{
    use RedirectsUsers, ThrottlesLogins;

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }
            // dd($request->session());
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request), $request->boolean('remember')
        );
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            
            return $response;
        }
        if ($this->guard()->user()){
            if(($this->guard()->user()->role == 2) || ($this->guard()->user()->role == 3) || $this->guard()->user()->role == 20){
                $permissions = OmsUserGroupModel::select(OmsUserGroupModel::FIELD_ACCESS)->where(OmsUserGroupModel::FIELD_ID, $this->guard()->user()->user_group_id)->first();
            }
            $account = OmsAccountSummaryModel::where(OmsAccountSummaryModel::FIELD_USER_ID,$this->guard()->user()->user_id)->exists();
            if(!$account){
                if($this->guard()->user()->role == 2){
                    $OmsAccountSummaryModel = new OmsAccountSummaryModel();
                    $OmsAccountSummaryModel->{OmsAccountSummaryModel::FIELD_USER_ID} = $this->guard()->user()->user_id;
                    $OmsAccountSummaryModel->{OmsAccountSummaryModel::FIELD_BALANCE} = 0;
                    $OmsAccountSummaryModel->save();

                    $OmsAccountTransactionModel = new OmsAccountTransactionModel();
                    $OmsAccountTransactionModel->{OmsAccountTransactionModel::FIELD_ACCOUNT_ID} = $OmsAccountSummaryModel->account_id;
                    $OmsAccountTransactionModel->{OmsAccountTransactionModel::FIELD_ORDER_ID} = '';
                    $OmsAccountTransactionModel->{OmsAccountTransactionModel::FIELD_DESCRIPTION} = 'Opening Balance';
                    $OmsAccountTransactionModel->{OmsAccountTransactionModel::FIELD_CREDIT} = $OmsAccountSummaryModel->balance;
                    $OmsAccountTransactionModel->{OmsAccountTransactionModel::FIELD_DEBIT} = 0;
                    $OmsAccountTransactionModel->{OmsAccountTransactionModel::FIELD_BALANCE} = $OmsAccountSummaryModel->balance;
                    $OmsAccountTransactionModel->save();
                }
            }
            if($this->guard()->user()->role == 2){
                $session['role'] = "SUPPLIER";
            }else if($this->guard()->user()->role == 3){
                $session['role'] = "STAFF";
            }else if($this->guard()->user()->role == 20) {
                $session['role'] = "RESELLER";
            }else{
                $session['role'] = "ADMIN";
            }
            $user = OmsUserModel::with('detail')->where(OmsUserModel::FIELD_USER_ID, $this->guard()->user()->user_id)->first();
            $user_logo = ($user->detail && $user->detail->brand_logo) ? Storage::url($user->detail->brand_logo) : 'https://www.gravatar.com/avatar/'.md5(session('email'));
            Session::put('user_logo', $user_logo);

            $session['duties'] = json_encode($user->activities);
            if(($this->guard()->user()->role == 2) || ($this->guard()->user()->role == 3)){
                if($permissions){
                    $per = $permissions->access;
                }else{
                    $per = '[]';
                }
                $user_perm = array_flip(json_decode($user->user_access));
                $group_per = array_flip(json_decode($per));
                $access = $group_per;
                if( count($user_perm) > 0 ){
                  $access = $user_perm;
                }
                // $access    = array_merge($user_perm,$group_per);
                $session['access'] = json_encode($access);
            }else{
                $session['access'] = json_encode(array_flip(array()));
            }
            \Session::put(array_merge($user->toArray(), $session));
        }
        return $request->wantsJson()
                    ? new JsonResponse([], 204)
                    : redirect()->intended($this->redirectPath());
     }
    
    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        //
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/login');
    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        //
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
