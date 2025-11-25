@extends('admin.layout')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Kullanıcı Yönetimi</h1>
                        <p class="mt-2 text-sm text-gray-600">Sistemdeki tüm kullanıcıları yönetin</p>
                    </div>
                    <button onclick="openCreateModal()"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Yeni Kullanıcı
                    </button>
                </div>
            </div>

            <!-- Users Table Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    ID</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Kullanıcı</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Rol</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Kayıt Tarihi</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($users as $user)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-gray-900">#{{ $user->id }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $roleValue = is_string($user->role) ? $user->role : $user->role->value;
                                            $roleConfig = [
                                                'admin' => ['label' => 'Admin', 'color' => 'red'],
                                                'birim_amiri' => ['label' => 'Birim Amiri', 'color' => 'purple'],
                                                'alan_amiri' => ['label' => 'Alan Amiri', 'color' => 'indigo'],
                                                'isg_uzmani' => ['label' => 'İSG Uzmanı', 'color' => 'blue'],
                                                'isveren_vekili' => ['label' => 'İşveren Vekili', 'color' => 'green'],
                                                'calisan' => ['label' => 'Çalışan', 'color' => 'gray'],
                                            ];
                                            $config = $roleConfig[$roleValue] ?? ['label' => $roleValue, 'color' => 'gray'];
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $config['color'] }}-100 text-{{ $config['color'] }}-800 border border-{{ $config['color'] }}-300">
                                            {{ $config['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $user->created_at->format('d.m.Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button onclick="openEditModal({{ $user->id }})"
                                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white rounded-lg transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-lg">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Düzenle
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div id="createModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeCreateModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <form method="POST" action="{{ route('admin.users.create') }}">
                    @csrf
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-2xl font-bold text-white flex items-center">
                                <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                Yeni Kullanıcı Oluştur
                            </h3>
                            <button type="button" onclick="closeCreateModal()"
                                class="text-white hover:text-gray-200 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="px-6 py-6 space-y-5">
                        <!-- Name Input -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">İsim</label>
                            <input type="text" name="name" id="name" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 outline-none"
                                placeholder="Kullanıcı adını girin">
                        </div>

                        <!-- Email Input -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" id="email" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 outline-none"
                                placeholder="ornek@email.com">
                        </div>

                        <!-- Password Input -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Şifre</label>
                                <input type="password" name="password" id="password" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 outline-none"
                                    placeholder="••••••••">
                            </div>
                            <div>
                                <label for="password_confirmation"
                                    class="block text-sm font-semibold text-gray-700 mb-2">Şifre Tekrar</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 outline-none"
                                    placeholder="••••••••">
                            </div>
                        </div>

                        <!-- Rol Seçimi -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Rol Seçin</label>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach (\App\Enums\Role::cases() as $role)
                                    <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-all duration-200 group">
                                        <input type="radio" name="role" value="{{ $role->value }}" required
                                            class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500"
                                            {{ $loop->index === 5 ? 'checked' : '' }}>
                                        <span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-blue-600">
                                            @switch($role->value)
                                                @case('admin') <span class="font-semibold">🔐 Admin</span> @break
                                                @case('birim_amiri') 👔 Birim Amiri @break
                                                @case('alan_amiri') 📋 Alan Amiri @break
                                                @case('isg_uzmani') 🛡️ İSG Uzmanı @break
                                                @case('isveren_vekili') 💼 İşveren Vekili @break
                                                @case('calisan') 👤 Çalışan @break
                                            @endswitch
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-6 py-4 flex items-center justify-end space-x-3 border-t border-gray-200">
                        <button type="button" onclick="closeCreateModal()"
                            class="px-6 py-2.5 bg-white border-2 border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-all duration-200">
                            İptal
                        </button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                            Oluştur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeEditModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <form method="POST" id="editForm">
                    @csrf
                    @method('PUT')
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-2xl font-bold text-white flex items-center">
                                <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Kullanıcıyı Düzenle
                            </h3>
                            <button type="button" onclick="closeEditModal()"
                                class="text-white hover:text-gray-200 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="px-6 py-6 space-y-5">
                        <!-- Edit form fields -->
                        <div>
                            <label for="edit_name" class="block text-sm font-semibold text-gray-700 mb-2">İsim</label>
                            <input type="text" name="name" id="edit_name" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none">
                        </div>

                        <div>
                            <label for="edit_email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" id="edit_email" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="edit_password" class="block text-sm font-semibold text-gray-700 mb-2">Yeni
                                    Şifre <span class="text-gray-500 text-xs">(boş bırakılabilir)</span></label>
                                <input type="password" name="password" id="edit_password"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none">
                            </div>
                            <div>
                                <label for="edit_password_confirmation"
                                    class="block text-sm font-semibold text-gray-700 mb-2">Şifre Tekrar</label>
                                <input type="password" name="password_confirmation" id="edit_password_confirmation"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 outline-none">
                            </div>
                        </div>

                        <!-- Rol Seçimi (Edit) -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Rol Seçin</label>
                            <div class="grid grid-cols-2 gap-3" id="editRoles">
                                @foreach (\App\Enums\Role::cases() as $role)
                                    <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-500 hover:bg-indigo-50 transition-all duration-200 group">
                                        <input type="radio" name="role" value="{{ $role->value }}" required
                                            class="w-5 h-5 text-indigo-600 border-gray-300 focus:ring-indigo-500 edit-role-radio">
                                        <span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-indigo-600">
                                            @switch($role->value)
                                                @case('admin') <span class="font-semibold">🔐 Admin</span> @break
                                                @case('birim_amiri') 👔 Birim Amiri @break
                                                @case('alan_amiri') 📋 Alan Amiri @break
                                                @case('isg_uzmani') 🛡️ İSG Uzmanı @break
                                                @case('isveren_vekili') 💼 İşveren Vekili @break
                                                @case('calisan') 👤 Çalışan @break
                                            @endswitch
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex items-center justify-end space-x-3 border-t border-gray-200">
                        <button type="button" onclick="closeEditModal()"
                            class="px-6 py-2.5 bg-white border-2 border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-all duration-200">
                            İptal
                        </button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-medium rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                            Güncelle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openEditModal(userId) {
            document.getElementById('editModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            // Form action'ını güncelle
            const editForm = document.getElementById('editForm');
            editForm.action = `/admin/users/${userId}`;

            // AJAX ile kullanıcı bilgilerini çek ve forma doldur
            fetch(`/admin/users/${userId}/edit`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('edit_name').value = data.user.name;
                    document.getElementById('edit_email').value = data.user.email;

                    // Rolü işaretle
                    const radioButtons = document.querySelectorAll('.edit-role-radio');
                    radioButtons.forEach(radio => {
                        radio.checked = (radio.value === data.user.role);
                    });
                })
                .catch(error => console.error('Error:', error));
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // ESC tuşu ile modal kapatma
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeCreateModal();
                closeEditModal();
            }
        });
    </script>
@endsection
