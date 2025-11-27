@extends('layouts.app')
@section('title', 'Çalışan Dashboard')

@section('content')
<div class="w-full px-4 md:px-8 py-6">

    <!-- Başlık -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            Hoş Geldiniz, {{ Auth::user()->name }}
        </h1>
        <span class="px-3 py-1 rounded-full bg-gray-700 text-white text-sm">
            Çalışan
        </span>
    </div>

    <!-- İstatistik Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Toplam İzinlerim -->
        <div class="border-l-4 border-blue-500 shadow bg-white rounded-lg p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold text-blue-600 uppercase">Toplam İzinlerim</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['my_permits'] ?? 0 }}</p>
                </div>
                <i class="fas fa-clipboard-list text-gray-300 text-3xl"></i>
            </div>
        </div>

        <!-- Onay Bekleyen -->
        <div class="border-l-4 border-yellow-500 shadow bg-white rounded-lg p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold text-yellow-600 uppercase">Onay Bekleyen</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['pending_permits'] ?? 0 }}</p>
                </div>
                <i class="fas fa-clock text-gray-300 text-3xl"></i>
            </div>
        </div>

        <!-- Onaylanan -->
        <div class="border-l-4 border-green-500 shadow bg-white rounded-lg p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold text-green-600 uppercase">Onaylanan İzinler</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['approved_permits'] ?? 0 }}</p>
                </div>
                <i class="fas fa-check-circle text-gray-300 text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- İş İzinlerim Tablosu -->
    <div class="bg-white shadow rounded-lg mt-8">
        <div class="border-b px-4 py-3 flex justify-between items-center">
            <h6 class="font-semibold text-blue-600">İş İzinlerim</h6>

            <a href="{{ route('company.work-permits.create') }}"
               class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                <i class="fas fa-plus"></i> Yeni İş İzni
            </a>
        </div>

        <div class="p-4">
            @if ($myWorkPermits->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full border text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-3 py-2 border">İzin Kodu</th>
                                <th class="px-3 py-2 border">Başlık</th>
                                <th class="px-3 py-2 border">Departman</th>
                                <th class="px-3 py-2 border">Durum</th>
                                <th class="px-3 py-2 border">Oluşturulma</th>
                                <th class="px-3 py-2 border text-center">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($myWorkPermits as $permit)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $permit->permit_code }}</td>
                                    <td class="px-4 py-3">{{ $permit->title }}</td>
                                    <td class="px-4 py-3">{{ $permit->department->name ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        @if($permit->status === 'approved')
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Onaylandı</span>
                                        @elseif($permit->status === 'rejected')
                                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Reddedildi</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Onay Bekliyor</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $permit->created_at->format('d.m.Y H:i') }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('company.work-permits.show', $permit) }}"
                                           class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                            Detay
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-file-alt text-gray-400 text-4xl mb-3"></i>
                    <p class="text-gray-500 mb-3">Henüz iş izni oluşturmadınız.</p>

                    <a href="{{ route('company.work-permits.create') }}"
                       class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        <i class="fas fa-plus"></i> İlk İş İzninizi Oluşturun
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
