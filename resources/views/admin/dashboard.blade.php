@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="space-y-6">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-700">Toplam Kullanıcı</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalUsers }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-building text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-700">Toplam Şirket</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalCompanies }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-clipboard-list text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-700">Toplam İş İzni</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalWorkPermits }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-700">Bekleyen Onaylar</h3>
                        <p class="text-2xl font-bold text-gray-900">{{ $pendingApprovals }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Hızlı İşlemler</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('admin.users.index') }}"
                        class="bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg p-4 text-center transition-colors">
                        <i class="fas fa-users text-blue-600 text-2xl mb-2"></i>
                        <h3 class="font-semibold text-blue-800">Kullanıcıları Yönet</h3>
                        <p class="text-sm text-blue-600 mt-1">Sistem kullanıcılarını görüntüle ve yönet</p>
                    </a>

                    <a href="{{ route('admin.companies.index') }}"
                        class="bg-green-50 hover:bg-green-100 border border-green-200 rounded-lg p-4 text-center transition-colors">
                        <i class="fas fa-building text-green-600 text-2xl mb-2"></i>
                        <h3 class="font-semibold text-green-800">Şirketleri Yönet</h3>
                        <p class="text-sm text-green-600 mt-1">Kayıtlı şirketleri görüntüle ve yönet</p>
                    </a>

                    <a href="{{ route('admin.work-permits.index') }}"
                        class="bg-purple-50 hover:bg-purple-100 border border-purple-200 rounded-lg p-4 text-center transition-colors">
                        <i class="fas fa-clipboard-list text-purple-600 text-2xl mb-2"></i>
                        <h3 class="font-semibold text-purple-800">İş İzinlerini Yönet</h3>
                        <p class="text-sm text-purple-600 mt-1">Yeni iş izni formu oluştur</p>
                    </a>
                </div>
            </div>
        </div>

    </div>
@endsection
