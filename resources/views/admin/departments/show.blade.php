@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $department->name }}</h1>
                <p class="text-gray-600">{{ $department->company->name }} şirketi</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('departments.edit', $department) }}"
                   class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                    Düzenle
                </a>
                <a href="{{ route('admin.work-permits.create-with-department', $department) }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Yeni İş İzni
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Sol Kolon - İstatistikler -->
            <div class="space-y-6">
                <!-- Temel Bilgiler -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Departman Bilgileri</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Şirket:</span>
                            <span class="font-medium">{{ $department->company->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Durum:</span>
                            <span class="{{ $department->is_active ? 'text-green-600' : 'text-red-600' }}">
                                {{ $department->is_active ? 'Aktif' : 'Pasif' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Oluşturulma:</span>
                            <span class="font-medium">{{ $department->created_at->format('d.m.Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Hızlı İstatistikler -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">İstatistikler</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Toplam İş İzni:</span>
                            <span class="font-medium">{{ $department->workPermits->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Form Şablonu:</span>
                            <span class="font-medium">{{ $department->formTemplates->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Onay Adımları:</span>
                            <span class="font-medium">{{ count($department->approval_workflow ?? []) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sağ Kolon - Son İş İzinleri -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Son İş İzinleri</h3>
                    </div>
                    <div class="overflow-x-auto">
                        @if($department->workPermits->count() > 0)
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Başlık</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($department->workPermits as $workPermit)
                                    <tr class="hover:bg-gray-50 cursor-pointer"
                                        onclick="window.location='{{ route('admin.work-permits.show', $workPermit) }}'">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            #{{ $workPermit->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ Str::limit($workPermit->title, 40) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $workPermit->status === 'approved' ? 'bg-green-100 text-green-800' :
                                                   ($workPermit->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ $workPermit->status_text }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $workPermit->created_at->format('d.m.Y') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-clipboard-list text-gray-400 text-4xl mb-3"></i>
                                <p class="text-gray-600">Henüz iş izni bulunmuyor</p>
                                <a href="{{ route('work-permits.create-with-department', $department) }}"
                                   class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                    İlk iş iznini oluşturun
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
