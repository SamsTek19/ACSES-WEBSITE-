@php($title = 'Sign In')

<x-layouts.auth :title="$title">
    <div class="w-full">
        <div class="mb-8 text-center">
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">Login to your account</h2>
            <p class="mt-2 text-sm text-slate-500">Enter your credentials to continue</p>
        </div>

        @if(session('status'))
            @if(session('pending_registration'))
            <div class="mb-6 rounded-xl border border-amber-300/50 bg-amber-50 px-4 py-4 text-sm text-amber-800">
                <div class="flex items-start gap-3">
                    <i data-lucide="clock" class="text-xl text-amber-500"></i>
                    <div>
                        <p class="font-semibold">Registration Submitted for Review</p>
                        <p class="mt-1 text-amber-700">{{ session('status') }}</p>
                    </div>
                </div>
            </div>
            @else
            <div class="mb-6 rounded-xl border border-[#0b3019]/30 bg-[#0b3019]/10 px-4 py-3 text-sm text-[#0b3019]">
                {{ session('status') }}
            </div>
            @endif
        @endif

        <form method="POST" action="{{ route('auth.login.submit') }}" class="space-y-5" data-auth-form>
            @csrf
            <div class="space-y-1.5">
                <label for="login_id" class="block text-sm font-medium text-slate-700">Email, Username, or Index Number</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <i data-lucide="user" class="text-base" aria-hidden="true"></i>
                    </span>
                    <input id="login_id" name="login_id" type="text" value="{{ old('login_id', old('email')) }}" required autofocus autocomplete="username" aria-describedby="login_id-error" @error('login_id') aria-invalid="true" @enderror @error('email') aria-invalid="true" @enderror class="block w-full rounded-lg border border-slate-200 bg-white py-2 pl-10 pr-3 text-sm text-slate-900 shadow-sm transition hover:border-slate-300 focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019] @error('login_id') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror @error('email') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" placeholder="Username, email, or index number" />
                </div>
                @error('login_id')
                    <p id="login_id-error" class="text-xs text-red-500" role="alert">{{ $message }}</p>
                @enderror
                @error('email')
                    <p id="email-error" class="text-xs text-red-500" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-[#0b3019] transition hover:text-[#094018] hover:underline">Forgot password?</a>
                </div>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <i data-lucide="lock" class="text-base" aria-hidden="true"></i>
                    </span>
                    <input id="password" name="password" type="password" required autocomplete="current-password" aria-describedby="password-error" @error('password') aria-invalid="true" @enderror class="block w-full rounded-lg border border-slate-200 bg-white py-2 pl-10 pr-10 text-sm text-slate-900 shadow-sm transition hover:border-slate-300 focus:border-[#0b3019] focus:outline-none focus:ring-1 focus:ring-[#0b3019] @error('password') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" placeholder="Enter your password" />
                    <button type="button" data-password-toggle="#password" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 transition hover:text-slate-600" aria-label="Toggle password visibility">
                        <i data-eye data-lucide="eye" class="text-base" aria-hidden="true"></i>
                        <i data-eye-off data-lucide="eye-off" class="hidden text-base" aria-hidden="true"></i>
                    </button>
                </div>
                @error('password')
                    <p id="password-error" class="text-xs text-red-500" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <label for="remember" class="flex items-center space-x-2 text-sm text-slate-500 cursor-pointer">
                    <input id="remember" name="remember" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300 text-[#0b3019] focus:ring-[#0b3019] cursor-pointer">
                    <span>Remember me</span>
                </label>
                <span aria-hidden="true"></span>
            </div>

            <button type="submit" class="flex w-full items-center justify-center space-x-2 rounded-lg bg-[#0b3019] px-4 py-2 text-sm font-medium text-white shadow-sm transition-all hover:bg-[#094018] active:scale-[0.98] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#0b3019]">
                <span>Login</span>
                <i data-lucide="arrow-right" class="text-sm" aria-hidden="true"></i>
            </button>
        </form>

        <div class="mt-8 space-y-3 text-center text-sm text-slate-500">
            <p>
                Don’t have an account?
                <a href="{{ route('auth.register') }}" class="font-medium text-[#0b3019] hover:underline hover:text-[#094018]">Sign up</a>
            </p>
            <p>
                Need help?
                <a href="mailto:acsesrepos@gmail.com" class="font-medium text-[#0b3019] hover:underline hover:text-[#094018]">Contact support</a>
            </p>
            <p>
                <a href="{{ route('admin.login') }}" class="font-medium text-slate-500 hover:text-[#0b3019] hover:underline">Admin sign in</a>
            </p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('[data-auth-form]');

            function clearErrors() {
                form.querySelectorAll('[data-inline-error]').forEach(el => el.remove());
                form.querySelectorAll('[aria-invalid]').forEach(el => el.removeAttribute('aria-invalid'));
                form.querySelectorAll('.border-red-500').forEach(el => {
                    el.classList.remove('border-red-500');
                });
            }

            function showFieldError(fieldName, message) {
                const input = form.querySelector(`[name="${fieldName}"]`);
                if (!input) return;
                input.setAttribute('aria-invalid', 'true');
                input.classList.add('border-red-500');
                const wrapper = input.closest('.space-y-1\\.5') || input.parentElement.parentElement;
                const errorEl = document.createElement('p');
                errorEl.setAttribute('data-inline-error', '');
                errorEl.className = 'text-xs text-red-500 mt-1';
                errorEl.setAttribute('role', 'alert');
                errorEl.textContent = message;
                wrapper.appendChild(errorEl);
            }

            function setLoading(isLoading) {
                const btn = form.querySelector('button[type="submit"]');
                if (!btn) return;
                btn.disabled = isLoading;
                if (isLoading) {
                    btn.dataset.originalText = btn.innerHTML;
                    btn.innerHTML = '<span class="flex items-center justify-center gap-2"><span class="h-4 w-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span> Signing in…</span>';
                    btn.classList.add('opacity-75', 'cursor-not-allowed');
                } else {
                    btn.innerHTML = btn.dataset.originalText || '<span>Login</span>';
                    btn.classList.remove('opacity-75', 'cursor-not-allowed');
                }
            }

            if (form) {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    clearErrors();
                    setLoading(true);

                    const formData = new FormData(form);

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            credentials: 'same-origin',
                        });

                        // Rate limited
                        if (response.status === 429) {
                            setLoading(false);
                            let seconds = 60;
                            let message = 'Too many login attempts. Please wait before trying again.';
                            try {
                                const data = await response.json();
                                seconds = data.retry_after || 60;
                                message = data.message || message;
                            } catch (_) {
                                const retryAfter = response.headers.get('Retry-After');
                                if (retryAfter) seconds = parseInt(retryAfter, 10) || 60;
                            }
                            if (window.RateLimitHandler) {
                                window.RateLimitHandler.show(seconds, message, 'Too Many Login Attempts');
                            } else {
                                alert(message);
                            }
                            return;
                        }

                        // Validation errors (422) — show inline without reloading
                        if (response.status === 422) {
                            setLoading(false);
                            const data = await response.json();
                            const errors = data.errors || {};
                            for (const [field, messages] of Object.entries(errors)) {
                                const msg = Array.isArray(messages) ? messages[0] : messages;
                                showFieldError(field, msg);
                            }
                            // Shake the submit button for feedback
                            const btn = form.querySelector('button[type="submit"]');
                            if (btn) {
                                btn.style.animation = 'none';
                                btn.offsetHeight;
                                btn.style.animation = 'shake 0.4s ease';
                            }
                            return;
                        }

                        // Successful redirect
                        if (response.redirected) {
                            window.location.href = response.url;
                            return;
                        }

                        if (response.ok) {
                            try {
                                const data = await response.json();
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                    return;
                                }
                            } catch (_) {}
                            window.location.reload();
                        } else {
                            setLoading(false);
                            showFieldError('login_id', 'Something went wrong. Please try again.');
                        }
                    } catch (error) {
                        console.error('Login error:', error);
                        setLoading(false);
                        showFieldError('login_id', 'A network error occurred. Please check your connection and try again.');
                    }
                });
            }
        });
    </script>
    <style>
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-6px); }
            40% { transform: translateX(6px); }
            60% { transform: translateX(-4px); }
            80% { transform: translateX(4px); }
        }
    </style>
</x-layouts.auth>

