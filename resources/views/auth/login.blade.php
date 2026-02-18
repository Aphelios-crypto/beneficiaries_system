<x-guest-layout>
    <div class="flex min-h-screen">

        {{-- Left Panel: LGU Branding --}}
        <div class="hidden lg:flex lg:w-1/2 flex-col items-center justify-center p-12 relative overflow-hidden">
            {{-- Background pattern --}}
            <div class="absolute inset-0 opacity-10">
                <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                            <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#grid)"/>
                </svg>
            </div>

            {{-- Gold top accent bar --}}
            <div class="absolute top-0 left-0 right-0 h-1 bg-gold-gradient"></div>

            <div class="relative z-10 text-center">
                {{-- Seal --}}
                <div class="w-36 h-36 rounded-full bg-white/10 border-4 border-lgu-gold/50 flex items-center justify-center mx-auto mb-8 shadow-2xl backdrop-blur-sm overflow-hidden">
                    <img src="{{ asset('images/logo.png') }}" class="w-full h-full object-cover" alt="LGU Seal">
                </div>

                <h1 class="text-4xl font-extrabold text-white tracking-tight mb-2">LGU Bayambang</h1>
                <p class="text-lgu-gold-light font-semibold text-lg mb-1">Pangasinan, Philippines</p>
                <div class="w-16 h-0.5 bg-lgu-gold mx-auto my-4"></div>
                <h2 class="text-white/90 text-xl font-semibold mb-2">Beneficiaries Financial</h2>
                <h2 class="text-white/90 text-xl font-semibold">Management System</h2>
                <p class="text-white/50 text-sm mt-6 max-w-xs mx-auto leading-relaxed">
                    A secure platform for managing beneficiary records and financial assistance programs.
                </p>
            </div>

            {{-- Bottom footer --}}
            <p class="absolute bottom-6 text-white/30 text-xs">© {{ date('Y') }} LGU Bayambang. All rights reserved.</p>
        </div>

        {{-- Right Panel: Login Form --}}
        <div class="w-full lg:w-1/2 flex flex-col items-center justify-center px-6 py-12 bg-lgu-cream">

            {{-- Mobile logo --}}
            <div class="lg:hidden text-center mb-8">
                <div class="w-20 h-20 rounded-full bg-lgu-blue border-4 border-lgu-gold/50 flex items-center justify-center mx-auto mb-4 shadow-lgu-lg overflow-hidden">
                    <img src="{{ asset('images/logo.png') }}" class="w-full h-full object-cover" alt="LGU Seal">
                </div>
                <h1 class="text-2xl font-bold text-lgu-blue">LGU Bayambang</h1>
                <p class="text-gray-500 text-sm">Beneficiaries Financial Management System</p>
            </div>

            <div class="w-full max-w-md">
                <div class="bg-white rounded-2xl shadow-lgu-lg p-8 border border-gray-100">
                    {{-- Header --}}
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-lgu-blue">Welcome back</h2>
                        <p class="text-gray-500 text-sm mt-1">Sign in with your iHRIS credentials</p>
                    </div>

                    {{-- Validation Errors --}}
                    <x-validation-errors class="mb-5" />

                    @session('status')
                        <div class="mb-5 p-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">
                            {{ $value }}
                        </div>
                    @endsession

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Email Address
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                    </svg>
                                </div>
                                <input id="email" type="email" name="email" value="{{ old('email') }}"
                                       required autofocus autocomplete="username"
                                       class="lgu-input pl-10"
                                       placeholder="you@bayambang.gov.ph" />
                            </div>
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input id="password" type="password" name="password"
                                       required autocomplete="current-password"
                                       class="lgu-input pl-10"
                                       placeholder="••••••••" />
                            </div>
                        </div>

                        {{-- Remember me --}}
                        <div class="flex items-center justify-between">
                            <label for="remember_me" class="flex items-center gap-2 cursor-pointer">
                                <input id="remember_me" type="checkbox" name="remember"
                                       class="w-4 h-4 rounded border-gray-300 text-lgu-blue focus:ring-lgu-blue cursor-pointer">
                                <span class="text-sm text-gray-600">Remember me</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                   class="text-sm text-lgu-blue-mid hover:text-lgu-blue font-medium transition-colors">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="btn-lgu w-full justify-center py-3 text-base mt-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Sign In
                        </button>
                    </form>

                    {{-- iHRIS note --}}
                    <div class="mt-6 p-3 bg-blue-50 rounded-lg border border-blue-100 flex items-start gap-2">
                        <svg class="w-4 h-4 text-lgu-blue-mid mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-xs text-blue-700">Use your <strong>iHRIS</strong> account credentials to sign in. Contact your system administrator if you need access.</p>
                    </div>
                </div>

                <p class="text-center text-xs text-white/50 mt-6 lg:text-gray-400">
                    © {{ date('Y') }} LGU Bayambang, Pangasinan
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
