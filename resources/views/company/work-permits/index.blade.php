@extends('admin.layout')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">

        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-10 gap-6">
            <div class="flex items-start lg:items-center gap-6">


                <div>
                    <h1 class="text-4xl font-black text-gray-900">İş İzinleri</h1>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <button type="button" onclick="toggleFilters()"
                    class="group inline-flex items-center gap-3 px-6 py-3.5 bg-white/80 backdrop-blur-sm border border-gray-200 rounded-xl shadow hover:shadow-md hover:bg-white transition-all duration-300">
                    <i class="fas fa-filter text-gray-600 group-hover:text-blue-600 transition-colors"></i>
                    <span class="font-semibold text-gray-700">Filtrele</span>
                </button>

                <a href="{{ route('admin.work-permits.create') }}"
                    class="group inline-flex items-center gap-3 px-7 py-3.5 bg-linear-to-r from-blue-600 to-purple-600 font-bold rounded-xl shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-400">
                    <i class="fas fa-plus-circle text-xl group-hover:scale-110 transition-transform"></i>
                    <span>Yeni İş İzni Oluştur</span>
                </a>
            </div>
        </div>

        <!-- Filtre Alanı (Gizli başlangıçta) -->
        <div id="filterSection" class="hidden mb-6 bg-gray-50 rounded-lg p-5 border border-gray-200">
            <form method="GET" action="{{ route('admin.work-permits.index') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
                    <select name="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tümü</option>
                        <option value="pending_unit_approval"
                            {{ request('status') == 'pending_unit_approval' ? 'selected' : '' }}>Birim Onayı Bekliyor
                        </option>
                        <option value="pending_area_approval"
                            {{ request('status') == 'pending_area_approval' ? 'selected' : '' }}>Alan Onayı Bekliyor
                        </option>
                        <option value="pending_safety_approval"
                            {{ request('status') == 'pending_safety_approval' ? 'selected' : '' }}>İSG Onayı Bekliyor
                        </option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Onaylandı</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Reddedildi</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Tamamlandı
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">İş Türü</label>
                    <select name="work_type"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tümü</option>
                        <option value="sıcak" {{ request('work_type') == 'sıcak' ? 'selected' : '' }}>Sıcak İş</option>
                        <option value="elektrik" {{ request('work_type') == 'elektrik' ? 'selected' : '' }}>Elektrik İşi
                        </option>
                        <option value="yuk_kaldirma" {{ request('work_type') == 'yuk_kaldirma' ? 'selected' : '' }}>Yük
                            Kaldırma</option>
                        <option value="kazı" {{ request('work_type') == 'kazı' ? 'selected' : '' }}>Kazı İşi</option>
                        <option value="diğer" {{ request('work_type') == 'diğer' ? 'selected' : '' }}>Diğer</option>
                    </select>
                </div>

                @php
                    $currentCompany = auth()->user()->currentCompany();
                    $departments = $currentCompany
                        ? \App\Models\Department::where('company_id', $currentCompany->id)
                            ->where('is_active', true)
                            ->orderBy('name')
                            ->get()
                        : collect();
                @endphp

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Departman</label>
                    <select name="department_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tümü</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}"
                                {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end space-x-3">
                    <button type="submit"
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                        <i class="fas fa-search mr-2"></i>Uygula
                    </button>
                    <a href="{{ route('admin.work-permits.index') }}"
                        class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                        Temizle
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-clipboard-list mr-2 text-blue-600"></i>
                    İş İzinleri Listesi
                    <span class="ml-3 text-sm font-normal text-gray-600">
                        Toplam: {{ $workPermits->total() }} izin
                        @if (request()->hasAny(['status', 'work_type', 'department_id']))
                            ({{ $workPermits->count() }} gösteriliyor)
                        @endif
                    </span>
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Başlık / Çalışan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İş
                                Türü</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Departman / Şirket</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($workPermits as $permit)
                            <tr class="hover:bg-gray-50 transition-colors">

                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('admin.work-permits.show', $permit) }}"
                                        class="font-medium text-gray-900 hover:text-blue-600 block">
                                        {{ Str::limit($permit->title ?? $permit->work_description, 40) }}
                                    </a>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-user mr-1"></i>{{ $permit->worker_name }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $permit->work_type == 'sıcak'
                                        ? 'bg-red-100 text-red-800'
                                        : ($permit->work_type == 'elektrik'
                                            ? 'bg-yellow-100 text-yellow-800'
                                            : ($permit->work_type == 'yuk_kaldirma'
                                                ? 'bg-purple-100 text-purple-800'
                                                : ($permit->work_type == 'kazı'
                                                    ? 'bg-orange-100 text-orange-800'
                                                    : 'bg-gray-100 text-gray-800'))) }}">
                                        {{ ucfirst(str_replace('_', ' ', $permit->work_type)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div>{{ $permit->department?->name ?? '—' }}</div>
                                    <div class="text-xs text-gray-500">{{ $permit->company?->name ?? '—' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusConfig = [
                                            'pending_unit_approval' => [
                                                'bg-yellow-100 text-yellow-800',
                                                'Birim Onayı Bekliyor',
                                            ],
                                            'pending_area_approval' => [
                                                'bg-orange-100 text-orange-800',
                                                'Alan Onayı Bekliyor',
                                            ],
                                            'pending_safety_approval' => [
                                                'bg-red-100 text-red-800',
                                                'İSG Onayı Bekliyor',
                                            ],
                                            'approved' => ['bg-green-100 text-green-800', 'Onaylandı'],
                                            'rejected' => ['bg-red-100 text-red-800', 'Reddedildi'],
                                            'completed' => ['bg-blue-100 text-blue-800', 'Tamamlandı'],
                                        ];
                                        $cfg = $statusConfig[$permit->status] ?? [
                                            'bg-gray-100 text-gray-800',
                                            'Bilinmiyor',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $cfg[0] }}">
                                        {{ $cfg[1] }}
                                        @if ($permit->isOverdue() && $permit->status === 'approved')
                                            <i class="fas fa-exclamation-triangle ml-1 text-red-600"
                                                title="Süre geçti!"></i>
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $permit->start_date->format('d.m.Y') }}
                                    <span class="text-xs block">→ {{ $permit->end_date->format('d.m.Y') }}</span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <a href="{{ route('admin.work-permits.show', $permit) }}"
                                        class="text-blue-600 hover:text-blue-900 mr-3" title="Detay">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if ($permit->pdf_path)
                                        <a href="{{ route('admin.work-permits.pdf', $permit) }}"
                                            class="text-green-600 hover:text-green-900" title="PDF İndir">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-3 block text-gray-300"></i>
                                    <p>Henüz hiç iş izni oluşturulmamış.</p>
                                    <a href="{{ route('admin.work-permits.create') }}"
                                        class="text-blue-600 hover:underline mt-2 inline-block">
                                        → İlk iş iznini şimdi oluşturun
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($workPermits->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $workPermits->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

    </div>

    <script>
        function toggleFilters() {
            const filter = document.getElementById('filterSection');
            filter.classList.toggle('hidden');
        }
    </script>
@endsection
