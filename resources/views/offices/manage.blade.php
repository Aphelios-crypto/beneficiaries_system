<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Manage Local Offices</h1>
                <p class="text-white/60 text-sm mt-0.5">Create, edit, and delete local office records</p>
            </div>
            
            <div class="flex gap-2">
                <a href="{{ route('offices.index') }}" class="btn-outline text-white border-white/20 hover:bg-white/10">
                    Back to Live View
                </a>
                <form action="{{ route('offices.sync') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-white/10 hover:bg-white/20 text-white border border-white/20 px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Sync iHRIS
                    </button>
                </form>
                <button @click="createOffice()" class="bg-lgu-gold hover:bg-yellow-400 text-lgu-blue border border-yellow-300 px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition shadow-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Office
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        staffModalOpen: false,
        officeModalOpen: false,
        deleteModalOpen: false,
        isEdit: false,
        
        activeOfficeName: '',
        staffList: [],
        loading: false,

        form: { name: '', code: '', head: '', uuid: '' },
        editUrl: '',
        deleteUrl: '',
        deleteName: '',

        createOffice() {
            this.isEdit = false;
            this.form = { name: '', code: '', head: '', uuid: '' };
            this.officeModalOpen = true;
        },

        editOffice(office) {
            this.isEdit = true;
            this.form = { 
                name: office.name || '', 
                code: office.code || '', 
                head: office.head || '', 
                uuid: office.uuid || '' 
            };
            this.editUrl = `{{ url('/offices') }}/${office.id}`;
            this.officeModalOpen = true;
        },

        confirmDelete(office) {
            this.deleteUrl = `{{ url('/offices') }}/${office.id}`;
            this.deleteName = office.name;
            this.deleteModalOpen = true;
        },
        
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

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-xl">
                    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="font-semibold text-sm">{{ session('success') }}</p>
                </div>
            @endif
            
            @if(session('error'))
                <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl">
                    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                         <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <p class="font-semibold text-sm">{{ session('error') }}</p>
                </div>
            @endif

            {{-- Search Bar --}}
            <div class="card">
                <div class="p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                    <form method="GET" action="{{ route('offices.manage') }}" class="flex-1 flex gap-2">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ $search }}"
                                   placeholder="Search local offices..."
                                   class="lgu-input pl-10" />
                        </div>
                        <button type="submit" class="btn-lgu">Search</button>
                        @if($search)
                            <a href="{{ route('offices.manage') }}" class="btn-outline">Clear</a>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Offices Table --}}
            <div class="card">
                @if($offices->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="lgu-table">
                            <thead>
                                <tr>
                                    <th class="w-12">#</th>
                                    <th>Office Name</th>
                                    <th>Code</th>
                                    <th>Office Head</th>
                                    <th>Linked</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($offices as $office)
                                    <tr>
                                        <td class="text-gray-400 font-mono text-xs">{{ $office->id }}</td>
                                        <td>
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                    </svg>
                                                </div>
                                                <span class="font-semibold text-gray-900">{{ $office->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($office->code)
                                                <span class="badge badge-blue font-mono">{{ $office->code }}</span>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($office->head)
                                                <div class="flex items-center gap-2">
                                                    <div class="w-6 h-6 rounded-full bg-lgu-gold/20 flex items-center justify-center text-lgu-blue font-bold text-xs">
                                                        {{ strtoupper(substr($office->head, 0, 1)) }}
                                                    </div>
                                                    <span class="text-gray-700">{{ $office->head }}</span>
                                                </div>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($office->uuid)
                                                <div class="flex items-center gap-1.5 text-green-600 bg-green-50 px-2 py-1 rounded text-xs font-semibold w-fit">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    Yes
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-xs">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="flex items-center justify-end gap-2">
                                                <button @click="viewStaff({{ $office->id }}, '{{ addslashes($office->name) }}')"
                                                    class="btn-outline text-xs px-2 py-1.5 h-8"
                                                    :class="!{{ $office->uuid ? 'true' : 'false' }} ? 'opacity-50 cursor-not-allowed' : ''"
                                                    :disabled="!{{ $office->uuid ? 'true' : 'false' }}" 
                                                    title="Preview Staff">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </button>
                                                <button @click='editOffice(@json($office))' class="btn-outline text-xs px-2 py-1.5 h-8">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </button>
                                                <button @click="confirmDelete({ id: {{ $office->id }}, name: '{{ addslashes($office->name) }}' })" class="btn-outline text-red-600 hover:bg-red-50 hover:border-red-200 text-xs px-2 py-1.5 h-8">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-5 py-4 border-t border-gray-100">
                        {{ $offices->links() }}
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-24 text-gray-400">
                        <div class="w-20 h-20 rounded-2xl bg-gray-100 flex items-center justify-center mb-5">
                            <svg class="w-10 h-10 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <p class="text-lg font-semibold text-gray-500">No local offices found</p>
                        <p class="text-sm mt-1">Populate this list by syncing from iHRIS or creating manually.</p>
                        <div class="flex gap-3 mt-5">
                            <button @click="createOffice()" class="btn-lgu">Create First Office</button>
                            <form action="{{ route('offices.sync') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-outline">Sync from iHRIS</button>
                            </form>
                         </div>
                    </div>
                @endif
            </div>

        </div>

        {{-- Create/Edit Modal --}}
        <div
            x-show="officeModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="display:none"
            @keydown.escape.window="officeModalOpen = false"
        >
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="officeModalOpen = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col">
                <div class="bg-lgu-blue px-6 py-4 flex items-center justify-between shrink-0">
                    <h3 class="font-bold text-white text-lg" x-text="isEdit ? 'Edit Local Office' : 'Create Local Office'"></h3>
                    <button @click="officeModalOpen = false" class="text-white/70 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <form :action="isEdit ? editUrl : '{{ route('offices.store') }}'" method="POST" class="p-6 space-y-4">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Office Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" x-model="form.name" required
                               class="lgu-input w-full" placeholder="e.g. City Mayor's Office">
                    </div>

                    {{-- Code --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Office Code</label>
                        <input type="text" name="code" x-model="form.code"
                               class="lgu-input w-full" placeholder="e.g. CMO">
                    </div>

                    {{-- Head --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Office Head</label>
                        <input type="text" name="head" x-model="form.head"
                               class="lgu-input w-full" placeholder="e.g. Hon. Juan Dela Cruz">
                    </div>

                    {{-- UUID --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            iHRIS UUID 
                            <span class="text-xs font-normal text-gray-400 ml-1">(Optional, for linking staff in Live View)</span>
                        </label>
                        <input type="text" name="uuid" x-model="form.uuid"
                               class="lgu-input w-full font-mono text-sm" placeholder="e.g. office|12345">
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" @click="officeModalOpen = false" class="btn-outline">Cancel</button>
                        <button type="submit" class="btn-lgu">Save Local Office</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Delete Confirmation Modal --}}
        <div
            x-show="deleteModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="display:none"
            @keydown.escape.window="deleteModalOpen = false"
        >
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="deleteModalOpen = false"></div>
            <div class="relative bg-white rounded-xl shadow-xl w-full max-w-sm overflow-hidden p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-center text-gray-900 mb-2">Delete Local Office?</h3>
                <p class="text-center text-sm text-gray-500 mb-6">
                    Are you sure you want to delete <strong x-text="deleteName"></strong>? This action will not affect iHRIS data.
                </p>
                
                <form :action="deleteUrl" method="POST" class="flex gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="deleteModalOpen = false" class="flex-1 btn-outline justify-center">Cancel</button>
                    <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition justify-center">Delete</button>
                </form>
            </div>
        </div>
        
        {{-- Staff Preview Modal --}}
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
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[80vh] overflow-hidden flex flex-col">
                <div class="bg-indigo-600 px-6 py-4 flex items-center justify-between shrink-0">
                    <div>
                         <h3 class="font-bold text-white text-lg">Staff Preview</h3>
                         <p class="text-white/60 text-xs" x-text="activeOfficeName"></p>
                    </div>
                    <button @click="staffModalOpen = false" class="text-white/70 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div class="overflow-y-auto flex-1 p-0">
                    <div x-show="loading" class="flex flex-col items-center justify-center py-12 text-gray-400">
                        <svg class="animate-spin h-8 w-8 text-indigo-600 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                         <p class="text-sm font-medium">Loading...</p>
                    </div>
                    <div x-show="!loading && staffList.length === 0" class="flex flex-col items-center justify-center py-12 text-gray-400">
                        <p class="text-sm">No employees found.</p>
                        <p class="text-xs mt-1">Check if the UUID is correct.</p>
                    </div>
                    <ul x-show="!loading && staffList.length > 0" class="divide-y divide-gray-100">
                         <template x-for="emp in staffList" :key="emp.id || emp.uuid">
                            <li class="px-6 py-2 hover:bg-gray-50 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs shrink-0">
                                    <span x-text="(emp.first_name || emp.name || '?').charAt(0)"></span>
                                </div>
                                <p class="text-sm font-medium text-gray-900" 
                                   x-text="`${emp.first_name || emp.name || ''} ${emp.last_name || emp.surname || ''}`"></p>
                            </li>
                         </template>
                    </ul>
                </div>
            </div>
        </div>
        
    </div>
</x-app-layout>
