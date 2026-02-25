{{-- Premium LGU Bayambang Navigation Menu --}}
<nav x-data="{ open: false, scrolled: false }" 
     @scroll.window="scrolled = (window.pageYOffset > 20)"
     :class="{'bg-lgu-blue/95 backdrop-blur-lg shadow-lgu-lg border-b border-white/10': scrolled, 'bg-lgu-blue shadow-lg border-b border-lgu-gold/20': !scrolled}"
     class="sticky top-0 z-50 transition-all duration-300">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            
            {{-- Left: Identity Area --}}
            <div class="flex items-center gap-6">
                {{-- Logo & Brand --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group relative">
                    <div class="absolute -inset-2 bg-gradient-to-r from-lgu-gold/0 via-lgu-gold/10 to-transparent opacity-0 group-hover:opacity-100 rounded-lg transition-all duration-500 blur-sm"></div>
                    <div class="relative w-11 h-11 rounded-full bg-white flex items-center justify-center shadow-lg ring-2 ring-lgu-gold/50 group-hover:ring-lgu-gold transition-all duration-300 z-10 overflow-hidden transform group-hover:scale-105">
                        <img src="{{ asset('images/logo.png') }}" class="w-full h-full object-cover" alt="LGU Seal">
                    </div>
                    <div class="leading-none relative z-10 hidden md:block">
                        <p class="text-white font-bold text-[15px] tracking-wide group-hover:text-lgu-gold-light transition-colors duration-300">LGU Bayambang</p>
                        <p class="text-white/60 text-[11px] uppercase tracking-wider mt-0.5 font-medium">Beneficiaries System</p>
                    </div>
                </a>

                {{-- Desktop Main Navigation Tabs --}}
                <div class="hidden sm:flex items-center gap-2 ml-4">
                    {{-- Dashboard --}}
                    <a href="{{ route('dashboard') }}"
                       class="relative px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 group overflow-hidden
                              {{ request()->routeIs('dashboard') ? 'text-white' : 'text-white/70 hover:text-white hover:bg-white/5' }}">
                        @if(request()->routeIs('dashboard'))
                            <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent rounded-xl"></div>
                            <div class="absolute bottom-0 left-1/4 right-1/4 h-0.5 bg-lgu-gold rounded-t-full shadow-[0_0_8px_rgba(252,211,77,0.8)]"></div>
                        @else
                            <div class="absolute bottom-0 left-1/2 right-1/2 h-0.5 bg-lgu-gold/50 rounded-t-full transition-all duration-300 group-hover:left-1/4 group-hover:right-1/4 opacity-0 group-hover:opacity-100"></div>
                        @endif
                        <span class="relative flex items-center gap-2">
                            <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-lgu-gold-light drop-shadow-md' : 'opacity-70 group-hover:opacity-100 group-hover:text-lgu-gold-light transition-colors' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Dashboard
                        </span>
                    </a>

                    {{-- Offices --}}
                    <a href="{{ route('offices.index') }}"
                       class="relative px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 group overflow-hidden
                              {{ request()->routeIs('offices.*') ? 'text-white' : 'text-white/70 hover:text-white hover:bg-white/5' }}">
                        @if(request()->routeIs('offices.*'))
                            <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent rounded-xl"></div>
                            <div class="absolute bottom-0 left-1/4 right-1/4 h-0.5 bg-lgu-gold rounded-t-full shadow-[0_0_8px_rgba(252,211,77,0.8)]"></div>
                        @else
                            <div class="absolute bottom-0 left-1/2 right-1/2 h-0.5 bg-lgu-gold/50 rounded-t-full transition-all duration-300 group-hover:left-1/4 group-hover:right-1/4 opacity-0 group-hover:opacity-100"></div>
                        @endif
                        <span class="relative flex items-center gap-2">
                            <svg class="w-5 h-5 {{ request()->routeIs('offices.*') ? 'text-lgu-gold-light drop-shadow-md' : 'opacity-70 group-hover:opacity-100 group-hover:text-lgu-gold-light transition-colors' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            Offices
                        </span>
                    </a>

                    {{-- Users --}}
                    @role('Super Admin|Admin')
                    <a href="{{ route('users.index') }}"
                       class="relative px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 group overflow-hidden
                              {{ request()->routeIs('users.*') ? 'text-white' : 'text-white/70 hover:text-white hover:bg-white/5' }}">
                        @if(request()->routeIs('users.*'))
                            <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent rounded-xl"></div>
                            <div class="absolute bottom-0 left-1/4 right-1/4 h-0.5 bg-lgu-gold rounded-t-full shadow-[0_0_8px_rgba(252,211,77,0.8)]"></div>
                        @else
                            <div class="absolute bottom-0 left-1/2 right-1/2 h-0.5 bg-lgu-gold/50 rounded-t-full transition-all duration-300 group-hover:left-1/4 group-hover:right-1/4 opacity-0 group-hover:opacity-100"></div>
                        @endif
                        <span class="relative flex items-center gap-2">
                            <svg class="w-5 h-5 {{ request()->routeIs('users.*') ? 'text-lgu-gold-light drop-shadow-md' : 'opacity-70 group-hover:opacity-100 group-hover:text-lgu-gold-light transition-colors' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Users
                        </span>
                    </a>
                    @endrole
                </div>
            </div>

            {{-- Right: Profile & Actions --}}
            <div class="hidden sm:flex items-center gap-4">
                {{-- Role Badge --}}
                @php $userRole = Auth::user()->roles->first(); @endphp
                @if($userRole)
                    <div class="px-3 py-1 bg-gradient-to-r from-lgu-gold/20 to-lgu-gold/5 border border-lgu-gold/40 rounded-full flex items-center shadow-inner">
                        <span class="w-1.5 h-1.5 rounded-full bg-lgu-gold-light mr-2 animate-pulse"></span>
                        <span class="text-[11px] font-bold text-lgu-gold-light uppercase tracking-wide">{{ $userRole->name }}</span>
                    </div>
                @endif

                {{-- Profile Dropdown --}}
                <div class="relative ml-2">
                    <x-dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-3 p-1 pr-3 rounded-full bg-white/5 border border-white/10 hover:bg-white/15 hover:border-white/25 transition-all duration-300 focus:outline-none group">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-lgu-gold to-yellow-600 flex items-center justify-center text-white font-bold text-xs shadow-md transform group-hover:scale-105 transition-transform duration-300">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                                <div class="hidden lg:block text-left">
                                    <p class="text-xs font-bold text-white leading-none group-hover:text-lgu-gold-light transition-colors">{{ explode(' ', Auth::user()->name)[0] }}</p>
                                </div>
                                <svg class="w-4 h-4 text-white/50 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-3 bg-gradient-to-b from-gray-50 to-white/50 border-b border-gray-100/50 backdrop-blur-sm">
                                <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 font-medium truncate mt-0.5">{{ Auth::user()->email }}</p>
                            </div>
                            
                            <div class="p-1.5">
                                <x-dropdown-link href="{{ route('profile.show') }}" class="rounded-lg transition-colors duration-200">
                                    <div class="flex items-center font-medium">
                                        <div class="p-1.5 bg-blue-50 text-lgu-blue rounded-md mr-3">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        </div>
                                        {{ __('My Profile') }}
                                    </div>
                                </x-dropdown-link>

                                <div class="my-1 border-t border-gray-100"></div>

                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf
                                    <x-dropdown-link href="{{ route('logout') }}"
                                        @click.prevent="Swal.fire({
                                            title: 'Sign Out?',
                                            text: 'Are you sure you want to end your session?',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#ef4444',
                                            cancelButtonColor: '#64748b',
                                            confirmButtonText: 'Yes, sign out',
                                            cancelButtonText: 'Cancel',
                                            customClass: {
                                                confirmButton: 'shadow-lg shadow-red-500/30',
                                            }
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                $root.submit();
                                            }
                                        });"
                                        class="rounded-lg text-red-600 hover:text-red-700 hover:bg-red-50 transition-colors duration-200">
                                        <div class="flex items-center font-medium">
                                            <div class="p-1.5 bg-red-50 text-red-600 rounded-md mr-3 group-hover:bg-red-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            </div>
                                            {{ __('Sign Out') }}
                                        </div>
                                    </x-dropdown-link>
                                </form>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            {{-- Mobile menu button --}}
            <div class="sm:hidden flex items-center">
                <button @click="open = !open" class="text-white/80 hover:text-white p-2.5 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-lgu-gold">
                    <svg class="w-5 h-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Sidebar Menu Overlay --}}
    <div x-show="open" 
         class="sm:hidden fixed inset-0 z-40 bg-gray-900/60 backdrop-blur-sm transition-opacity"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         style="display: none;"></div>

    {{-- Mobile Menu Content (Slide-over) --}}
    <div x-show="open" 
         class="sm:hidden fixed inset-y-0 right-0 z-50 w-[280px] bg-lgu-blue shadow-2xl overflow-y-auto transform transition-transform border-l border-white/10"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         style="display: none;">
        
        <div class="pt-5 pb-4 px-5 border-b border-white/10 bg-black/20">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-gradient-to-br from-lgu-gold to-yellow-600 flex items-center justify-center text-white font-bold shadow-lg ring-2 ring-white/10">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-white font-bold text-[15px] leading-tight">{{ Auth::user()->name }}</p>
                        <p class="text-white/60 text-[11px] font-medium mt-0.5 truncate max-w-[150px]">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <button @click="open = false" class="p-2 rounded-full bg-white/5 hover:bg-white/10 text-white/70 hover:text-white transition-colors focus:outline-none">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            @if($userRole)
                <div class="mt-4 inline-flex items-center px-3 py-1.5 rounded-full bg-gradient-to-r from-lgu-gold/20 to-lgu-gold/5 border border-lgu-gold/30 text-xs font-bold text-lgu-gold-light tracking-wide shadow-inner">
                    <span class="w-1.5 h-1.5 rounded-full bg-lgu-gold-light mr-2 animate-pulse"></span>
                    {{ $userRole->name }}
                </div>
            @endif
        </div>

        <div class="pt-6 pb-6 px-4 space-y-2">
            <div class="px-3 text-[10px] font-bold uppercase tracking-widest text-white/40 mb-3">Main Navigation</div>
            
            <a href="{{ route('dashboard') }}" 
               class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold transition-all duration-300
                      {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-lgu-gold/20 to-transparent border-l-4 border-lgu-gold text-white shadow-md' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                <div class="{{ request()->routeIs('dashboard') ? 'text-lgu-gold-light' : 'text-white/40' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
                Dashboard
            </a>
            
            <a href="{{ route('offices.index') }}" 
               class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold transition-all duration-300
                      {{ request()->routeIs('offices.*') ? 'bg-gradient-to-r from-lgu-gold/20 to-transparent border-l-4 border-lgu-gold text-white shadow-md' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                <div class="{{ request()->routeIs('offices.*') ? 'text-lgu-gold-light' : 'text-white/40' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                Offices
            </a>
            
            @role('Super Admin|Admin')
            <a href="{{ route('users.index') }}" 
               class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold transition-all duration-300
                      {{ request()->routeIs('users.*') ? 'bg-gradient-to-r from-lgu-gold/20 to-transparent border-l-4 border-lgu-gold text-white shadow-md' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                <div class="{{ request()->routeIs('users.*') ? 'text-lgu-gold-light' : 'text-white/40' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                Users
            </a>
            @endrole

            <div class="mt-10 px-3 text-[10px] font-bold uppercase tracking-widest text-white/40 mb-3 block">Account & Settings</div>
            
            <a href="{{ route('profile.show') }}" 
               class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold transition-all duration-300 text-white/70 hover:bg-white/5 hover:text-white">
                <div class="text-white/40">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                My Profile
            </a>

            <form method="POST" action="{{ route('logout') }}" x-data class="mt-2">
                @csrf
                <button @click.prevent="Swal.fire({
                        title: 'Sign Out?',
                        text: 'Ready to securely sign out of your account?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        confirmButtonText: 'Yes, sign out',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $root.submit();
                        }
                    });"
                    class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold transition-all duration-300 text-red-300 hover:bg-red-900/30 hover:text-red-200">
                    <div class="text-red-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </div>
                    Sign Out
                </button>
            </form>
        </div>
    </div>
</nav>
