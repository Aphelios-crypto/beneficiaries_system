<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Dashboard</h1>
                <p class="text-white/60 text-sm mt-0.5">Welcome back, {{ Auth::user()->name }}</p>
            </div>
            <div class="flex items-center gap-2 text-white/60 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ now()->format('F j, Y') }}
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Welcome Banner --}}
            <div class="relative bg-lgu-blue rounded-2xl overflow-hidden shadow-lgu-lg">
                {{-- Background pattern --}}
                <div class="absolute inset-0 opacity-10">
                    <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse">
                                <circle cx="2" cy="2" r="1.5" fill="white"/>
                            </pattern>
                        </defs>
                        <rect width="100%" height="100%" fill="url(#dots)"/>
                    </svg>
                </div>
                {{-- Gold top bar --}}
                <div class="absolute top-0 left-0 right-0 h-1 bg-gold-gradient"></div>

                <div class="relative z-10 flex flex-col sm:flex-row items-center gap-6 p-8">

                    <div>
                        <p class="text-lgu-gold-light text-sm font-semibold uppercase tracking-widest mb-1">Local Government Unit</p>
                        <h2 class="text-white text-3xl font-extrabold tracking-tight">Bayambang, Pangasinan</h2>
                        <p class="text-white/60 mt-1">Beneficiaries Financial Management System</p>
                    </div>
                    <div class="sm:ml-auto text-right hidden sm:block">
                        @php $role = Auth::user()->roles->first(); @endphp
                        @if($role)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-lgu-gold/20 text-lgu-gold-light border border-lgu-gold/30 uppercase tracking-wide">
                                {{ $role->name }}
                            </span>
                        @endif
                        <p class="text-white/40 text-xs mt-2">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                {{-- Stat: Offices --}}
                <a href="{{ route('offices.index') }}" class="card group hover:shadow-lgu-lg transition-all duration-200 hover:-translate-y-0.5">
                    <div class="p-6 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center group-hover:bg-lgu-blue transition-colors duration-200">
                            <svg class="w-6 h-6 text-lgu-blue group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Offices</p>
                            <p class="text-2xl font-bold text-lgu-blue mt-0.5">{{ $officesCount }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">From iHRIS API</p>
                        </div>
                    </div>
                    <div class="h-1 bg-lgu-gradient opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                </a>

                {{-- Stat: Users --}}
                @role('Super Admin|Admin')
                <a href="{{ route('users.index') }}" class="card group hover:shadow-lgu-lg transition-all duration-200 hover:-translate-y-0.5">
                    <div class="p-6 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center group-hover:bg-purple-600 transition-colors duration-200">
                            <svg class="w-6 h-6 text-purple-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">System Users</p>
                            <p class="text-2xl font-bold text-purple-600 mt-0.5">{{ \App\Models\User::count() }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">Registered accounts</p>
                        </div>
                    </div>
                    <div class="h-1 bg-gradient-to-r from-purple-500 to-purple-700 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                </a>
                @endrole

                {{-- Stat: iHRIS Status --}}
                <div class="card">
                    <div class="p-6 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">iHRIS API</p>
                            <p class="text-sm font-bold text-green-600 mt-0.5">Connected</p>
                            <p class="text-xs text-gray-400 mt-0.5">ihris.bayambang.gov.ph</p>
                        </div>
                    </div>
                    <div class="h-1 bg-gradient-to-r from-green-400 to-green-600"></div>
                </div>

                {{-- Stat: Your Role --}}
                <div class="card">
                    <div class="p-6 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Your Role</p>
                            @php $role = Auth::user()->roles->first(); @endphp
                            <p class="text-sm font-bold text-yellow-700 mt-0.5">{{ $role?->name ?? 'No Role' }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ Auth::user()->is_api_user ? 'iHRIS Account' : 'Local Account' }}</p>
                        </div>
                    </div>
                    <div class="h-1 bg-gold-gradient"></div>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Offices Quick Link --}}
                <div class="card">
                    <div class="card-header">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-lgu-blue flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-800">Offices Management</h3>
                        </div>
                        <a href="{{ route('offices.index') }}" class="text-xs text-lgu-blue-mid hover:text-lgu-blue font-medium transition-colors">
                            View all →
                        </a>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-500 text-sm leading-relaxed">
                            Browse and search all government offices fetched directly from the iHRIS system. View office heads, codes, and organizational details.
                        </p>
                        <a href="{{ route('offices.index') }}" class="btn-lgu mt-4 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Go to Offices
                        </a>
                    </div>
                </div>

                {{-- User Management Quick Link --}}
                @role('Super Admin|Admin')
                <div class="card">
                    <div class="card-header">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-purple-600 flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-800">User Management</h3>
                        </div>
                        <a href="{{ route('users.index') }}" class="text-xs text-lgu-blue-mid hover:text-lgu-blue font-medium transition-colors">
                            View all →
                        </a>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-500 text-sm leading-relaxed">
                            Manage system users and their roles. Assign Super Admin, Admin, or Employee roles to control access levels within the system.
                        </p>
                        <a href="{{ route('users.index') }}" class="btn-lgu mt-4 text-sm" style="background: #7c3aed;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Manage Users
                        </a>
                    </div>
                </div>
                @else
                <div class="card">
                    <div class="card-header">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-lgu-green flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-800">My Profile</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-500 text-sm leading-relaxed">
                            View and update your profile information, manage your account settings and security preferences.
                        </p>
                        <a href="{{ route('profile.show') }}" class="btn-lgu mt-4 text-sm" style="background: #2E7D32;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            View Profile
                        </a>
                    </div>
                </div>
                @endrole
            </div>

        </div>
    </div>
</x-app-layout>
