@extends('layouts.app')

@section('title', 'Yeni İş İzni Oluştur')

@section('content')

    <div class="max-w-7xl mx-auto px-4 py-8">

        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Yeni İş İzni Oluştur</h1>
                    <p class="text-gray-600">Güvenli çalışma için iş izni formunu doldurun</p>
                </div>
                <a href="{{ route('company.work-permits.index') }}"
                    class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    İş İzinleri Listesi
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <form action="{{ route('company.work-permits.store') }}" method="POST" id="workPermitForm">
                @csrf

                <div class="p-8 space-y-8">
                    <!-- Basic Information Section -->
                    <div class="border-b border-gray-200 pb-8">
                        <div class="flex items-center mb-6">
                            <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                <i class="fas fa-info-circle text-blue-600"></i>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-900">Temel Bilgiler</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Departman -->
                            <div>
                                <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-sitemap mr-1 text-gray-500"></i>
                                    Departman *
                                </label>
                                <select name="department_id" id="department_id" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                                    <option value="">Departman seçin</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}"
                                            data-positions-url="/company/work-permits/departments/{{ $dept->id }}/positions"
                                            {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Pozisyon -->
                            <div>
                                <label for="position_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-briefcase mr-1 text-gray-500"></i>
                                    Pozisyon *
                                </label>
                                <select name="position_id" id="position_id" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                                    <option value="">Önce departman seçin</option>
                                </select>
                                @error('position_id')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- İş Başlığı -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-heading mr-1 text-gray-500"></i>
                                    İş Başlığı *
                                </label>
                                <input type="text" name="title" id="title" required value="{{ old('title') }}"
                                    placeholder="İşin kısa tanımı"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                                @error('title')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="work_type" class="block text-sm font-medium text-gray-700 mb-2">
                                    İş Türü *
                                </label>
                                <input type="text" name="work_type" id="work_type" required
                                    value="{{ old('work_type', $workPermit->work_type ?? '') }}"
                                    placeholder="Örn: Sıcak İş, Elektrik İşi, Kapalı Alan, Kimyasal Çalışma..."
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">

                                @error('work_type')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Lokasyon -->
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-map-marker-alt mr-1 text-gray-500"></i>
                                    Çalışma Lokasyonu *
                                </label>
                                <input type="text" name="location" id="location" required value="{{ old('location') }}"
                                    placeholder="Çalışma yapılacak alan"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                                @error('location')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Çalışan Adı -->
                            <div>
                                <label for="worker_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-user mr-1 text-gray-500"></i>
                                    Çalışan Adı *
                                </label>
                                <input type="text" name="worker_name" id="worker_name" required
                                    placeholder="Çalışanın adı soyadı"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                                @error('worker_name')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Başlangıç Tarihi -->
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-calendar-plus mr-1 text-gray-500"></i>
                                    Başlangıç Tarihi *
                                </label>
                                <input type="datetime-local" name="start_date" id="start_date" required
                                    value="{{ old('start_date') }}"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                                @error('start_date')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Bitiş Tarihi -->
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-calendar-minus mr-1 text-gray-500"></i>
                                    Bitiş Tarihi *
                                </label>
                                <input type="datetime-local" name="end_date" id="end_date" required
                                    value="{{ old('end_date') }}"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                                @error('end_date')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- İş Tanımı Section -->
                    <div class="border-b border-gray-200 pb-8">
                        <div class="flex items-center mb-6">
                            <div class="p-2 bg-green-100 rounded-lg mr-3">
                                <i class="fas fa-clipboard-list text-green-600"></i>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-900">İş Tanımı</h2>
                        </div>

                        <!-- İş Açıklaması -->
                        <div class="col-span-2">
                            <label for="work_description" class="block text-sm font-medium text-gray-700 mb-2">
                                İş Açıklaması *
                            </label>
                            <textarea name="work_description" id="work_description" rows="5" required
                                placeholder="Yapılacak işin tüm detaylarını burada açıklayın (örneğin: hangi ekipman kullanılacak, kaç kişi çalışacak, hangi riskler var...)"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition resize-none">{{ old('work_description') }}</textarea>

                            @error('work_description')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Risk Analizi Section -->
                    <div class="border-b border-gray-200 pb-8">
                        <div class="flex items-center mb-6">
                            <div class="p-2 bg-red-100 rounded-lg mr-3">
                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-900">Risk Analizi</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Riskler -->
                            <div>
                                <label class="block text-sm font-medium text-red-700 mb-3">
                                    <i class="fas fa-radiation-alt mr-1"></i>
                                    Tespit Edilen Riskler *
                                </label>
                                <div id="risks-container" class="space-y-3">
                                    <div class="flex gap-2">
                                        <input type="text" name="risks[]" placeholder="Risk tanımı"
                                            class="flex-1 border border-red-300 rounded-lg px-4 py-2 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition">
                                        <button type="button" onclick="addRiskField()"
                                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('risks')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Kontrol Önlemleri -->
                            <div>
                                <label class="block text-sm font-medium text-green-700 mb-3">
                                    <i class="fas fa-shield-alt mr-1"></i>
                                    Alınacak Önlemler *
                                </label>
                                <div id="measures-container" class="space-y-3">
                                    <div class="flex gap-2">
                                        <input type="text" name="control_measures[]" placeholder="Önlem açıklaması"
                                            class="flex-1 border border-green-300 rounded-lg px-4 py-2 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition">
                                        <button type="button" onclick="addMeasureField()"
                                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('control_measures')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Ekipman ve Acil Durum Section -->
                    <div class="border-b border-gray-200 pb-8">
                        <div class="flex items-center mb-6">
                            <div class="p-2 bg-purple-100 rounded-lg mr-3">
                                <i class="fas fa-tools text-purple-600"></i>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-900">Ekipman ve Acil Durum</h2>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            <!-- Kullanılacak Ekipmanlar -->
                            <div>
                                <label for="tools_equipment" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-wrench mr-1 text-gray-500"></i>
                                    Kullanılacak Ekipman ve Araçlar *
                                </label>
                                <textarea name="tools_equipment" id="tools_equipment" required rows="3"
                                    placeholder="Kullanılacak ekipman, alet ve makineler..."
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">{{ old('tools_equipment') }}</textarea>
                                @error('tools_equipment')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Acil Durum Prosedürleri -->
                            <div>
                                <label for="emergency_procedures" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-first-aid mr-1 text-gray-500"></i>
                                    Acil Durum Prosedürleri *
                                </label>
                                <textarea name="emergency_procedures" id="emergency_procedures" required rows="3"
                                    placeholder="Acil durumda izlenecek prosedürler..."
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">{{ old('emergency_procedures') }}</textarea>
                                @error('emergency_procedures')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-4 pt-6">
                        <a href="{{ route('company.work-permits.index') }}"
                            class="px-8 py-4 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-semibold">
                            <i class="fas fa-times mr-2"></i>İptal
                        </a>
                        <button type="submit"
                            class="px-8 py-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                            <i class="fas fa-save mr-2"></i>İş İzni Oluştur
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const departmentSelect = document.getElementById('department_id');
            const positionSelect = document.getElementById('position_id');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            console.log('🚀 Script yüklendi - Esnek mod aktif');

            // Eski değer varsa
            if (departmentSelect.value) {
                departmentSelect.dispatchEvent(new Event('change'));
            }

            // Departman değiştiğinde pozisyonları yükle
            departmentSelect.addEventListener('change', function() {
                const departmentId = this.value;
                const departmentName = this.options[this.selectedIndex].text;

                console.log('📝 Departman değişti:', departmentId, departmentName);

                // Reset pozisyon alanı
                positionSelect.innerHTML = '<option value="">Yükleniyor...</option>';
                positionSelect.disabled = true;
                clearPositionError();

                if (!departmentId) {
                    positionSelect.innerHTML = '<option value="">Önce departman seçin</option>';
                    return;
                }

                const positionsUrl = `/company/work-permits/departments/${departmentId}/positions`;

                console.log('🔗 API URL:', positionsUrl);

                fetch(positionsUrl, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(response => {
                        console.log('📡 Response status:', response.status);

                        if (!response.ok) {
                            // 403 hatasında bile devam et - pozisyonları getirmeye çalış
                            console.warn('⚠️ Response not OK, but continuing...');
                        }

                        return response.json();
                    })
                    .then(data => {
                        console.log('📊 Response data:', data);

                        // ✅ ERROR KONTROLÜ - data error içeriyorsa bile devam et
                        if (data.error) {
                            console.warn('⚠️ Sunucu hatası ama devam ediliyor:', data.error);
                            // Hata olsa bile boş array gibi davran
                            data = [];
                        }

                        // ✅ DATA'NIN ARRAY OLDUĞUNDAN EMİN OL
                        const positions = Array.isArray(data) ? data : [];

                        console.log('✅ Pozisyonlar işlendi:', positions);

                        positionSelect.innerHTML = '';

                        if (positions.length === 0) {
                            const option = document.createElement('option');
                            option.value = '';
                            option.textContent = 'Bu departmanda aktif pozisyon bulunamadı';
                            option.disabled = true;
                            positionSelect.appendChild(option);
                            positionSelect.disabled = true;

                            showPositionError('Seçilen departmanda aktif pozisyon bulunamadı.');
                        } else {
                            const defaultOption = document.createElement('option');
                            defaultOption.value = '';
                            defaultOption.textContent = 'Pozisyon seçin';
                            positionSelect.appendChild(defaultOption);

                            positions.forEach(pos => {
                                const option = document.createElement('option');
                                option.value = pos.id;
                                option.textContent = pos.name;

                                const oldPositionId = "{{ old('position_id') }}";
                                if (oldPositionId && oldPositionId == pos.id) {
                                    option.selected = true;
                                }

                                positionSelect.appendChild(option);
                            });

                            positionSelect.disabled = false;
                            clearPositionError();

                            console.log('✅ Pozisyonlar yüklendi:', positions.length + ' adet');
                        }
                    })
                    .catch(error => {
                        console.error('❌ Pozisyonlar yüklenemedi:', error);
                        positionSelect.innerHTML = '<option value="">Pozisyonlar yüklenemedi</option>';

                        // ❌ ARTIK HATA GÖSTERME - SADECE KONSOLA YAZ
                        console.warn('Pozisyon yükleme hatası (görmezden geliniyor):', error.message);
                    });
            });
        });

        function showPositionError(message) {
            clearPositionError();
            const positionField = document.getElementById('position_id');
            const errorDiv = document.createElement('p');
            errorDiv.id = 'position-error';
            errorDiv.className = 'text-red-600 text-sm mt-2 font-medium';
            errorDiv.textContent = message;
            positionField.parentNode.appendChild(errorDiv);
            positionField.classList.add('border-red-500', 'ring-2', 'ring-red-200');
        }

        function clearPositionError() {
            const existingError = document.getElementById('position-error');
            if (existingError) existingError.remove();

            const positionField = document.getElementById('position_id');
            positionField.classList.remove('border-red-500', 'ring-2', 'ring-red-200');
        }

        function showPositionError(message) {
            clearPositionError();
            const positionField = document.getElementById('position_id');
            const errorDiv = document.createElement('p');
            errorDiv.id = 'position-error';
            errorDiv.className = 'text-red-600 text-sm mt-2 font-medium';
            errorDiv.textContent = message;
            positionField.parentNode.appendChild(errorDiv);
            positionField.classList.add('border-red-500', 'ring-2', 'ring-red-200');
        }

        function clearPositionError() {
            const existingError = document.getElementById('position-error');
            if (existingError) existingError.remove();

            const positionField = document.getElementById('position_id');
            positionField.classList.remove('border-red-500', 'ring-2', 'ring-red-200');
        }

        function addRiskField() {
            const container = document.getElementById('risks-container');
            const div = document.createElement('div');
            div.className = 'flex gap-2 mt-2';
            div.innerHTML = `
            <input type="text" name="risks[]" placeholder="Risk tanımı"
                   class="flex-1 border border-red-300 rounded-lg px-4 py-2 focus:border-red-500">
            <button type="button" onclick="this.parentElement.remove()"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                <i class="fas fa-trash"></i>
            </button>
        `;
            container.appendChild(div);
        }

        function addMeasureField() {
            const container = document.getElementById('measures-container');
            const div = document.createElement('div');
            div.className = 'flex gap-2 mt-2';
            div.innerHTML = `
            <input type="text" name="control_measures[]" placeholder="Önlem açıklaması"
                   class="flex-1 border border-green-300 rounded-lg px-4 py-2 focus:border-green-500">
            <button type="button" onclick="this.parentElement.remove()"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-trash"></i>
            </button>
        `;
            container.appendChild(div);
        }
    </script>
@endpush
