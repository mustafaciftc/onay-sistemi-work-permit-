@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Raporlar ve İstatistikler</h1>
                <p class="text-gray-600">{{ $company->name }} şirketi istatistikleri</p>
            </div>

            <!-- İstatistik Kartları -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-clipboard-list text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-700">Toplam İş İzni</h3>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-amber-100 text-amber-600">
                            <i class="fas fa-clock text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-700">Bekleyen Onay</h3>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_approvals'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-emerald-100 text-emerald-600">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-700">Tamamlanma Oranı</h3>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['completion_rate'] }}%</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-chart-line text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-700">Aktiflik</h3>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['monthly_counts']->last() ?? 0 }}/ay</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grafikler -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Aylık İş İzni Grafiği -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Aylık İş İzni Dağılımı</h3>
                    <div class="h-64">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>

                <!-- İş Türü Dağılımı -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">İş Türü Dağılımı</h3>
                    <div class="h-64">
                        <canvas id="workTypeChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Hızlı Erişim Butonları -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ route('admin.work-permits') }}"
                    class="bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-list-alt text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">İş İzni Listesi</h3>
                            <p class="text-gray-600">Tüm iş izinlerini görüntüle ve filtrele</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('reports.approvals') }}"
                    class="bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-check-double text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Onay Geçmişi</h3>
                            <p class="text-gray-600">Onay süreçlerini incele</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('reports.export-work-permits') }}"
                    class="bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-download text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Veri İndir</h3>
                            <p class="text-gray-600">İş izni verilerini CSV olarak indir</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Aylık Grafik
            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            const monthlyChart = new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($charts['monthly']->pluck('month')) !!},
                    datasets: [{
                            label: 'Toplam İş İzni',
                            data: {!! json_encode($charts['monthly']->pluck('total')) !!},
                            backgroundColor: 'rgba(59, 130, 246, 0.5)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Tamamlanan',
                            data: {!! json_encode($charts['monthly']->pluck('completed')) !!},
                            backgroundColor: 'rgba(16, 185, 129, 0.5)',
                            borderColor: 'rgba(16, 185, 129, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // İş Türü Grafiği
            const workTypeCtx = document.getElementById('workTypeChart').getContext('2d');
            const workTypeChart = new Chart(workTypeCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode(array_keys($charts['work_types']->toArray())) !!},
                    datasets: [{
                        data: {!! json_encode(array_values($charts['work_types']->toArray())) !!},
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(139, 92, 246, 0.8)',
                            'rgba(236, 72, 153, 0.8)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
    </script>
@endsection
