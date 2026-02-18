<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight">User Management</h1>
                <p class="text-white/60 text-sm mt-0.5">System users and iHRIS employee records</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Message --}}
            @if(session('success'))
                <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-xl">
                    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            {{-- iHRIS API Error --}}
            @if($apiError)
                <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-700 px-5 py-4 rounded-xl">
                    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm"><strong>iHRIS API:</strong> {{ $apiError }}</span>
                </div>
            @endif

            {{-- Search --}}
            <div class="card">
                <div class="p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                    <form method="GET" action="{{ route('users.index') }}" class="flex-1 flex gap-2">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ $search }}"
                                   placeholder="Search users or employees..."
                                   class="lgu-input pl-10" />
                        </div>
                        <button type="submit" class="btn-lgu">Search</button>
                        @if($search)
                            <a href="{{ route('users.index') }}" class="btn-outline">Clear</a>
                        @endif
                    </form>
                    <div class="flex items-center gap-4 text-sm text-gray-500 shrink-0">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-lgu-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <strong class="text-lgu-blue">{{ $users->count() }}</strong> users
                        </span>
                        @if($empTotal > 0)
                            <span class="text-gray-300">|</span>
                            <span class="flex items-center gap-1">
                                <strong class="text-green-600">{{ $empTotal }}</strong> iHRIS employees
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── System Users Table ── --}}
            <div class="card" x-data="{
                userModalOpen: {{ $errors->any() ? 'true' : 'false' }},
                isEdit: {{ old('_method') === 'PATCH' ? 'true' : 'false' }},
                form: {
                    id: '{{ old('id') }}',
                    name: '{{ old('name') }}',
                    email: '{{ old('email') }}',
                    role: '{{ old('role', 'Employee') }}',
                },
                actionUrl: '{{ old('_method') === 'PATCH' && old('id') ? route('users.update', old('id')) : route('users.store') }}',
                
                openCreate() {
                    this.isEdit = false;
                    this.form = { id: null, name: '', email: '', role: 'Employee' };
                    this.actionUrl = '{{ route("users.store") }}';
                    this.userModalOpen = true;
                },
                openEdit(user) {
                    this.isEdit = true;
                    this.form = {
                        id: user.id,
                        name: user.name,
                        email: user.email,
                        role: user.roles && user.roles.length > 0 ? user.roles[0].name : 'Employee'
                    };
                    // Construct update URL dynamically 
                    this.actionUrl = '{{ route("users.index") }}/' + user.id;
                    this.userModalOpen = true;
                }
            }">
                <div class="card-header flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-lgu-blue flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-800">System Users</h3>
                        <span class="badge badge-blue ml-1">{{ $users->count() }}</span>
                    </div>

                    @can('manage-users')
                    <button @click="openCreate()" class="btn-lgu text-sm py-1.5 px-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add User
                    </button>
                    @endcan
                </div>

                @if($users->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="lgu-table">
                            <thead>
                                <tr>
                                    <th class="w-12">#</th>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Account Type</th>
                                    @can('manage-users')
                                    <th class="text-right">Actions</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $i => $user)
                                    <tr>
                                        <td class="text-gray-400 font-mono text-xs">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                        <td>
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full bg-lgu-blue flex items-center justify-center text-white font-bold text-xs shrink-0 shadow">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-900 text-sm">{{ $user->name }}</p>
                                                    @if(auth()->id() === $user->id)
                                                        <span class="text-xs text-lgu-gold font-medium">You</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-gray-600 text-sm">{{ $user->email }}</td>
                                        <td>
                                            @php
                                                $role = $user->roles->first();
                                                $badgeMap = [
                                                    'Super Admin' => 'badge-purple',
                                                    'Admin'       => 'badge-blue',
                                                    'Employee'    => 'badge-green',
                                                ];
                                                $badgeClass = $badgeMap[$role?->name] ?? 'badge-gold';
                                            @endphp
                                            @if($role)
                                                <span class="{{ $badgeClass }}">{{ $role->name }}</span>
                                            @else
                                                <span class="badge badge-red">No Role</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->is_api_user)
                                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-lgu-blue-mid">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-lgu-blue-mid"></span>
                                                    iHRIS
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-400">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                                    Local
                                                </span>
                                            @endif
                                        </td>
                                        @can('manage-users')
                                        <td class="text-right">
                                            @if(auth()->id() !== $user->id)
                                                <div class="flex items-center justify-end gap-2">
                                                    <button @click="openEdit({{ $user->load('roles') }})" 
                                                            class="p-1.5 text-gray-400 hover:text-lgu-blue transition rounded-lg hover:bg-lgu-blue/10" title="Edit">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    </button>
                                                    <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 transition rounded-lg hover:bg-red-50" title="Delete">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400 italic">Current User</span>
                                            @endif
                                        </td>
                                        @endcan
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                        <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <p class="font-semibold text-gray-500">No users found</p>
                    </div>
                @endif

                {{-- User Modal --}}
                <div
                    x-show="userModalOpen"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-50 flex items-center justify-center p-4"
                    style="display:none"
                    @keydown.escape.window="userModalOpen = false"
                >
                    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="userModalOpen = false"></div>

                    <div
                        x-show="userModalOpen"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                        class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col"
                    >
                        <div class="bg-lgu-blue px-6 py-4 flex items-center justify-between">
                            <h3 class="font-bold text-white text-lg" x-text="isEdit ? 'Edit User' : 'Create New User'"></h3>
                            <button @click="userModalOpen = false" class="text-white/70 hover:text-white transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        
                        <form method="POST" :action="actionUrl" class="p-6 space-y-4">
                            @csrf
                            <input type="hidden" name="_method" :value="isEdit ? 'PATCH' : 'POST'">
                            <input type="hidden" name="id" x-model="form.id">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input type="text" name="name" x-model="form.name" required class="lgu-input w-full" placeholder="Full Name">
                                @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" x-model="form.email" required class="lgu-input w-full" placeholder="email@example.com">
                                @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                <select name="role" x-model="form.role" class="lgu-input w-full">
                                    @foreach($roles as $r)
                                        <option value="{{ $r->name }}">{{ $r->name }}</option>
                                    @endforeach
                                </select>
                                @error('role') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <hr class="border-gray-100 my-2">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    <span x-text="isEdit ? 'New Password (Optional)' : 'Password'"></span>
                                </label>
                                <input type="password" name="password" class="lgu-input w-full" placeholder="••••••••" :required="!isEdit">
                                @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="lgu-input w-full" placeholder="••••••••" :required="!isEdit">
                            </div>

                            <div class="pt-2 flex justify-end gap-3">
                                <button type="button" @click="userModalOpen = false" class="btn-outline">Cancel</button>
                                <button type="submit" class="btn-lgu" x-text="isEdit ? 'Save Changes' : 'Create User'"></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ── iHRIS Employees Card Grid ── --}}
            @if($empTotal > 0)
            <div class="card" x-data="{
                empModalOpen: false,
                isEdit: false,
                empForm: {
                    id: null,
                    first_name: '',
                    middle_name: '',
                    last_name: '',
                    extension: '',
                    email: ''
                },
                empActionUrl: '{{ route('employees.store') }}',

                openCreateEmp() {
                    this.isEdit = false;
                    this.empForm = { id: null, first_name: '', middle_name: '', last_name: '', extension: '', email: '' };
                    this.empActionUrl = '{{ route('employees.store') }}';
                    this.empModalOpen = true;
                },
                openEditEmp(emp) {
                    this.isEdit = true;
                    // Map API fields to form
                    this.empForm = {
                        id: emp.id || emp.employee_id,
                        first_name: emp.first_name || emp.name,
                        middle_name: emp.middle_name || '',
                        last_name: emp.last_name || emp.surname,
                        extension: emp.extension || '',
                        email: emp.email || ''
                    };
                    this.empActionUrl = '{{ route("employees.store") }}/' + this.empForm.id; // Correct route handled via method spoofing
                    this.empModalOpen = true;
                },
                
                // For View Detail Modal (existing)
                viewModalOpen: false,
                viewEmp: {}
            }">

                {{-- Section Header --}}
                <div class="card-header flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-green-600 flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-800">iHRIS Employees</h3>
                        <span class="badge badge-green ml-1">{{ $empTotal }} total</span>
                        <span class="inline-flex items-center gap-1 text-xs text-green-600 ml-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                            Live
                        </span>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <span class="text-xs text-gray-400 hidden sm:inline-block">
                            Page <strong class="text-gray-700">{{ $empPage }}</strong> of <strong class="text-gray-700">{{ $empPages }}</strong>
                        </span>
                        @can('manage-users')
                        <button @click="openCreateEmp()" class="btn-lgu text-sm py-1.5 px-3 flex items-center gap-2 bg-green-600 hover:bg-green-700 border-green-600 hover:border-green-700 text-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Employee
                        </button>
                        @endcan
                    </div>
                </div>

                {{-- Cards Grid --}}
                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($employees as $i => $emp)
                        @php
                            $globalIndex = ($empPage - 1) * $perPage + $i + 1;
                            $firstName   = $emp['name'] ?? $emp['first_name'] ?? '';
                            $lastName    = $emp['last_name'] ?? $emp['surname'] ?? '';
                            $middleName  = $emp['middle_name'] ?? '';
                            $ext         = $emp['extension'] ?? '';
                            $fullName    = trim("$firstName $middleName $lastName" . ($ext ? " $ext" : ''));
                            $email       = $emp['email'] ?? '—';
                            $empId       = $emp['id'] ?? $emp['employee_id'] ?? '—';
                            $initials    = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
                        @endphp
                        <div class="group relative bg-white border border-gray-200 rounded-xl p-4 hover:border-green-500 hover:shadow-md transition-all duration-200">
                            {{-- Row number --}}
                            <span class="absolute top-3 right-3 text-xs text-gray-300 font-mono">
                                #{{ str_pad($globalIndex, 2, '0', STR_PAD_LEFT) }}
                            </span>

                            {{-- Avatar + Name --}}
                            <div class="flex items-center gap-3 mb-3 cursor-pointer" @click="viewEmp = {{ json_encode($emp) }}; viewModalOpen = true">
                                <div class="w-11 h-11 rounded-full bg-green-600 flex items-center justify-center text-white font-bold text-sm shrink-0 shadow-sm group-hover:bg-green-700 transition-colors">
                                    {{ $initials ?: '??' }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900 text-sm truncate leading-tight group-hover:text-green-700 transition-colors">
                                        {{ $fullName ?: '(No Name)' }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">ID: {{ $empId }}</p>
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="flex items-center gap-2 text-xs text-gray-500 mb-3">
                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span class="truncate">{{ $email }}</span>
                            </div>

                            {{-- Footer Actions --}}
                            <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                                <button @click="viewEmp = {{ json_encode($emp) }}; viewModalOpen = true" class="text-xs text-green-600 font-medium hover:underline flex items-center gap-1">
                                    View Details
                                </button>
                                
                                @can('manage-users')
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button @click="openEditEmp({{ json_encode($emp) }})" class="p-1 text-gray-400 hover:text-green-600 transition rounded hover:bg-green-50" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('employees.destroy', $empId) }}" onsubmit="return confirm('Are you sure you want to delete this employee from iHRIS?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-gray-400 hover:text-red-600 transition rounded hover:bg-red-50" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                                @endcan
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($empPages > 1)
                <div class="px-5 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-xs text-gray-500">
                        Showing <strong>{{ ($empPage - 1) * $perPage + 1 }}</strong>–<strong>{{ min($empPage * $perPage, $empTotal) }}</strong>
                        of <strong>{{ $empTotal }}</strong> employees
                    </p>
                    <div class="flex items-center gap-1">
                        @if($empPage > 1)
                            <a href="{{ request()->fullUrlWithQuery(['emp_page' => $empPage - 1]) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium border border-gray-300 text-gray-600 hover:bg-gray-50 hover:border-green-600 hover:text-green-600 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                Prev
                            </a>
                        @else
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium border border-gray-200 text-gray-300 cursor-not-allowed">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                Prev
                            </span>
                        @endif

                        @php
                            $window = 2;
                            $start  = max(1, $empPage - $window);
                            $end    = min($empPages, $empPage + $window);
                        @endphp
                        @if($start > 1)
                            <a href="{{ request()->fullUrlWithQuery(['emp_page' => 1]) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium border border-gray-300 text-gray-600 hover:bg-gray-50 transition">1</a>
                            @if($start > 2)<span class="px-1 text-gray-400 text-xs">…</span>@endif
                        @endif
                        @for($p = $start; $p <= $end; $p++)
                            @if($p === $empPage)
                                <span class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-green-600 text-white border border-green-600">{{ $p }}</span>
                            @else
                                <a href="{{ request()->fullUrlWithQuery(['emp_page' => $p]) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium border border-gray-300 text-gray-600 hover:bg-gray-50 hover:border-green-600 hover:text-green-600 transition">{{ $p }}</a>
                            @endif
                        @endfor
                        @if($end < $empPages)
                            @if($end < $empPages - 1)<span class="px-1 text-gray-400 text-xs">…</span>@endif
                            <a href="{{ request()->fullUrlWithQuery(['emp_page' => $empPages]) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium border border-gray-300 text-gray-600 hover:bg-gray-50 transition">{{ $empPages }}</a>
                        @endif

                        @if($empPage < $empPages)
                            <a href="{{ request()->fullUrlWithQuery(['emp_page' => $empPage + 1]) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium border border-gray-300 text-gray-600 hover:bg-gray-50 hover:border-green-600 hover:text-green-600 transition">
                                Next
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @else
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium border border-gray-200 text-gray-300 cursor-not-allowed">
                                Next
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Employee Detail View Modal (Read Only) --}}
                <div
                    x-show="viewModalOpen"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-50 flex items-center justify-center p-4"
                    style="display:none"
                    @click.self="viewModalOpen = false"
                    @keydown.escape.window="viewModalOpen = false"
                >
                    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
                    <div
                        x-show="viewModalOpen"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                        class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[85vh] overflow-hidden flex flex-col"
                    >
                        <div class="bg-green-600 px-6 py-4 flex items-center justify-between shrink-0">
                            <h3 class="font-bold text-white text-lg">Employee Details</h3>
                            <button @click="viewModalOpen = false" class="text-white/70 hover:text-white transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="overflow-y-auto flex-1 p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <template x-for="[key, val] in Object.entries(viewEmp)" :key="key">
                                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1"
                                           x-text="key.replace(/_/g, ' ')"></p>
                                        <p class="text-sm font-medium text-gray-800 break-all"
                                           x-text="(val === null || val === '' || val === undefined)
                                               ? '—'
                                               : (typeof val === 'object' ? JSON.stringify(val) : String(val))">
                                        </p>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="px-6 py-4 border-t border-gray-100 shrink-0 flex justify-end">
                            <button @click="viewModalOpen = false" class="btn-outline text-sm">Close</button>
                        </div>
                    </div>
                </div>

                {{-- Employee Create/Edit Modal --}}
                <div
                    x-show="empModalOpen"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-50 flex items-center justify-center p-4"
                    style="display:none"
                    @keydown.escape.window="empModalOpen = false"
                >
                    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="empModalOpen = false"></div>
                    <div
                        x-show="empModalOpen"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                        class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col"
                    >
                        <div class="bg-green-600 px-6 py-4 flex items-center justify-between">
                            <h3 class="font-bold text-white text-lg" x-text="isEdit ? 'Edit Employee' : 'Add New Employee'"></h3>
                            <button @click="empModalOpen = false" class="text-white/70 hover:text-white transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        
                        <form method="POST" :action="empActionUrl" class="p-6 space-y-4">
                            @csrf
                            <input type="hidden" name="_method" :value="isEdit ? 'PUT' : 'POST'">
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                    <input type="text" name="first_name" x-model="empForm.first_name" required class="lgu-input w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                                    <input type="text" name="middle_name" x-model="empForm.middle_name" class="lgu-input w-full">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                    <input type="text" name="last_name" x-model="empForm.last_name" required class="lgu-input w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Extension</label>
                                    <input type="text" name="extension" x-model="empForm.extension" class="lgu-input w-full" placeholder="e.g. Jr.">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" x-model="empForm.email" required class="lgu-input w-full" placeholder="email@example.com">
                            </div>

                            <div class="pt-2 flex justify-end gap-3">
                                <button type="button" @click="empModalOpen = false" class="btn-outline">Cancel</button>
                                <button type="submit" class="btn-lgu bg-green-600 hover:bg-green-700 border-green-600 hover:border-green-700 text-white" 
                                        x-text="isEdit ? 'Update Employee' : 'Add Employee'"></button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
            @endif

        </div>
    </div>
</x-app-layout>
