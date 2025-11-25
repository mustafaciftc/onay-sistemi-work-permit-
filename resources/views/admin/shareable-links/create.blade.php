@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Yeni Paylaşım Linki Oluştur</h1>
                    <p class="text-gray-600">{{ $workPermit->title }}</p>
                </div>
                <a href="{{ route('admin.work-permits.shareable-links.index', $workPermit) }}"
                    class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    ← Geri Dön
                </a>
            </div>

            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-6 py-6">
                    <form action="{{ route('admin.work-permits.shareable-links.store', $workPermit) }}" method="POST">
                        @csrf

                        <div class="space-y-6">
                            <!-- Son Kullanım Tarihi -->
                            <div>
                                <label for="expires_at" class="block text-sm font-medium text-gray-700">
                                    Son Kullanım Tarihi (Opsiyonel)
                                </label>
                                <div class="mt-1">
                                    <input type="datetime-local" name="expires_at" id="expires_at"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        value="{{ old('expires_at') }}">
                                </div>
                                <p class="mt-2 text-sm text-gray-500">
                                    Belirtilmezse link hiçbir zaman süresi dolmaz.
                                </p>
                            </div>

                            <!-- Şifre Koruması (Opsiyonel) -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    Şifre Koruması (Opsiyonel)
                                </label>
                                <div class="mt-1 relative">
                                    <input type="password" name="password" id="password"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md pr-10"
                                        placeholder="Şifresiz bırakmak için boş bırakın">
                                    <button type="button" onclick="togglePasswordVisibility()"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye" id="eye-icon"></i>
                                    </button>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">
                                    Linki açmak için şifre istensin. Boş bırakılırsa şifresiz olur.
                                </p>
                            </div>

                            <!-- Maksimum Görüntülenme Sayısı -->
                            <div>
                                <label for="max_views" class="block text-sm font-medium text-gray-700">
                                    Maksimum Görüntülenme Sayısı (Opsiyonel)
                                </label>
                                <div class="mt-1">
                                    <input type="number" name="max_views" id="max_views" min="1"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        value="{{ old('max_views') }}" placeholder="Örn: 50">
                                </div>
                                <p class="mt-2 text-sm text-gray-500">
                                    Belirtilmezse sınırsız görüntüleme olur.
                                </p>
                            </div>

                            <!-- Link Aktif mi? -->
                            <div class="flex items-center">
                                <input id="is_active" name="is_active" type="checkbox"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" checked>
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Link oluşturulsun ve hemen aktif olsun
                                </label>
                            </div>

                            <div class="pt-5">
                                <div class="flex justify-end space-x-3">
                                    <a href="{{ route('admin.work-permits.shareable-links.index', $workPermit) }}"
                                        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        İptal
                                    </a>
                                    <button type="submit"
                                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 font-medium">
                                        Linki Oluştur
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bilgilendirme Kartı -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="shrink-0">
                        <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Nasıl çalışır?</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Oluşturulan link, sadece bu iş iznini görüntüleme izni verir.</li>
                                <li>Link pasif hale getirilirse kimse erişemez.</li>
                                <li>Süre dolarsa veya maksimum görüntüleme sayısına ulaşılırsa otomatik devre dışı kalır.
                                </li>
                                <li>Her zaman listeleme sayfasından kontrol edebilir, silebilir veya durumunu
                                    değiştirebilirsiniz.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mevcut toast fonksiyonunu da ekleyelim (index sayfasında da var, tutarlı olsun) -->
    <script>
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-4 right-4 px-4 py-2 rounded text-white z-50 ${
                type === 'success' ? 'bg-green-500' : 'bg-blue-500'
            }`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 3000);
        }

        @if (session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                showToast('{{ session('success') }}', 'success');
            });
        @endif

        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                showToast('Lütfen formu kontrol edin.', 'error');
            });
        @endif
    </script>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
@endsection
