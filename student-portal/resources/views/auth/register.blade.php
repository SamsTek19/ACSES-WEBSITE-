@php($title = 'Create Account')

<x-layouts.auth :title="$title" card-width="max-w-xl">
    <div class="mx-auto w-full">
        <!-- Wizard Step Header Indicator -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="step-indicator flex flex-col items-center flex-1 active-step" data-step-indicator="1">
                    <div class="step-num flex h-9 w-9 items-center justify-center rounded-full bg-[#0b3019] text-xs font-bold text-white shadow-sm ring-4 ring-[#0b3019]/10 transition-all">1</div>
                    <span class="step-label mt-2 text-xs font-semibold text-[#0b3019]">Personal</span>
                </div>
                <div class="h-[2px] flex-1 bg-slate-200 -mt-5 transition-all" data-step-line="1"></div>
                <div class="step-indicator flex flex-col items-center flex-1 opacity-50" data-step-indicator="2">
                    <div class="step-num flex h-9 w-9 items-center justify-center rounded-full bg-slate-200 text-xs font-bold text-slate-600 transition-all">2</div>
                    <span class="step-label mt-2 text-xs font-medium text-slate-500">Academic</span>
                </div>
                <div class="h-[2px] flex-1 bg-slate-200 -mt-5 transition-all" data-step-line="2"></div>
                <div class="step-indicator flex flex-col items-center flex-1 opacity-50" data-step-indicator="3">
                    <div class="step-num flex h-9 w-9 items-center justify-center rounded-full bg-slate-200 text-xs font-bold text-slate-600 transition-all">3</div>
                    <span class="step-label mt-2 text-xs font-medium text-slate-500">Security</span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('auth.register.submit') }}" class="space-y-6" data-auth-form id="wizard-form">
            @csrf

            <!-- Security Honeypot -->
            <div class="hidden" aria-hidden="true">
                <input type="text" name="website_origin" tabindex="-1" autocomplete="off">
            </div>

            @if(session('error'))
                <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                    <div class="flex items-center gap-2">
                        <i data-lucide="triangle-alert" class="text-lg"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- STEP 1: Academic & Personal Overview -->
            <div data-step="1" class="space-y-5">
                <div class="mb-4">
                    <h2 class="text-xl font-bold text-slate-900">Personal & Academic Info</h2>
                    <p class="text-xs text-slate-500">Enter your name, username, program, and current level</p>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <div class="space-y-1.5">
                        <label for="first_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">First name</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <i data-lucide="user" class="text-base" aria-hidden="true"></i>
                            </span>
                            <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" required autocomplete="given-name" class="block w-full rounded-lg border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-900 shadow-sm transition focus:border-[#0b3019] focus:outline-none hover:border-slate-300 focus:ring-1 focus:ring-[#0b3019]" placeholder="Kofi" />
                        </div>
                        @error('first_name')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="other_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Other name <span class="normal-case font-normal text-slate-400">(optional)</span></label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <i data-lucide="user" class="text-base" aria-hidden="true"></i>
                            </span>
                            <input id="other_name" name="other_name" type="text" value="{{ old('other_name') }}" autocomplete="additional-name" class="block w-full rounded-lg border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-900 shadow-sm transition focus:border-[#0b3019] focus:outline-none hover:border-slate-300 focus:ring-1 focus:ring-[#0b3019]" placeholder="Kwame" />
                        </div>
                        @error('other_name')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="last_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Last name</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <i data-lucide="user" class="text-base" aria-hidden="true"></i>
                            </span>
                            <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" required autocomplete="family-name" class="block w-full rounded-lg border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-900 shadow-sm transition focus:border-[#0b3019] focus:outline-none hover:border-slate-300 focus:ring-1 focus:ring-[#0b3019]" placeholder="Mensah" />
                        </div>
                        @error('last_name')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="username" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Username</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i data-lucide="user-check" class="text-base" aria-hidden="true"></i>
                        </span>
                        <input id="username" name="username" type="text" value="{{ old('username') }}" required autocomplete="username" class="block w-full rounded-lg border border-slate-200 bg-white py-2.5 pl-10 pr-12 text-sm text-slate-900 shadow-sm transition focus:border-[#0b3019] focus:outline-none hover:border-slate-300 focus:ring-1 focus:ring-[#0b3019]" placeholder="kofimensah" />
                        <div id="username-status-icon" class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <i id="username-checking" data-lucide="loader-circle" class="hidden animate-spin text-lg text-slate-400"></i>
                            <i id="username-available" data-lucide="check" class="hidden text-lg text-emerald-500"></i>
                            <i id="username-taken" data-lucide="x" class="hidden text-lg text-rose-500"></i>
                        </div>
                    </div>
                    <div id="username-feedback" class="hidden rounded-lg px-3 py-2 text-xs"></div>
                    @error('username')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-1.5">
                        <label for="class" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Program</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <i data-lucide="book-open" class="text-base" aria-hidden="true"></i>
                            </span>
                            <select id="class" name="class" required class="block w-full appearance-none rounded-lg border border-slate-200 bg-white py-2.5 pl-10 pr-10 text-sm text-slate-900 shadow-sm transition focus:border-[#0b3019] focus:outline-none hover:border-slate-300 focus:ring-1 focus:ring-[#0b3019]">
                                <option value="" disabled {{ old('class') ? '' : 'selected' }}>Select program</option>
                                <optgroup label="Computer Science Department" class="font-bold text-slate-900 bg-slate-50">
                                    <option value="Computer Science" {{ old('class') === 'Computer Science' ? 'selected' : '' }} class="font-normal text-slate-800 bg-white">Computer Science</option>
                                </optgroup>
                                <optgroup label="Cyber Security & Information System Department" class="font-bold text-slate-900 bg-slate-50">
                                    <option value="Cyber Security" {{ old('class') === 'Cyber Security' ? 'selected' : '' }} class="font-normal text-slate-800 bg-white">Cyber Security</option>
                                    <option value="Information System" {{ old('class') === 'Information System' ? 'selected' : '' }} class="font-normal text-slate-800 bg-white">Information System</option>
                                    <option value="Robotics" {{ old('class') === 'Robotics' ? 'selected' : '' }} class="font-normal text-slate-800 bg-white">Robotics</option>
                                </optgroup>
                            </select>
                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                                <i data-lucide="chevron-down" class="text-base"></i>
                            </span>
                        </div>
                        @error('class')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="year" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Level</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <i data-lucide="medal" class="text-base" aria-hidden="true"></i>
                            </span>
                            <select id="year" name="year" required class="block w-full appearance-none rounded-lg border border-slate-200 bg-white py-2.5 pl-10 pr-10 text-sm text-slate-900 shadow-sm transition focus:border-[#0b3019] focus:outline-none hover:border-slate-300 focus:ring-1 focus:ring-[#0b3019]">
                                <option value="" disabled {{ old('year') ? '' : 'selected' }}>Select level</option>
                                @foreach (['1' => 'Level 100', '2' => 'Level 200', '3' => 'Level 300', '4' => 'Level 400'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('year') === (string)$val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                                <i data-lucide="chevron-down" class="text-base"></i>
                            </span>
                        </div>
                        @error('year')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- STEP 2: Student Identification & Contact -->
            <div data-step="2" class="hidden space-y-5">
                <div class="mb-4">
                    <h2 class="text-xl font-bold text-slate-900">Student Verification</h2>
                    <p class="text-xs text-slate-500">Provide your official student identifier and contact info</p>
                </div>

                <!-- Dynamic Student ID / Reference Field -->
                <div class="space-y-1.5">
                    <label id="index_number_label" for="index_number" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                        Index Number <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i data-lucide="hash" class="text-base" aria-hidden="true"></i>
                        </span>
                        <input id="index_number" name="index_number" type="text" value="{{ old('index_number') }}" required maxlength="30" class="block w-full rounded-lg border border-slate-200 bg-white py-2.5 pl-10 pr-12 text-sm uppercase text-slate-900 shadow-sm transition focus:border-[#0b3019] focus:outline-none hover:border-slate-300 focus:ring-1 focus:ring-[#0b3019]" placeholder="e.g. FOE.55.012.088.24" />
                        <div id="index_number-status-icon" class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <i id="index_number-checking" data-lucide="loader-circle" class="hidden animate-spin text-lg text-slate-400"></i>
                            <i id="index_number-available" data-lucide="check" class="hidden text-lg text-emerald-500"></i>
                            <i id="index_number-taken" data-lucide="x" class="hidden text-lg text-rose-500"></i>
                        </div>
                    </div>
                    <!-- Helpful Dynamic Context Note for Students -->
                    <div id="id_field_hint" class="flex items-center gap-1.5 text-xs text-slate-500 pt-0.5">
                        <i data-lucide="info" class="h-3.5 w-3.5 text-emerald-700 shrink-0"></i>
                        <span id="id_hint_text">Enter your official Index Number assigned by the university.</span>
                    </div>
                    <div id="index_number-feedback" class="hidden rounded-lg px-3 py-2 text-xs"></div>
                    @error('index_number')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Email address</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i data-lucide="mail" class="text-base" aria-hidden="true"></i>
                        </span>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" class="block w-full rounded-lg border border-slate-200 bg-white py-2.5 pl-10 pr-12 text-sm text-slate-900 shadow-sm transition focus:border-[#0b3019] focus:outline-none hover:border-slate-300 focus:ring-1 focus:ring-[#0b3019]" placeholder="cy-yourname@st.umat.edu.gh" />
                        <div id="email-status-icon" class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <i id="email-checking" data-lucide="loader-circle" class="hidden animate-spin text-lg text-slate-400"></i>
                            <i id="email-available" data-lucide="check" class="hidden text-lg text-emerald-500"></i>
                            <i id="email-taken" data-lucide="x" class="hidden text-lg text-rose-500"></i>
                        </div>
                    </div>
                    <div id="email-feedback" class="hidden rounded-lg px-3 py-2 text-xs"></div>
                    @error('email')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror

                    <div id="email-verification-notice" class="hidden rounded-xl border p-3 text-xs">
                        <div id="school-email-notice" class="hidden">
                            <div class="flex items-start gap-2 text-emerald-700">
                                <i data-lucide="shield-check" class="text-base mt-0.5"></i>
                                <div>
                                    <p class="font-semibold">School Email Detected</p>
                                    <p class="text-emerald-600">You'll receive a quick email verification code to activate your account instantly.</p>
                                </div>
                            </div>
                        </div>
                        <div id="non-school-email-notice" class="hidden">
                            <div class="flex items-start gap-2 text-amber-700">
                                <i data-lucide="user-search" class="text-base mt-0.5"></i>
                                <div>
                                    <p class="font-semibold">Manual Verification Required</p>
                                    <p class="text-amber-600">Non-school emails require admin approval. You'll receive an email once reviewed.</p>
                                </div>
                            </div>
                        </div>
                        <div id="email-mismatch-notice" class="hidden">
                            <div class="flex items-start gap-2 text-rose-700">
                                <i data-lucide="alert-circle" class="text-base mt-0.5"></i>
                                <div>
                                    <p class="font-semibold">Program Mismatch</p>
                                    <p class="text-rose-600" id="mismatch-message"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="phone_number" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Phone number</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i data-lucide="phone" class="text-base" aria-hidden="true"></i>
                        </span>
                        <input id="phone_number" name="phone_number" type="tel" value="{{ old('phone_number') }}" inputmode="numeric" pattern="\d{9,11}" maxlength="11" data-numeric-only class="block w-full rounded-lg border border-slate-200 bg-white py-2.5 pl-10 pr-12 text-sm text-slate-900 shadow-sm transition focus:border-[#0b3019] focus:outline-none hover:border-slate-300 focus:ring-1 focus:ring-[#0b3019]" placeholder="0541234567" />
                        <div id="phone_number-status-icon" class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <i id="phone_number-checking" data-lucide="loader-circle" class="hidden animate-spin text-lg text-slate-400"></i>
                            <i id="phone_number-available" data-lucide="check" class="hidden text-lg text-emerald-500"></i>
                            <i id="phone_number-taken" data-lucide="x" class="hidden text-lg text-rose-500"></i>
                        </div>
                    </div>
                    <div id="phone_number-feedback" class="hidden rounded-lg px-3 py-2 text-xs"></div>
                    @error('phone_number')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- STEP 3: Security & Confirmation -->
            <div data-step="3" class="hidden space-y-5">
                <div class="mb-4">
                    <h2 class="text-xl font-bold text-slate-900">Security Credentials</h2>
                    <p class="text-xs text-slate-500">Create a secure password and accept the terms</p>
                </div>

                <div class="space-y-1.5">
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Password</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i data-lucide="lock" class="text-base" aria-hidden="true"></i>
                        </span>
                        <input id="password" name="password" type="password" required autocomplete="new-password" class="block w-full rounded-lg border border-slate-200 bg-white py-2.5 pl-10 pr-12 text-sm text-slate-900 shadow-sm transition focus:border-[#0b3019] focus:outline-none hover:border-slate-300 focus:ring-1 focus:ring-[#0b3019]" placeholder="Create a secure password" />
                        <button type="button" data-password-toggle="#password" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 transition hover:text-slate-600">
                            <i data-eye data-lucide="eye" class="text-base"></i>
                            <i data-eye-off data-lucide="eye-off" class="hidden text-base"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="hidden space-y-3 rounded-xl border border-slate-200 bg-slate-50/70 p-4" data-password-strength data-password-input="#password">
                    <div class="flex items-center justify-between text-xs font-medium text-slate-600">
                        <span>Password strength</span>
                        <span data-password-strength-label class="text-slate-500 font-semibold">Weak</span>
                    </div>
                    <div class="h-2 w-full rounded-full bg-slate-200 overflow-hidden">
                        <div data-password-strength-bar class="h-2 w-1/12 rounded-full bg-red-500 transition-all duration-300"></div>
                    </div>
                    <div class="pt-1 space-y-1.5 text-xs text-slate-600">
                        <p class="font-semibold text-slate-700 mb-1 text-[11px] uppercase tracking-wider">Password Requirements:</p>
                        <div class="flex items-center gap-2" data-password-rule="length">
                            <span data-pass-icon class="hidden text-emerald-600 font-bold">✓</span>
                            <span data-fail-icon class="text-slate-400 font-bold">•</span>
                            <span>At least 8 characters long</span>
                        </div>
                        <div class="flex items-center gap-2" data-password-rule="mixed">
                            <span data-pass-icon class="hidden text-emerald-600 font-bold">✓</span>
                            <span data-fail-icon class="text-slate-400 font-bold">•</span>
                            <span>Contains uppercase & lowercase letters</span>
                        </div>
                        <div class="flex items-center gap-2" data-password-rule="number">
                            <span data-pass-icon class="hidden text-emerald-600 font-bold">✓</span>
                            <span data-fail-icon class="text-slate-400 font-bold">•</span>
                            <span>Contains at least one number (0-9)</span>
                        </div>
                        <div class="flex items-center gap-2" data-password-rule="symbol">
                            <span data-pass-icon class="hidden text-emerald-600 font-bold">✓</span>
                            <span data-fail-icon class="text-slate-400 font-bold">•</span>
                            <span>Contains a special character (@, #, $, !, etc.)</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Confirm password</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i data-lucide="lock" class="text-base" aria-hidden="true"></i>
                        </span>
                        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="block w-full rounded-lg border border-slate-200 bg-white py-2.5 pl-10 pr-12 text-sm text-slate-900 shadow-sm transition focus:border-[#0b3019] focus:outline-none hover:border-slate-300 focus:ring-1 focus:ring-[#0b3019]" placeholder="Confirm your password" />
                        <button type="button" data-password-toggle="#password_confirmation" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 transition hover:text-slate-600">
                            <i data-eye data-lucide="eye" class="text-base"></i>
                            <i data-eye-off data-lucide="eye-off" class="hidden text-base"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-start space-x-3 rounded-xl border border-slate-200 bg-slate-50/70 p-4">
                    <input id="accept_terms" name="accept_terms" type="checkbox" value="1" required class="mt-1 h-4 w-4 rounded border-slate-300 text-[#0b3019] focus:ring-[#0b3019]" {{ old('accept_terms') ? 'checked' : '' }}>
                    <label for="accept_terms" class="text-sm text-slate-500">
                        I agree to the
                        <a href="{{ route('legal.terms') }}" class="font-semibold text-[#0b3019] hover:underline">Terms</a>
                        and
                        <a href="{{ route('legal.privacy') }}" class="font-semibold text-[#0b3019] hover:underline">Privacy Policy</a>
                        of the ACSES Portal.
                    </label>
                </div>
            </div>

            <!-- Wizard Navigation Buttons -->
            <div class="pt-4 flex items-center justify-between border-t border-slate-100">
                <button type="button" id="prev-step-btn" class="hidden flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    <i data-lucide="arrow-left" class="text-sm"></i>
                    <span>Back</span>
                </button>
                <div class="ml-auto flex items-center gap-3">
                    <button type="button" id="next-step-btn" class="flex items-center gap-2 rounded-lg bg-[#0b3019] px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-[#094018] active:scale-[0.98]">
                        <span>Continue</span>
                        <i data-lucide="arrow-right" class="text-sm"></i>
                    </button>
                    <button id="submit-btn" type="submit" class="hidden flex items-center justify-center space-x-2 rounded-lg bg-[#0b3019] px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-[#094018] active:scale-[0.98]">
                        <span>Create account</span>
                        <i data-lucide="check" class="text-sm"></i>
                    </button>
                </div>
            </div>
        </form>

        <div class="mt-8 text-center text-sm text-slate-500">
            <p>
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold text-[#0b3019] hover:underline">Sign in</a>
            </p>
        </div>
    </div>

    {{-- Email validation script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const classSelect = document.getElementById('class');
            const noticeContainer = document.getElementById('email-verification-notice');
            const schoolEmailNotice = document.getElementById('school-email-notice');
            const nonSchoolEmailNotice = document.getElementById('non-school-email-notice');
            const mismatchNotice = document.getElementById('email-mismatch-notice');
            const mismatchMessage = document.getElementById('mismatch-message');

            const SCHOOL_DOMAIN = 'st.umat.edu.gh';
            const CLASS_PREFIXES = {
                'cy': 'Cyber Security',
                'is': 'Information System',
                'ce': 'Computer Science',
                'ro': 'Robotics'
            };

            function validateEmail() {
                const email = emailInput.value.trim().toLowerCase();
                const selectedClass = classSelect.value;

                // Hide all notices first
                noticeContainer.classList.add('hidden');
                schoolEmailNotice.classList.add('hidden');
                nonSchoolEmailNotice.classList.add('hidden');
                mismatchNotice.classList.add('hidden');

                if (!email || !email.includes('@')) {
                    return;
                }

                const domain = email.split('@')[1] || '';
                const isSchoolEmail = domain === SCHOOL_DOMAIN;

                noticeContainer.classList.remove('hidden');

                if (isSchoolEmail) {
                    // Check for prefix match
                    const localPart = email.split('@')[0] || '';
                    const match = localPart.match(/^([a-z]{2})-/i);
                    const prefix = match ? match[1].toLowerCase() : null;
                    const expectedClass = prefix ? CLASS_PREFIXES[prefix] : null;

                    if (selectedClass && expectedClass && expectedClass !== selectedClass) {
                        // Mismatch
                        mismatchNotice.classList.remove('hidden');
                        noticeContainer.classList.remove('border-emerald-200', 'bg-emerald-50', 'border-amber-200', 'bg-amber-50');
                        noticeContainer.classList.add('border-rose-200', 'bg-rose-50');
                        mismatchMessage.textContent = `Your school email prefix "${prefix.toUpperCase()}" indicates ${expectedClass}, but you selected ${selectedClass}. Please select the correct program.`;
                    } else if (!expectedClass) {
                        // Unknown prefix
                        mismatchNotice.classList.remove('hidden');
                        noticeContainer.classList.remove('border-emerald-200', 'bg-emerald-50', 'border-amber-200', 'bg-amber-50');
                        noticeContainer.classList.add('border-rose-200', 'bg-rose-50');
                        mismatchMessage.textContent = 'Your school email prefix is not recognized. Expected prefixes: CY (Cyber Security), IS (Information System), CE (Computer Science), RO (Robotics).';
                    } else {
                        // Valid school email
                        schoolEmailNotice.classList.remove('hidden');
                        noticeContainer.classList.remove('border-rose-200', 'bg-rose-50', 'border-amber-200', 'bg-amber-50');
                        noticeContainer.classList.add('border-emerald-200', 'bg-emerald-50');
                    }
                } else {
                    // Non-school email
                    nonSchoolEmailNotice.classList.remove('hidden');
                    noticeContainer.classList.remove('border-emerald-200', 'bg-emerald-50', 'border-rose-200', 'bg-rose-50');
                    noticeContainer.classList.add('border-amber-200', 'bg-amber-50');
                }
            }

            emailInput.addEventListener('input', validateEmail);
            emailInput.addEventListener('change', validateEmail);
            classSelect.addEventListener('change', validateEmail);

            // Run on page load if there's a value
            if (emailInput.value) {
                validateEmail();
            }
        });
    </script>

    {{-- Generic Field Validation Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            class ValidationManager {
                constructor() {
                    this.form = document.querySelector('[data-auth-form]');
                    this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                    this.validationState = {
                        username: false,
                        email: false,
                        index_number: false,
                        phone_number: false
                    };
                    
                    this.fields = {
                        username: {
                            input: document.getElementById('username'),
                            regex: /^[a-zA-Z0-9_-]+$/ 
                        },
                        email: {
                            input: document.getElementById('email'),
                            // Regex is handled by API check mostly, but we can do basic format
                            regex: /^\S+@\S+\.\S+$/
                        },
                        index_number: {
                            input: document.getElementById('index_number'),
                            regex: /^[a-zA-Z0-9\.\-\/]{9,30}$/
                        },
                        phone_number: {
                            input: document.getElementById('phone_number'),
                            regex: /^\d{9,11}$/,
                            optional: true // phone is technically nullable in DB but standard users should provide it
                        }
                    };

                    this.init();
                }

                init() {
                    Object.keys(this.fields).forEach(field => {
                        const config = this.fields[field];
                        if (config.input) {
                            config.input.addEventListener('input', () => this.handleInput(field));
                            config.input.addEventListener('change', () => this.handleInput(field));
                            
                            // Initialize state (mark as false initially if empty and required)
                            if (!config.optional && !config.input.value.trim()) {
                                this.validationState[field] = false;
                            } else {
                                // If pre-filled, trigger check
                                if (config.input.value.trim()) {
                                    this.handleInput(field);
                                } else {
                                    // Empty and optional
                                    if(config.optional) this.validationState[field] = true;
                                }
                            }
                        }
                    });

                    // Intercept form submission
                    if (this.form) {
                        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
                    }
                }

                handleInput(field) {
                    const config = this.fields[field];
                    const value = config.input.value.trim();
                    const state = this.getElements(field);

                    // Clear existing timer
                    if (config.timer) clearTimeout(config.timer);

                    // Allow empty if optional
                    if (!value) {
                       this.updateUI(field, 'idle');
                       this.validationState[field] = !!config.optional;
                       return;
                    }

                    // Basic Regex Check
                    if (config.regex && !config.regex.test(value)) {
                         this.updateUI(field, 'error', `Invalid format.`);
                         this.validationState[field] = false;
                         return;
                    }

                    // Debounce API Check
                    this.updateUI(field, 'checking');
                    config.timer = setTimeout(() => this.checkAvailability(field, value), 500);
                }

                async checkAvailability(field, value) {
                    try {
                        const response = await fetch('{{ route("auth.register.check-availability") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ field, value })
                        });

                        // Handle Rate Limit
                        if (response.status === 429) {
                             const data = await response.json();
                             this.updateUI(field, 'error', 'Too many checks. Please wait.');
                             if (window.RateLimitHandler && data.type === 'rate_limit') {
                                 window.RateLimitHandler.show(data.retry_after, data.message, 'Slow Down');
                             }
                             return;
                        }

                        const data = await response.json();
                        
                        // Ensure value hasn't changed since request started
                        if (this.fields[field].input.value.trim() !== value) return;

                        if (data.available) {
                            this.updateUI(field, 'available', data.message);
                            this.validationState[field] = true;
                        } else {
                            this.updateUI(field, 'taken', data.message);
                            this.validationState[field] = false;
                        }

                    } catch (error) {
                        console.error('Validation error:', error);
                        this.updateUI(field, 'idle'); // Fail gracefully
                    }
                }

                getElements(field) {
                    return {
                        checking: document.getElementById(`${field}-checking`),
                        available: document.getElementById(`${field}-available`),
                        taken: document.getElementById(`${field}-taken`),
                        feedback: document.getElementById(`${field}-feedback`)
                    };
                }

                updateUI(field, status, message = '') {
                    const els = this.getElements(field);
                    const input = this.fields[field].input;

                    if (!els.checking) return; // Guard for missing elements

                    // Reset icons
                    els.checking.classList.add('hidden');
                    els.available.classList.add('hidden');
                    els.taken.classList.add('hidden');
                    
                    // Reset feedback
                    els.feedback.classList.remove('hidden', 'bg-emerald-50', 'text-emerald-700', 'bg-rose-50', 'text-rose-700');
                    els.feedback.textContent = message;

                    // Reset input border
                    input.classList.remove('border-emerald-400', 'border-rose-400');

                    switch (status) {
                        case 'checking':
                            els.checking.classList.remove('hidden');
                            els.feedback.classList.add('hidden');
                            break;
                        case 'available':
                            els.available.classList.remove('hidden');
                            els.feedback.classList.add('bg-emerald-50', 'text-emerald-700');
                            input.classList.add('border-emerald-400');
                            break;
                        case 'taken':
                        case 'error':
                            els.taken.classList.remove('hidden');
                            els.feedback.classList.add('bg-rose-50', 'text-rose-700');
                            input.classList.add('border-rose-400');
                            break;
                        case 'idle':
                            els.feedback.classList.add('hidden');
                            break;
                    }
                }

                async handleSubmit(e) {
                    // Check if there are any invalid fields (that are not empty/optional)
                    const invalidFields = Object.keys(this.fields).filter(field => {
                        const config = this.fields[field];
                        if (!config.input) return false;
                        
                        const val = config.input.value.trim();
                        // If it has a value and our state says it's invalid, block it
                        // Exception: Phone is optional, but if provided must be valid
                        if (val && this.validationState[field] === false) return true;
                        
                        // If it's required and empty (HTML5 required handles this mostly, but good double check)
                        if (!config.optional && !val) return false; // let native validation handle empty
                        
                        return false; 
                    });

                    if (invalidFields.length > 0) {
                        e.preventDefault();
                        const firstInvalid = invalidFields[0];
                        this.fields[firstInvalid].input.focus();
                        alert(`Please fix the ${firstInvalid.replace('_', ' ')} before submitting.`);
                        return false;
                    }
                    
                    // AJAX Submission for Rate Limit Handling
                    e.preventDefault();
                    
                    const overlay = document.getElementById('auth-loading-overlay');
                    if (overlay) {
                        overlay.classList.remove('hidden');
                        overlay.classList.add('flex');
                    }

                    const formData = new FormData(this.form);

                    try {
                        const response = await fetch(this.form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });

                        // Handle Rate Limit (429)
                        if (response.status === 429) {
                            if (overlay) {
                                overlay.classList.add('hidden');
                                overlay.classList.remove('flex');
                            }
                            
                            const data = await response.json();
                            const seconds = data.retry_after || 60;
                            
                            if (window.RateLimitHandler) {
                                window.RateLimitHandler.show(seconds, data.message || 'Too many attempts.', 'Rate Limit Reached');
                            } else {
                                alert(data.message || 'Too many attempts. Please wait.');
                            }
                            return;
                        }

                        // Handle Success or Validation Errors
                        if (response.redirected) {
                            window.location.href = response.url;
                        } else if (response.ok) {
                            const html = await response.text();
                             document.open();
                             document.write(html);
                             document.close();
                        } else {
                            // Fallback
                             const html = await response.text();
                             document.open();
                             document.write(html);
                             document.close();
                        }

                    } catch (error) {
                        console.error('Registration error:', error);
                        // Fallback: Submit normally if fetch fails
                        this.form.submit();
                    }
                }
            }

            new ValidationManager();
        });
    </script>

    {{-- Step Wizard Controller Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentStep = 1;
            const totalSteps = 3;
            const prevBtn = document.getElementById('prev-step-btn');
            const nextBtn = document.getElementById('next-step-btn');
            const submitBtn = document.getElementById('submit-btn');

            function updateWizardUI() {
                // Toggle step content visibility
                document.querySelectorAll('[data-step]').forEach(stepEl => {
                    const stepNum = parseInt(stepEl.getAttribute('data-step'), 10);
                    if (stepNum === currentStep) {
                        stepEl.classList.remove('hidden');
                    } else {
                        stepEl.classList.add('hidden');
                    }
                });

                // Update Step Indicators
                document.querySelectorAll('[data-step-indicator]').forEach(indEl => {
                    const stepNum = parseInt(indEl.getAttribute('data-step-indicator'), 10);
                    const numBadge = indEl.querySelector('.step-num');
                    const labelText = indEl.querySelector('.step-label');

                    if (stepNum === currentStep) {
                        indEl.classList.remove('opacity-50');
                        numBadge.className = 'step-num flex h-9 w-9 items-center justify-center rounded-full bg-[#0b3019] text-xs font-bold text-white shadow-sm ring-4 ring-[#0b3019]/10 transition-all';
                        labelText.className = 'step-label mt-2 text-xs font-semibold text-[#0b3019]';
                    } else if (stepNum < currentStep) {
                        indEl.classList.remove('opacity-50');
                        numBadge.className = 'step-num flex h-9 w-9 items-center justify-center rounded-full bg-emerald-600 text-xs font-bold text-white transition-all';
                        labelText.className = 'step-label mt-2 text-xs font-medium text-slate-700';
                    } else {
                        indEl.classList.add('opacity-50');
                        numBadge.className = 'step-num flex h-9 w-9 items-center justify-center rounded-full bg-slate-200 text-xs font-bold text-slate-600 transition-all';
                        labelText.className = 'step-label mt-2 text-xs font-medium text-slate-500';
                    }
                });

                // Update connecting lines
                document.querySelectorAll('[data-step-line]').forEach(lineEl => {
                    const lineNum = parseInt(lineEl.getAttribute('data-step-line'), 10);
                    if (lineNum < currentStep) {
                        lineEl.className = 'h-[2px] flex-1 bg-emerald-600 -mt-5 transition-all';
                    } else {
                        lineEl.className = 'h-[2px] flex-1 bg-slate-200 -mt-5 transition-all';
                    }
                });

                // Update button controls
                if (currentStep === 1) {
                    prevBtn.classList.add('hidden');
                } else {
                    prevBtn.classList.remove('hidden');
                }

                if (currentStep === totalSteps) {
                    nextBtn.classList.add('hidden');
                    submitBtn.classList.remove('hidden');
                } else {
                    nextBtn.classList.remove('hidden');
                    submitBtn.classList.add('hidden');
                }
            }

            function validateCurrentStep() {
                const activeStepEl = document.querySelector(`[data-step="${currentStep}"]`);
                if (!activeStepEl) return true;

                const inputs = activeStepEl.querySelectorAll('input[required], select[required]');
                let isValid = true;

                inputs.forEach(input => {
                    if (!input.checkValidity()) {
                        isValid = false;
                        input.reportValidity();
                    }
                });

                return isValid;
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', function() {
                    if (validateCurrentStep()) {
                        if (currentStep < totalSteps) {
                            currentStep++;
                            updateWizardUI();
                        }
                    }
                });
            }

            function updateStudentIdLabel() {
                const yearSelect = document.getElementById('year');
                const idLabel = document.getElementById('index_number_label');
                const idInput = document.getElementById('index_number');
                const idHintText = document.getElementById('id_hint_text');

                if (!yearSelect || !idLabel || !idInput) return;

                const selectedLevel = yearSelect.value;
                if (selectedLevel === '1') {
                    // Level 100 freshers
                    idLabel.innerHTML = 'Reference / Admission Number <span class="text-rose-500">*</span>';
                    idInput.placeholder = 'e.g. 9012345623';
                    if (idHintText) {
                        idHintText.textContent = 'Level 100 freshers without an Index Number should enter their Reference / Admission Number.';
                    }
                } else {
                    // Level 200, 300, 400
                    idLabel.innerHTML = 'Index Number <span class="text-rose-500">*</span>';
                    idInput.placeholder = 'e.g. FOE.55.012.088.24';
                    if (idHintText) {
                        idHintText.textContent = 'Enter your official university Index Number (e.g. FOE.55.012.088.24).';
                    }
                }
            }

            const yearSelectEl = document.getElementById('year');
            if (yearSelectEl) {
                yearSelectEl.addEventListener('change', updateStudentIdLabel);
            }

            if (prevBtn) {
                prevBtn.addEventListener('click', function() {
                    if (currentStep > 1) {
                        currentStep--;
                        updateWizardUI();
                    }
                });
            }

            updateStudentIdLabel();
            updateWizardUI();
        });
    </script>

</x-layouts.auth>