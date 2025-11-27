@extends('admin.layout')

@section('content')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Departman ve Pozisyon Yönetimi</h1>
                        <p class="mt-1 text-sm text-gray-600">Şirketlere özel departman ve pozisyon yapılarını yönetin</p>
                    </div>
                    <div class="flex gap-3">
                        <button onclick="openCreateDepartmentModal()"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Yeni Departman
                        </button>
                    </div>
                </div>
            </div>

            <!-- Departmanlar Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6" id="departmentsGrid">
                @forelse($departments as $department)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden department-card"
                        data-department-id="{{ $department->id }}">
                        <!-- Departman Header -->
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $department->name }}</h3>
                                    <p class="text-sm text-gray-600">
                                        {{ $department->company->name ?? 'Şirket bulunamadı' }}
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $department->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $department->is_active ? 'Aktif' : 'Pasif' }}
                                    </span>
                                    <div class="relative">
                                        <button onclick="toggleDepartmentMenu({{ $department->id }})"
                                            class="p-1 text-gray-400 hover:text-gray-600 rounded">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                            </svg>
                                        </button>
                                        <!-- Dropdown Menu -->
                                        <div id="departmentMenu-{{ $department->id }}"
                                            class="hidden absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50">
                                            <div class="py-1">
                                                <button onclick="openEditDepartmentModal({{ $department->id }})"
                                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Düzenle
                                                </button>
                                                <button onclick="toggleDepartmentStatus({{ $department->id }})"
                                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-left">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    {{ $department->is_active ? 'Pasif Yap' : 'Aktif Yap' }}
                                                </button>
                                                <button
                                                    onclick="deleteDepartment({{ $department->id }}, '{{ addslashes($department->name) }}')"
                                                    class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 w-full text-left">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Sil
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if ($department->description)
                                <p class="text-sm text-gray-500 mt-2">{{ $department->description }}</p>
                            @endif
                        </div>

                        <!-- Pozisyonlar Listesi -->
                        <div class="p-4">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="text-sm font-medium text-gray-700">Pozisyonlar</h4>
                                <button onclick="openCreatePositionModal({{ $department->id }})"
                                    class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 rounded transition-colors">
                                    + Yeni Pozisyon
                                </button>
                            </div>
                            <div class="space-y-2" id="positions-{{ $department->id }}">
                                @forelse($department->positions as $position)
                                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded border"
                                        data-position-id="{{ $position->id }}">
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-900">{{ $position->name }}</span>
                                            <span
                                                class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs {{ $position->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $position->is_active ? 'Aktif' : 'Pasif' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center space-x-1">
                                            <button onclick="editPosition({{ $position->id }})"
                                                class="p-1 text-gray-400 hover:text-blue-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button onclick="deletePosition({{ $position->id }}, '{{ addslashes($position->name) }}')"
                                                class="p-1 text-gray-400 hover:text-red-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500 text-center py-2">Henüz pozisyon bulunmuyor</p>
                                @endforelse
                            </div>
                        </div>

                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m8-8V4a1 1 0 00-1-1h-2a1 1 0 00-1 1v1M9 7h6" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Departman bulunamadı</h3>
                        <p class="mt-1 text-sm text-gray-500">Henüz hiç departman oluşturulmamış.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($departments->hasPages())
                <div class="mt-6">
                    {{ $departments->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Create Department Modal -->
    <div id="createDepartmentModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                onclick="closeModal('createDepartmentModal')"></div>
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Yeni Departman</h3>
                        <form id="createDepartmentForm" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label for="create_company_id" class="block text-sm font-medium text-gray-700 mb-1">Şirket *</label>
                                    <select id="create_company_id" name="company_id" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Şirket seçin</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="create_department_name" class="block text-sm font-medium text-gray-700 mb-1">Departman Adı *</label>
                                    <input type="text" id="create_department_name" name="name" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Departman adını girin">
                                </div>
                                <div>
                                    <label for="create_department_description" class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                                    <textarea id="create_department_description" name="description" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Departman açıklaması (opsiyonel)"></textarea>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="create_department_is_active" name="is_active" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="create_department_is_active" class="ml-2 block text-sm text-gray-700">Aktif departman</label>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end space-x-3">
                                <button type="button" onclick="closeModal('createDepartmentModal')" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    İptal
                                </button>
                                <button type="submit" id="createDepartmentSubmitBtn" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Oluştur
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Department Modal -->
    <div id="editDepartmentModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                onclick="closeModal('editDepartmentModal')"></div>
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Departmanı Düzenle</h3>
                        <form id="editDepartmentForm" class="space-y-4">
                            @csrf
                            <input type="hidden" id="edit_department_id" name="department_id">
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label for="edit_department_company_id" class="block text-sm font-medium text-gray-700 mb-1">Şirket *</label>
                                    <select id="edit_department_company_id" name="company_id" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Şirket seçin</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="edit_department_name" class="block text-sm font-medium text-gray-700 mb-1">Departman Adı *</label>
                                    <input type="text" id="edit_department_name" name="name" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label for="edit_department_description" class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                                    <textarea id="edit_department_description" name="description" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="edit_department_is_active" name="is_active" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="edit_department_is_active" class="ml-2 block text-sm text-gray-700">Aktif departman</label>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end space-x-3">
                                <button type="button" onclick="closeModal('editDepartmentModal')" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    İptal
                                </button>
                                <button type="submit" id="editDepartmentSubmitBtn" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Güncelle
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Position Modal -->
    <div id="createPositionModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                onclick="closeModal('createPositionModal')"></div>
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Yeni Pozisyon</h3>
                        <form id="createPositionForm" class="space-y-4">
                            @csrf
                            <input type="hidden" id="create_position_department_id" name="department_id">
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label for="create_position_name" class="block text-sm font-medium text-gray-700 mb-1">Pozisyon Adı *</label>
                                    <input type="text" id="create_position_name" name="name" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Pozisyon adını girin">
                                </div>
                                <div>
                                    <label for="create_position_description" class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                                    <textarea id="create_position_description" name="description" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Pozisyon açıklaması (opsiyonel)"></textarea>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="create_position_is_active" name="is_active" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="create_position_is_active" class="ml-2 block text-sm text-gray-700">Aktif pozisyon</label>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end space-x-3">
                                <button type="button" onclick="closeModal('createPositionModal')" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    İptal
                                </button>
                                <button type="submit" id="createPositionSubmitBtn" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Oluştur
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Position Modal -->
    <div id="editPositionModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                onclick="closeModal('editPositionModal')"></div>
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Pozisyonu Düzenle</h3>
                        <form id="editPositionForm" class="space-y-4">
                            @csrf
                            <input type="hidden" id="edit_position_id" name="position_id">
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label for="edit_position_name" class="block text-sm font-medium text-gray-700 mb-1">Pozisyon Adı *</label>
                                    <input type="text" id="edit_position_name" name="name" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label for="edit_position_description" class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                                    <textarea id="edit_position_description" name="description" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="edit_position_is_active" name="is_active" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="edit_position_is_active" class="ml-2 block text-sm text-gray-700">Aktif pozisyon</label>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end space-x-3">
                                <button type="button" onclick="closeModal('editPositionModal')" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    İptal
                                </button>
                                <button type="submit" id="editPositionSubmitBtn" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Güncelle
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // CSRF Token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Toast
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-[100] px-6 py-4 rounded-lg shadow-lg text-white font-medium transition-all duration-300 transform translate-x-full`;
            toast.innerHTML = `
                <div class="flex items-center gap-3">
                    ${type === 'success' ? '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' : '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>'}
                    <span>${message}</span>
                </div>
            `;
            toast.style.backgroundColor = type === 'success' ? '#10b981' : '#ef4444';
            document.body.appendChild(toast);
            setTimeout(() => toast.classList.remove('translate-x-full'), 100);
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 400);
            }, 4000);
        }

        // Modal functions
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }

        // Dışarı tıklayınca kapan
        document.querySelectorAll('[id$="Modal"]').forEach(modal => {
            modal.addEventListener('click', e => {
                if (e.target === modal) closeModal(modal.id);
            });
        });

        // ESC ile kapan
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                document.querySelectorAll('[id$="Modal"]').forEach(m => closeModal(m.id));
                document.querySelectorAll('[id^="departmentMenu-"]').forEach(menu => menu.classList.add('hidden'));
            }
        });

        // Dropdown
        function toggleDepartmentMenu(id) {
            document.querySelectorAll('[id^="departmentMenu-"]').forEach(m => m.classList.add('hidden'));
            const menu = document.getElementById(`departmentMenu-${id}`);
            if (menu) menu.classList.toggle('hidden');
        }

        document.addEventListener('click', e => {
            if (!e.target.closest('.relative')) {
                document.querySelectorAll('[id^="departmentMenu-"]').forEach(m => m.classList.add('hidden'));
            }
        });

        // Departman İşlemleri
        function openCreateDepartmentModal() {
            document.getElementById('createDepartmentForm').reset();
            document.getElementById('create_department_is_active').checked = true;
            openModal('createDepartmentModal');
        }

        function openEditDepartmentModal(id) {
            fetch(`/admin/departments/${id}/edit`, { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    const d = data.department;
                    document.getElementById('edit_department_id').value = d.id;
                    document.getElementById('edit_department_company_id').value = d.company_id;
                    document.getElementById('edit_department_name').value = d.name;
                    document.getElementById('edit_department_description').value = d.description || '';
                    document.getElementById('edit_department_is_active').checked = d.is_active;
                    openModal('editDepartmentModal');
                })
                .catch(() => showToast('Yükleme hatası', 'error'));
        }

        function deleteDepartment(id, name) {
            if (!confirm(`"${name}" silinsin mi?`)) return;
            fetch(`/admin/departments/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showToast('Silindi');
                        document.querySelector(`.department-card[data-department-id="${id}"]`)?.remove();
                    } else showToast(data.message, 'error');
                });
        }

        function toggleDepartmentStatus(id) {
            fetch(`/admin/departments/${id}/toggle`, {
                method: 'PATCH',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            })
                .then(() => location.reload());
        }

        // Pozisyon İşlemleri
        function openCreatePositionModal(deptId) {
            document.getElementById('createPositionForm').reset();
            document.getElementById('create_position_department_id').value = deptId;
            document.getElementById('create_position_is_active').checked = true;
            openModal('createPositionModal');
        }

        function editPosition(id) {
            fetch(`/admin/positions/${id}/edit`, { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    const p = data.position;
                    document.getElementById('edit_position_id').value = p.id;
                    document.getElementById('edit_position_name').value = p.name;
                    document.getElementById('edit_position_description').value = p.description || '';
                    document.getElementById('edit_position_is_active').checked = p.is_active;
                    openModal('editPositionModal');
                })
                .catch(() => showToast('Yükleme hatası', 'error'));
        }

        function deletePosition(id, name) {
            if (!confirm(`"${name}" silinsin mi?`)) return;
            fetch(`/admin/positions/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showToast('Silindi');
                        document.querySelector(`[data-position-id="${id}"]`)?.remove();
                    } else showToast(data.message, 'error');
                });
        }

        // Form Submissions - EKSİK OLANLAR EKLENDİ
        document.getElementById('createDepartmentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('createDepartmentSubmitBtn');
            const orig = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = 'Oluşturuluyor...';

            const payload = {
                company_id: document.getElementById('create_company_id').value,
                name: document.getElementById('create_department_name').value.trim(),
                description: document.getElementById('create_department_description').value,
                is_active: document.getElementById('create_department_is_active').checked
            };

            fetch("{{ route('admin.departments.store') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showToast('Oluşturuldu');
                        closeModal('createDepartmentModal');
                        setTimeout(() => location.reload(), 800);
                    } else throw data;
                })
                .catch(err => showToast(Object.values(err.errors || {}).flat().join(', ') || err.message || 'Hata', 'error'))
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = orig;
                });
        });

        document.getElementById('editDepartmentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('edit_department_id').value;
            const btn = document.getElementById('editDepartmentSubmitBtn');
            const orig = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = 'Güncelleniyor...';

            const payload = {
                company_id: document.getElementById('edit_department_company_id').value,
                name: document.getElementById('edit_department_name').value.trim(),
                description: document.getElementById('edit_department_description').value,
                is_active: document.getElementById('edit_department_is_active').checked,
                _method: 'PUT'
            };

            fetch(`/admin/departments/${id}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showToast('Güncellendi');
                        closeModal('editDepartmentModal');
                        setTimeout(() => location.reload(), 800);
                    } else throw data;
                })
                .catch(err => showToast(Object.values(err.errors || {}).flat().join(', ') || 'Hata', 'error'))
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = orig;
                });
        });

        document.getElementById('createPositionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('createPositionSubmitBtn');
            const orig = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = 'Oluşturuluyor...';

            const payload = {
                department_id: document.getElementById('create_position_department_id').value,
                name: document.getElementById('create_position_name').value.trim(),
                description: document.getElementById('create_position_description').value,
                is_active: document.getElementById('create_position_is_active').checked
            };

            fetch("{{ route('admin.positions.store') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showToast('Oluşturuldu');
                        closeModal('createPositionModal');
                        setTimeout(() => location.reload(), 800);
                    } else throw data;
                })
                .catch(err => showToast(Object.values(err.errors || {}).flat().join(', ') || 'Hata', 'error'))
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = orig;
                });
        });

        document.getElementById('editPositionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('edit_position_id').value;
            const btn = document.getElementById('editPositionSubmitBtn');
            const orig = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = 'Güncelleniyor...';

            const payload = {
                name: document.getElementById('edit_position_name').value.trim(),
                description: document.getElementById('edit_position_description').value,
                is_active: document.getElementById('edit_position_is_active').checked,
                _method: 'PUT'
            };

            fetch(`/admin/positions/${id}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showToast('Güncellendi');
                        closeModal('editPositionModal');
                        setTimeout(() => location.reload(), 800);
                    } else throw data;
                })
                .catch(err => showToast(Object.values(err.errors || {}).flat().join(', ') || 'Hata', 'error'))
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = orig;
                });
        });
    </script>
@endsection

