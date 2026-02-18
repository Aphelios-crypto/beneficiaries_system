<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Offices Management</h1>
                <p class="text-white/60 text-sm mt-0.5">Government offices from iHRIS API</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-white/10 text-white border border-white/20">
                <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                Live from iHRIS
            </span>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        staffModalOpen: false,
        activeOfficeName: '',
        staffList: [],
        loading: false,

        async viewStaff(id, name) {
            this.activeOfficeName = name;
            this.staffModalOpen = true;
            this.loading = true;
            this.staffList = [];
            
            try {
                const response = await fetch(`/offices/${id}/employees`);
                const data = await response.json();
                this.staffList = data.employees || [];
            } catch (e) {
                console.error(e);
                this.staffList = [];
            } finally {
                this.loading = false;
            }
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Error Banner --}}
            @if($error)
                <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl">
                    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-sm">API Error</p>
                        <p class="text-sm">{{ $error }}</p>
                    </div>
                </div>
            @endif

            {{-- Search Bar --}}
            <div class="card">
                <div class="p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                    <form method="GET" action="{{ route('offices.index') }}" class="flex-1 flex gap-2">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ $search }}"
                                   placeholder="Search offices by name..."
                                   class="lgu-input pl-10" />
                        </div>
                        <button type="submit" class="btn-lgu">
                            Search
                        </button>
                        @if($search)
                            <a href="{{ route('offices.index') }}" class="btn-outline">
                                Clear
                            </a>
                        @endif
                    </form>
                    <div class="text-sm text-gray-500 shrink-0 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-lgu-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="font-bold text-lgu-blue">{{ count($offices) }}</span>
                        <span>office(s)</span>
                    </div>
                </div>
            </div>

            {{-- Offices Table --}}
            <div class="card">
                @if(count($offices) > 0)
                    <div class="overflow-x-auto">
                        <table class="lgu-table">
                            <thead>
                                <tr>
                                    <th class="w-12">#</th>
                                    <th>Office Name</th>
                                    <th>Code</th>
                                    <th>Office Head</th>
                                    <th>Staff</th>
                                    <th>Additional Info</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($offices as $i => $office)
                                    <tr>
                                        <td class="text-gray-400 font-mono text-xs">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                        <td>
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-lgu-blue/10 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4 text-lgu-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                    </svg>
                                                </div>
                                                <span class="font-semibold text-gray-900">
                                                    {{ $office['name'] ?? $office['office_name'] ?? '—' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            @php $code = $office['code'] ?? $office['office_code'] ?? null; @endphp
                                            @if($code)
                                                <span class="badge badge-blue font-mono">{{ $code }}</span>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php $head = $office['head'] ?? $office['office_head'] ?? $office['head_name'] ?? null; @endphp
                                            @if($head)
                                                <div class="flex items-center gap-2">
                                                    <div class="w-6 h-6 rounded-full bg-lgu-gold/20 flex items-center justify-center text-lgu-blue font-bold text-xs">
                                                        {{ strtoupper(substr($head, 0, 1)) }}
                                                    </div>
                                                    <span class="text-gray-700">{{ $head }}</span>
                                                </div>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($office['id'] ?? false)
                                                <button @click="viewStaff('{{ $office['id'] }}', '{{ addslashes($office['name'] ?? $office['office_name'] ?? 'Office') }}')" 
                                                        class="text-xs font-semibold text-lgu-blue hover:text-lgu-blue-dark hover:underline flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                    View Staff
                                                </button>
                                            @else
                                                <span class="text-xs text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $known = ['name','office_name','code','office_code','head','office_head','head_name','id'];
                                                $extra = array_diff_key($office, array_flip($known));
                                            @endphp
                                            <div class="flex flex-wrap gap-1">
                                                @foreach(array_slice($extra, 0, 3) as $key => $val)
                                                    <span class="badge badge-gold text-xs">
                                                        {{ $key }}: {{ is_array($val) ? '...' : Str::limit((string)$val, 20) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-24 text-gray-400">
                        <div class="w-20 h-20 rounded-2xl bg-gray-100 flex items-center justify-center mb-5">
                            <svg class="w-10 h-10 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <p class="text-lg font-semibold text-gray-500">No offices found</p>
                        <p class="text-sm mt-1">{{ $search ? 'Try a different search term.' : 'No data returned from iHRIS API.' }}</p>
                        @if($search)
                            <a href="{{ route('offices.index') }}" class="btn-lgu mt-5 text-sm">Clear Search</a>
                        @endif
                    </div>
                @endif
            </div>

        </div>

        {{-- Staff List Modal --}}
        <div
            x-show="staffModalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="display:none"
            @keydown.escape.window="staffModalOpen = false"
        >
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="staffModalOpen = false"></div>
            <div
                x-show="staffModalOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[80vh] overflow-hidden flex flex-col"
            >
                <div class="bg-lgu-blue px-6 py-4 flex items-center justify-between shrink-0">
                    <div>
                        <h3 class="font-bold text-white text-lg">Office Staff</h3>
                        <p class="text-white/60 text-xs" x-text="activeOfficeName"></p>
                    </div>
                    <button @click="staffModalOpen = false" class="text-white/70 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div class="overflow-y-auto flex-1 p-0">
                    <div x-show="loading" class="flex flex-col items-center justify-center py-12 text-gray-400">
                        <svg class="animate-spin h-8 w-8 text-lgu-blue mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-sm font-medium">Loading staff list...</p>
                    </div>

                    <div x-show="!loading && staffList.length === 0" class="flex flex-col items-center justify-center py-12 text-gray-400">
                        <p class="text-sm">No employees found assigned to this office.</p>
                    </div>

                    <ul x-show="!loading && staffList.length > 0" class="divide-y divide-gray-100">
                        <template x-for="emp in staffList" :key="emp.id || emp.employee_id">
                            <li class="px-6 py-3 hover:bg-gray-50 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-lgu-blue/10 flex items-center justify-center text-lgu-blue font-bold text-xs shrink-0">
                                    <span x-text="(emp.first_name || emp.name || '?').charAt(0)"></span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900" 
                                       x-text="`${emp.first_name || emp.name || ''} ${emp.middle_name || ''} ${emp.last_name || emp.surname || ''} ${emp.extension || ''}`.trim() || 'Unknown Name'"></p>
                                    <p class="text-xs text-gray-500" x-text="emp.position || '—'"></p>
                                </div>
                            </li>
                        </template>
                    </ul>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-100 shrink-0 bg-gray-50 flex justify-between items-center">
                    <span class="text-xs text-gray-400" x-show="!loading && staffList.length > 0">
                        Total: <strong x-text="staffList.length"></strong>
                    </span>
                    <button @click="staffModalOpen = false" class="btn-outline text-sm ml-auto">Close</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
