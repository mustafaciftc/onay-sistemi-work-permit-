@extends('layouts.app')
@section('title', 'İSG Uzmanı Dashboard')

@section('content')
    <div class="px-6 py-6">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">
                Hoş Geldiniz, {{ Auth::user()->name }}
            </h1>
            <span class="px-3 py-1 text-sm bg-green-600 text-white rounded-full">
                İSG Uzmanı
            </span>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Açılış Onayları -->
            <div class="bg-white shadow rounded-xl border-l-4 border-yellow-500 p-5 flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold text-yellow-600 uppercase">Bekleyen Açılış Onayları</p>
                    <p class="mt-1 text-2xl font-bold text-gray-800">{{ $stats['pending_approvals'] ?? 0 }}</p>
                </div>
                <i class="fas fa-shield-alt text-gray-300 text-3xl"></i>
            </div>

            <!-- Kapatma Bekleyen -->
            <div class="bg-white shadow rounded-xl border-l-4 border-red-600 p-5 flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold text-red-600 uppercase">Kapatma Bekleyen</p>
                    <p class="mt-1 text-2xl font-bold text-gray-800">{{ $stats['pending_closing'] ?? 0 }}</p>
                </div>
                <i class="fas fa-lock text-gray-300 text-3xl"></i>
            </div>

            <!-- Toplam Kontroller -->
            <div class="bg-white shadow rounded-xl border-l-4 border-blue-600 p-5 flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold text-blue-600 uppercase">Toplam Kontroller</p>
                    <p class="mt-1 text-2xl font-bold text-gray-800">{{ $stats['total_permits'] ?? 0 }}</p>
                </div>
                <i class="fas fa-clipboard-check text-gray-300 text-3xl"></i>
            </div>
        </div>

        <!-- Table -->
        <div class="mt-10 bg-white shadow rounded-xl">
            <div class="border-b px-6 py-4">
                <h2 class="font-bold text-lg text-blue-600">Bekleyen İSG Kontrolleri</h2>
            </div>

            <div class="p-6">
                @if ($pendingApprovals->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full border rounded-lg">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">İzin Kodu</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Başlık</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Lokasyon</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">İş Türü</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Durum</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">İşlem</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200">
                                @foreach ($pendingApprovals as $permit)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-mono text-sm">{{ $permit->permit_code }}</td>
                                        <td class="px-4 py-3">
                                            <div class="font-medium">{{ Str::limit($permit->title, 25) }}</div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ Str::limit($permit->work_description, 35) }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">{{ $permit->location }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                                                {{ ucfirst($permit->work_type) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($permit->status === 'pending_safety_approval')
                                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 border border-yellow-300">
                                                    Açılış Onayı
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 border border-red-300">
                                                    Kapatma Onayı
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex space-x-2">
                                                <form action="{{ route('company.work-permits.quick-approve', $permit) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="quick_action" value="approve">
                                                    <button type="submit"
                                                            class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition">
                                                        Onayla
                                                    </button>
                                                </form>

                                                <form action="{{ route('company.work-permits.quick-approve', $permit) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="quick_action" value="reject">
                                                    <button type="submit"
                                                            onclick="return confirm('Bu iş iznini reddetmek istediğinizden emin misiniz?')"
                                                            class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition">
                                                        Reddet
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-10">
                        <i class="fas fa-check-circle text-green-500 text-5xl mb-3"></i>
                        <p class="text-gray-500">Bekleyen İSG kontrolünüz bulunmamaktadır.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
