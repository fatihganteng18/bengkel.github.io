<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(Request $request): View
    {
        $this->rememberIntendedUrl($request);

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);
        $credentials['email'] = Str::lower($credentials['email']);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau kata sandi tidak sesuai.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        if ($request->user()->role === User::ROLE_ADMIN) {
            return redirect()->route('admin.dashboard');
        }

        if ($request->user()->role === User::ROLE_CASHIER) {
            return redirect()->route('cashir.dashboard');
        }

        return redirect()->intended(route('home'));
    }

    public function showAdminLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()?->role === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('home');
        }

        return view('auth.admin-login');
    }

    public function loginAdmin(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()?->role === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('home');
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);
        $credentials['email'] = Str::lower($credentials['email']);

        if (! Auth::attempt($credentials + ['role' => 'admin'], $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau kata sandi admin tidak sesuai.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function showRegister(Request $request): View
    {
        $this->rememberIntendedUrl($request);

        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => Str::lower((string) $request->input('email')),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::notIn([
                    'admin@gmail.com',
                    'adillahalfaris@gmail.com',
                    Str::lower((string) config('planetban.admin.email')),
                ]),
                'unique:users,email',
            ],
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+() -]{8,20}$/', 'unique:users,phone'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('home'))
            ->with('success', 'Akun berhasil dibuat. Selamat datang di Planet Ban!');
    }

    public function dashboard(): View|RedirectResponse
    {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if (Auth::user()->role === User::ROLE_CASHIER) {
            return redirect()->route('cashir.dashboard');
        }

        $bookings = Booking::with(['store', 'product', 'serviceQueue'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('account.dashboard', compact('bookings'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function showLinkRequestForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        return back()
            ->withErrors(['email' => __($status)])
            ->onlyInput('email');
    }

    public function showResetForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()],
        ]);

        $status = Password::reset(
            $validated,
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()
                ->withErrors(['email' => __($status)])
                ->onlyInput('email');
        }

        return redirect()->route('login')->with('status', __($status));
    }

    private function rememberIntendedUrl(Request $request): void
    {
        $intended = $request->query('intended');

        if (! is_string($intended)
            || ! preg_match('#^/(?!/)[^\\\\]*$#D', $intended)
            || parse_url($intended, PHP_URL_HOST) !== null
            || parse_url($intended, PHP_URL_SCHEME) !== null) {
            return;
        }

        $request->session()->put('url.intended', url($intended));
    }
}
