@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Yeni Departman</h1>
            <p class="text-gray-600">{{ $company->name }} şirketi için departman oluşturun</p>
        </div>

        <form action="{{ route('departments.store') }}" method="POST">
            @csrf

            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Temel Bilgiler</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Departman Adı</label>
                        <input type="text" name="name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Örn: Elektrik Bakım, Mekanik İşler">
                    </div>
                </div>
            </div>

            <!-- Onay Workflow Bölümü - Basit versiyon, sonra geliştireceğiz -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Onay Süreci</h3>
                <p class="text-gray-600 mb-4">Varsayılan onay süreci kullanılacak. Daha sonra düzenleyebilirsiniz.</p>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-2">Varsayılan Onay Akışı:</h4>
                    <ol class="list-decimal list-inside space-y-1 text-sm text-gray-600">
                        <li>Birim Amiri - İş izni açılışı</li>
                        <li>Alan Amiri - Alan uygunluk onayı</li>
                        <li>İSG Uzmanı - Teknik güvenlik onayı</li>
                    </ol>
                </div>
            </div>

            <!-- Butonlar -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('departments.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    İptal
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Departmanı Oluştur
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
