@extends('layouts.app')
@section('title', 'Birim Amiri Dashboard')

@section('content')
    <div class="w-full px-4 md:px-8 py-6">

        <!-- Başlık -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">
                Hoş Geldiniz, {{ Auth::user()->name }}
            </h1>
            <span class="px-3 py-1 rounded-full bg-blue-600 text-white text-sm">
                Birim Amiri
            </span>
        </div>

        <!-- İstatistik Kartları -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Bekleyen Onaylar -->
            <div class="border-l-4 border-yellow-500 shadow bg-white rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs font-semibold text-yellow-600 uppercase">Bekleyen Onaylar</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['pending_approvals'] ?? 0 }}</p>
                    </div>
                    <i class="fas fa-clock text-gray-300 text-3xl"></i>
                </div>
            </div>

            <!-- Onaylanan İzinler -->
            <div class="border-l-4 border-green-500 shadow bg-white rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs font-semibold text-green-600 uppercase">Onaylanan İzinler</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['approved_permits'] ?? 0 }}</p>
                    </div>
                    <i class="fas fa-check-circle text-gray-300 text-3xl"></i>
                </div>
            </div>

            <!-- Toplam İzinler -->
            <div class="border-l-4 border-blue-400 shadow bg-white rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs font-semibold text-blue-500 uppercase">Toplam İzinler</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_permits'] ?? 0 }}</p>
                    </div>
                    <i class="fas fa-clipboard-list text-gray-300 text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Bekleyen Onaylar Tablosu -->
        <div class="bg-white shadow rounded-lg mt-8">
            <div class="border-b px-4 py-3">
                <h6 class="font-semibold text-blue-600">Onay Bekleyen İş İzinleri</h6>
            </div>

            <div class="p-4">
                @if ($pendingApprovals->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full border text-sm">
                            <thead class="bg-gray-100 text-gray-700">
                                <tr>
                                    <th class="px-3 py-2 border">İzin Kodu</th>
                                    <th class="px-3 py-2 border">Başlık</th>
                                    <th class="px-3 py-2 border">Lokasyon</th>
                                    <th class="px-3 py-2 border">Oluşturan</th>
                                    <th class="px-3 py-2 border">Durum</th>
                                    <th class="px-3 py-2 border text-center">İşlemler</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($pendingApprovals as $permit)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">{{ $permit->permit_code }}</td>
                                        <td class="px-4 py-3">{{ $permit->title }}</td>
                                        <td class="px-4 py-3">{{ $permit->location }}</td>
                                        <td class="px-4 py-3">{{ $permit->creator->name ?? '—' }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                                Birim Onayı Bekliyor
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <form action="{{ route('company.work-permits.quick-approve', $permit) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="quick_action" value="approve">
                                                <button type="submit" class="text-green-600 hover:text-green-800 font-medium text-sm">
                                                    Onayla
                                                </button>
                                            </form>

                                            <span class="mx-2 text-gray-300">|</span>

                                            <form action="{{ route('company.work-permits.quick-approve', $permit) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="quick_action" value="reject">
                                                <button type="submit" onclick="return confirm('Bu iş iznini reddetmek istediğinizden emin misiniz?')" class="text-red-600 hover:text-red-800 font-medium text-sm">
                                                    Reddet
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-check-circle text-green-500 text-4xl mb-3"></i>
                        <p class="text-gray-500">Onay bekleyen iş izni bulunmamaktadır.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
