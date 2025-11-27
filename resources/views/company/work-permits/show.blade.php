@extends('layouts.app') {{-- ✅ Admin layout yerine normal layout --}}

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">

        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-3">İş İzni Detayları</h1>
                    <div class="flex flex-wrap items-center gap-4 text-lg">
                        <span class="text-2xl font-mono text-blue-600 font-bold">{{ $workPermit->permit_code }}</span>

                    </div>
                </div>

                <div class="flex gap-3">
                    <!-- ✅ COMPANY ROUTE KULLAN -->
                    <a href="{{ route('company.work-permits.index') }}"
                        class="px-5 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 flex items-center gap-2 shadow-md transition">
                        <i class="fas fa-arrow-left"></i>
                        <span>Geri Dön</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Sol Kolon: Detaylar -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Genel Bilgiler -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8">
                    <h2 class="text-2xl font-bold mb-6 flex items-center text-gray-900">
                        <i class="fas fa-clipboard-list text-blue-600 mr-3"></i>
                        İş İzni Bilgileri
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">İş Başlığı</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $workPermit->title }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">İş Türü</p>
                            <p class="text-lg font-semibold capitalize">{{ $workPermit->work_type }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Çalışan</p>
                            <p class="text-lg font-semibold">{{ $workPermit->worker_name }}</p>
                            <p class="text-sm text-gray-600">{{ $workPermit->worker_position }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Lokasyon</p>
                            <p class="text-lg font-semibold">{{ $workPermit->location }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Başlangıç</p>
                            <p class="text-lg font-semibold text-green-600">
                                {{ $workPermit->start_date->format('d.m.Y H:i') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Bitiş</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $workPermit->end_date->format('d.m.Y H:i') }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <p class="text-sm text-gray-500 mb-2">İş Tanımı</p>
                        <div class="bg-gray-50 p-5 rounded-xl text-gray-800 leading-relaxed">
                            {{ $workPermit->work_description }}
                        </div>
                    </div>
                </div>

            </div>

            <!-- Sağ Kolon -->
            <div class="space-y-8">

                <!-- PDF ve Email Butonları -->
                <div class="bg-gradient-to-br from-green-600 to-blue-600 rounded-2xl shadow-2xl p-8 text-white">
                    <h3 class="text-2xl font-bold mb-4 flex items-center">
                        <i class="fas fa-file-pdf mr-3 text-2xl"></i>
                        PDF & Email İşlemleri
                    </h3>

                    <div class="space-y-4">
                        <!-- ✅ COMPANY ROUTE KULLAN -->
                        <a href="{{ route('company.work-permits.final-pdf.download', $workPermit) }}"
                            class="w-full bg-white text-green-700 font-bold py-3 rounded-xl hover:bg-gray-100 transition shadow-lg text-center block">
                            <i class="fas fa-download mr-2"></i>PDF İndir
                        </a>


                        <!-- ✅ COMPANY ROUTE KULLAN -->
                        <button onclick="sendFinalEmail({{ $workPermit->id }})" id="sendEmailBtn"
                            class="w-full bg-white text-purple-700 font-bold py-3 rounded-xl hover:bg-gray-100 transition shadow-lg">
                            <i class="fas fa-paper-plane mr-2"></i>Email Gönder
                        </button>
                    </div>
                </div>

                <!-- PDF Durumu -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                    <h3 class="text-xl font-bold mb-4 text-gray-900">PDF Durumu</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Durum:</span>
                            <span class="font-semibold {{ $workPermit->final_pdf_path ? 'text-green-600' : 'text-yellow-600' }}">
                                {{ $workPermit->final_pdf_path ? 'Oluşturuldu' : 'Oluşturulmadı' }}
                            </span>
                        </div>
                        @if($workPermit->final_pdf_path)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Dosya:</span>
                            <span class="font-mono text-xs">{{ basename($workPermit->final_pdf_path) }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Kurum Bilgileri -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8">
                    <h3 class="text-xl font-bold mb-6 text-gray-900">Kurum Bilgileri</h3>
                    <div class="space-y-5 text-lg">
                        <div><span class="text-gray-500">Şirket:</span>
                            <strong>{{ $workPermit->company->name ?? '—' }}</strong>
                        </div>
                        <div><span class="text-gray-500">Departman:</span>
                            <strong>{{ $workPermit->department->name ?? '—' }}</strong>
                        </div>
                        <div><span class="text-gray-500">Oluşturan:</span>
                            <strong>{{ $workPermit->creator->name ?? '—' }}</strong>
                        </div>
                        <div><span class="text-gray-500">Oluşturma:</span>
                            <strong>{{ $workPermit->created_at->format('d.m.Y H:i') }}</strong>
                        </div>
                        <div><span class="text-gray-500">Durum:</span>
                            <strong class="capitalize">{{ $workPermit->status }}</strong>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function generateFinalPdf(workPermitId) {
            if (confirm('PDF yeniden oluşturulsun mu?')) {
                const button = document.getElementById('generatePdfBtn');
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Oluşturuluyor...';
                button.disabled = true;

                // ✅ COMPANY URL KULLAN
                const url = `/company/work-permits/${workPermitId}/generate-final-pdf`;
                console.log('🔗 İstek URL:', url);

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    console.log('📡 Response status:', response.status);

                    return response.text().then(text => {
                        console.log('📄 Response body:', text.substring(0, 500));

                        if (text.trim().startsWith('<!DOCTYPE') || text.trim().startsWith('<html')) {
                            throw new Error('Route bulunamadı - HTML sayfası döndü');
                        }

                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error('JSON parse hatası: ' + e.message);
                        }
                    });
                })
                .then(data => {
                    console.log('✅ Success data:', data);
                    if (data.success) {
                        alert('✅ PDF başarıyla oluşturuldu!');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        throw new Error(data.message || 'Bilinmeyen hata');
                    }
                })
                .catch(error => {
                    console.error('❌ Error:', error);
                    alert('❌ Hata: ' + error.message + '\n\nURL: ' + url);
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
            }
        }

        function sendFinalEmail(workPermitId) {
            if (confirm('Onay emaili gönderilsin mi?')) {
                const button = document.getElementById('sendEmailBtn');
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Gönderiliyor...';
                button.disabled = true;

                // ✅ COMPANY URL KULLAN
                const url = `/company/work-permits/${workPermitId}/send-final-email`;
                console.log('🔗 İstek URL:', url);

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    console.log('📡 Response status:', response.status);

                    return response.text().then(text => {
                        console.log('📄 Response body:', text.substring(0, 500));

                        if (text.trim().startsWith('<!DOCTYPE') || text.trim().startsWith('<html')) {
                            throw new Error('Route bulunamadı - HTML sayfası döndü');
                        }

                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error('JSON parse hatası: ' + e.message);
                        }
                    });
                })
                .then(data => {
                    console.log('✅ Success data:', data);
                    if (data.success) {
                        alert('✅ Email başarıyla gönderildi!');
                        button.innerHTML = '<i class="fas fa-check mr-2"></i>Gönderildi';
                        setTimeout(() => {
                            button.innerHTML = originalText;
                            button.disabled = false;
                        }, 3000);
                    } else {
                        throw new Error(data.message || 'Bilinmeyen hata');
                    }
                })
                .catch(error => {
                    console.error('❌ Error:', error);
                    alert('❌ Hata: ' + error.message + '\n\nURL: ' + url);
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
            }
        }

        // Debug için
        console.log('🔧 Debug Bilgileri:');
        console.log('- WorkPermit ID:', {{ $workPermit->id }});
        console.log('- Status:', '{{ $workPermit->status }}');
        console.log('- Final PDF Path:', '{{ $workPermit->final_pdf_path }}');
        console.log('- User Role:', '{{ Auth::user()->role }}');
    </script>
@endsection
