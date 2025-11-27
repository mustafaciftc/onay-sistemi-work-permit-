@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="space-y-8">

        <!-- Sayfa Başlığı -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Admin Paneli</h1>
                <p class="text-gray-600 mt-1">Sistemin genel durumu ve hızlı erişim noktaları</p>
            </div>
            <div class="text-sm text-gray-500">
                {{ now()->format('d F Y, l') }} <!-- 26 Kasım 2025, Çarşamba -->
            </div>
        </div>

        <!-- İstatistik Kartları -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            <!-- Toplam Kullanıcı -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Toplam Kullanıcı</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalUsers }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-users text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            <!-- Toplam Şirket -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Toplam Şirket</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalCompanies }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-building text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>

            <!-- Aktif Departman (YENİ!) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Aktif Departman</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">
                            {{ \App\Models\Department::where('is_active', true)->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-indigo-100 rounded-full">
                        <i class="fas fa-sitemap text-2xl text-indigo-600"></i>
                    </div>
                </div>
            </div>

            <!-- Toplam İş İzni -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Toplam İş İzni</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalWorkPermits }}</p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i class="fas fa-clipboard-check text-2xl text-yellow-600"></i>
                    </div>
                </div>
            </div>

            <!-- Bekleyen Onaylar -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Bekleyen Onay</p>
                        <p class="text-3xl font-bold text-red-600 mt-2">{{ $pendingApprovals }}</p>
                        @if($pendingApprovals > 0)
                            <span class="text-xs text-red-600 font-medium">Acil dikkat!</span>
                        @endif
                    </div>
                    <div class="p-3 bg-red-100 rounded-full">
                        <i class="fas fa-clock text-2xl text-red-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hızlı İşlemler -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-5 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-bolt mr-2 text-yellow-500"></i>
                    Hızlı İşlemler
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">

                    <!-- Kullanıcılar -->
                    <a href="{{ route('admin.users.index') }}"
                        class="group bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-6 text-center hover:from-blue-100 hover:to-blue-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                        <i class="fas fa-users text-4xl text-blue-600 mb-3 group-hover:scale-110 transition-transform"></i>
                        <h3 class="font-bold text-blue-900 text-lg">Kullanıcılar</h3>
                        <p class="text-sm text-blue-700 mt-1">Tüm kullanıcıları yönet</p>
                    </a>

                    <!-- Şirketler -->
                    <a href="{{ route('admin.companies.index') }}"
                        class="group bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-6 text-center hover:from-green-100 hover:to-green-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                        <i class="fas fa-building text-4xl text-green-600 mb-3 group-hover:scale-110 transition-transform"></i>
                        <h3 class="font-bold text-green-900 text-lg">Şirketler</h3>
                        <p class="text-sm text-green-700 mt-1">Şirketleri görüntüle ve düzenle</p>
                    </a>

                    <!-- Departmanlar (YENİ!) -->
                    <a href="{{ route('admin.departments.index') }}"
                        class="group bg-gradient-to-br from-indigo-50 to-indigo-100 border border-indigo-200 rounded-xl p-6 text-center hover:from-indigo-100 hover:to-indigo-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                        <i class="fas fa-sitemap text-4xl text-indigo-600 mb-3 group-hover:scale-110 transition-transform"></i>
                        <h3 class="font-bold text-indigo-900 text-lg">Departmanlar</h3>
                        <p class="text-sm text-indigo-700 mt-1">Departman yapılarını yönet</p>
                    </a>

                    <!-- İş İzinleri -->
                    <a href="{{ route('admin.work-permits.index') }}"
                        class="group bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl p-6 text-center hover:from-purple-100 hover:to-purple-200 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                        <i class="fas fa-clipboard-list text-4xl text-purple-600 mb-3 group-hover:scale-110 transition-transform"></i>
                        <h3 class="font-bold text-purple-900 text-lg">İş İzinleri</h3>
                        <p class="text-sm text-purple-700 mt-1">Tüm izinleri yönet ve yeni oluştur</p>
                    </a>

                </div>
            </div>
        </div>

        <!-- Ek Bilgi (Opsiyonel) -->
        <div class="text-center text-sm text-gray-500">
            <p>© 2025 Onay Sistemi • v2.4</p>
        </div>
    </div>
@endsection
