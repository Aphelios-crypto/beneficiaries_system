{{-- LGU Bayambang Navigation Menu --}}
<nav x-data="{ open: false }" class="bg-lgu-blue shadow-lgu-lg sticky top-0 z-50">

    {{-- Top bar: LGU identity strip --}}
    <div class="bg-lgu-blue border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-14">

            {{-- Logo + Name --}}
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                {{-- Bayambang Seal SVG --}}
                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow ring-2 ring-lgu-gold/60 shrink-0 overflow-hidden">
                    <img src="{{ asset('images/logo.png') }}" class="w-full h-full object-cover" alt="LGU Seal">
                </div>
                <div class="leading-tight">
                    <p class="text-white font-bold text-sm tracking-wide group-hover:text-lgu-gold-light transition-colors">LGU Bayambang</p>
                    <p class="text-white/60 text-xs">Beneficiaries System</p>
                </div>
            </a>

            {{-- Desktop Nav Links --}}
            <div class="hidden sm:flex items-center gap-1">
                <a href="{{ route('dashboard') }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </span>
                </a>
                <a href="{{ route('offices.index') }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('offices.*') ? 'bg-white/20 text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Offices
                    </span>
                </a>
                @role('Super Admin|Admin')
                <a href="{{ route('users.index') }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('users.*') ? 'bg-white/20 text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Users
                    </span>
                </a>
                @endrole
            </div>

            {{-- Right: User Dropdown --}}
            <div class="hidden sm:flex items-center gap-3">
                {{-- Role badge --}}
                @php $userRole = Auth::user()->roles->first(); @endphp
                @if($userRole)
                    <span class="hidden lg:inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-lgu-gold/20 text-lgu-gold-light border border-lgu-gold/30">
                        {{ $userRole->name }}
                    </span>
                @endif

                <x-dropdown align="right" width="52">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-2 text-sm text-white/80 hover:text-white transition-colors focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-lgu-gold flex items-center justify-center text-lgu-blue font-bold text-xs shadow">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                            <span class="hidden lg:block font-medium">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <x-dropdown-link href="{{ route('profile.show') }}">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ __('My Profile') }}
                        </x-dropdown-link>
                        <div class="border-t border-gray-100"></div>
                        <form method="POST" action="{{ route('logout') }}" x-data>
                            @csrf
                            <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();" class="text-red-600 hover:text-red-700 hover:bg-red-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                {{ __('Sign Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Mobile hamburger --}}
            <div class="sm:hidden">
                <button @click="open = !open" class="text-white/80 hover:text-white p-2 rounded-lg hover:bg-white/10 transition">
                    <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden bg-lgu-blue border-t border-white/10">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }} transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
            <a href="{{ route('offices.index') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('offices.*') ? 'bg-white/20 text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }} transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Offices
            </a>
            @role('Super Admin|Admin')
            <a href="{{ route('users.index') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('users.*') ? 'bg-white/20 text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }} transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Users
            </a>
            @endrole
        </div>
        <div class="px-4 py-3 border-t border-white/10">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-lgu-gold flex items-center justify-center text-lgu-blue font-bold text-sm">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div>
                    <p class="text-white text-sm font-medium">{{ Auth::user()->name }}</p>
                    <p class="text-white/50 text-xs">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <a href="{{ route('profile.show') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-white/75 hover:bg-white/10 hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                My Profile
            </a>
            <form method="POST" action="{{ route('logout') }}" x-data>
                @csrf
                <button @click.prevent="$root.submit()" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-red-300 hover:bg-red-900/30 hover:text-red-200 transition mt-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Sign Out
                </button>
            </form>
        </div>
    </div>
</nav>
