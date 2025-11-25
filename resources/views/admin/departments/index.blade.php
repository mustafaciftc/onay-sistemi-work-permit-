@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Departmanlar</h1>
                <p class="text-gray-600">{{ $company->name }} şirketine ait departmanlar</p>
            </div>
            <a href="{{ route('company.departments.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                Yeni Departman
            </a>
        </div>

        @if($departments->count() > 0)
            <!-- Departments Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($departments as $department)
                <div class="bg-white rounded-lg shadow border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $department->name }}</h3>
                            @if(!$department->is_active)
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">
                                    Pasif
                                </span>
                            @endif
                        </div>

                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Şablon Sayısı:</span>
                                <span class="font-medium">{{ $department->form_templates_count }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">İş İzni Sayısı:</span>
                                <span class="font-medium">{{ $department->work_permits_count }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Onay Adımları:</span>
                                <span class="font-medium">{{ count($department->approval_workflow ?? []) }}</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                            <div class="flex space-x-2">
                                <a href="{{ route('company.departments.show', $department) }}"
                                   class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    Detay
                                </a>
                                <a href="{{ route('company.departments.edit', $department) }}"
                                   class="text-green-600 hover:text-green-900 text-sm font-medium">
                                    Düzenle
                                </a>
                            </div>
                            <a href="{{ route('admin.work-permits.create-with-department', $department) }}"
                               class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                                İş İzni Aç
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12 bg-white rounded-lg shadow">
                <i class="fas fa-building text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Henüz departmanınız yok</h3>
                <p class="text-gray-600 mb-4">İş izinlerinizi departmanlara göre yönetmek için departman oluşturun.</p>
                <a href="{{ route('company.departments.create') }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    İlk Departmanı Oluştur
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
