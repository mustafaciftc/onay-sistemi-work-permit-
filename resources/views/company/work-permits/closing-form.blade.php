@extends('layouts.app')

@section('title', 'İş İzni Kapatma Talebi')

@section('content')
    <div class="container mx-auto px-4 py-6 max-w-3xl">

        <!-- Başlık -->
        <div class="bg-gradient-to-r from-orange-600 to-orange-700 rounded-xl shadow-lg p-6 text-white mb-6">
            <div class="flex items-center">
                <i class="fas fa-times-circle text-4xl mr-4"></i>
                <div>
                    <h1 class="text-2xl font-bold">İş İzni Kapatma Talebi</h1>
                    <p class="text-orange-100 text-sm mt-1">
                        {{ $workPermit->permit_code }} - {{ $workPermit->title }}
                    </p>
                </div>
            </div>
        </div>

        <!-- İş İzni Özeti -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>İş İzni Bilgileri
            </h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-600">İzin Kodu:</span>
                    <span class="font-semibold text-gray-900 ml-2">{{ $workPermit->permit_code }}</span>
                </div>
                <div>
                    <span class="text-gray-600">Lokasyon:</span>
                    <span class="font-semibold text-gray-900 ml-2">{{ $workPermit->location }}</span>
                </div>
                <div>
                    <span class="text-gray-600">İş Türü:</span>
                    <span class="font-semibold text-gray-900 ml-2">{{ ucfirst($workPermit->work_type) }}</span>
                </div>
                <div>
                    <span class="text-gray-600">Başlangıç:</span>
                    <span class="font-semibold text-gray-900 ml-2">{{ $workPermit->start_date->format('d.m.Y H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- Kapatma Formu -->
        <form action="{{ route('admin.work-permits.initiate-closing.submit', $workPermit) }}" method="POST">
            @csrf

            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-clipboard-check text-orange-600 mr-2"></i>Kapatma Kontrolleri
                </h2>

                <div class="space-y-4">
                    <!-- İş Tamamlandı -->
                    <label
                        class="flex items-start space-x-3 p-4 border-2 border-gray-200 rounded-lg hover:border-orange-500 cursor-pointer transition">
                        <input type="checkbox" name="work_completed" value="1" required
                            class="w-6 h-6 text-orange-600 rounded focus:ring-2 focus:ring-orange-500 mt-1">
                        <div class="flex-1">
                            <span class="font-semibold text-gray-900 block">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>İş Tamamlandı
                            </span>
                            <span class="text-sm text-gray-600">
                                Çalışma kapsamındaki tüm işler tamamlanmıştır
                            </span>
                        </div>
                    </label>

                    <!-- Ekipmanlar Toplandı -->
                    <label
                        class="flex items-start space-x-3 p-4 border-2 border-gray-200 rounded-lg hover:border-orange-500 cursor-pointer transition">
                        <input type="checkbox" name="equipment_collected" value="1" required
                            class="w-6 h-6 text-orange-600 rounded focus:ring-2 focus:ring-orange-500 mt-1">
                        <div class="flex-1">
                            <span class="font-semibold text-gray-900 block">
                                <i class="fas fa-toolbox text-blue-500 mr-2"></i>Ekipmanlar Toplandı
                            </span>
                            <span class="text-sm text-gray-600">
                                Tüm ekipman ve malzemeler alandan kaldırılmıştır
                            </span>
                        </div>
                    </label>

                    <!-- Acil Durum Ekipmanları Kapatıldı -->
                    <label
                        class="flex items-start space-x-3 p-4 border-2 border-gray-200 rounded-lg hover:border-orange-500 cursor-pointer transition">
                        <input type="checkbox" name="emergency_equipment_closed" value="1" required
                            class="w-6 h-6 text-orange-600 rounded focus:ring-2 focus:ring-orange-500 mt-1">
                        <div class="flex-1">
                            <span class="font-semibold text-gray-900 block">
                                <i class="fas fa-fire-extinguisher text-red-500 mr-2"></i>Acil Durum Ekipmanları Kapatıldı
                            </span>
                            <span class="text-sm text-gray-600">
                                Tüm acil durum ve güvenlik ekipmanları yerlerine kaldırılmıştır
                            </span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Kapatma Notları -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-comment mr-2"></i>Kapatma Notları (Opsiyonel)
                </label>
                <textarea name="closing_notes" rows="4"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-orange-500"
                    placeholder="Kapatma süreci ile ilgili notlarınızı yazın..."></textarea>
            </div>

            <!-- Uyarı -->
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6">
                <div class="flex">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mr-3 mt-1"></i>
                    <div class="text-sm text-yellow-800">
                        <p class="font-semibold mb-1">DİKKAT</p>
                        <p>Kapatma talebini gönderdikten sonra, sırasıyla <strong>Alan Amiri</strong>, <strong>İSG
                                Uzmanı</strong> ve <strong>İşveren Vekili</strong> onayı beklenecektir.</p>
                    </div>
                </div>
            </div>

            <!-- Butonlar -->
            <div class="flex justify-between items-center">
                <a href="{{ route('company.dashboard') }}"
                    class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition font-semibold">
                    <i class="fas fa-arrow-left mr-2"></i>İptal
                </a>
                <button type="submit"
                    class="bg-orange-600 text-white px-8 py-3 rounded-lg hover:bg-orange-700 transition font-bold shadow-lg">
                    <i class="fas fa-paper-plane mr-2"></i>Kapatma Talebi Gönder
                </button>
            </div>
        </form>

    </div>
@endsection
