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
                <a href="{{ route('admin.work-permits.index') }}"
                    class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    İş İzinleri Listesi
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <form action="{{ route('admin.work-permits.store') }}" method="POST" id="workPermitForm">
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
                                    <option value="">Departman Seçin</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ old('department_id') == $department->id ? 'selected' : '' }}
                                            data-positions-url="{{ route('departments.positions', $department) }}">
                                            {{ $department->name }}
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

                            <!-- Diğer input alanları aynı kalacak -->
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

                            <!-- İş Türü -->
                            <div>
                                <label for="work_type" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-hard-hat mr-1 text-gray-500"></i>
                                    İş Türü *
                                </label>
                                <select name="work_type" id="work_type" required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                                    <option value="">İş Türü Seçin</option>
                                    <option value="sıcak" {{ old('work_type') == 'sıcak' ? 'selected' : '' }}>Sıcak İş
                                    </option>
                                    <option value="elektrik" {{ old('work_type') == 'elektrik' ? 'selected' : '' }}>
                                        Elektrik İşi</option>
                                    <option value="yuk_kaldirma"
                                        {{ old('work_type') == 'yuk_kaldirma' ? 'selected' : '' }}>Yük Kaldırma</option>
                                    <option value="kazı" {{ old('work_type') == 'kazı' ? 'selected' : '' }}>Kazı İşi
                                    </option>
                                    <option value="diğer" {{ old('work_type') == 'diğer' ? 'selected' : '' }}>Diğer
                                    </option>
                                </select>
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
                                    value="{{ old('worker_name') }}" placeholder="Çalışanın adı soyadı"
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

                        <div>
                            <label for="work_description" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-align-left mr-1 text-gray-500"></i>
                                İş Açıklaması *
                            </label>
                            <textarea name="work_description" id="work_description" required rows="4"
                                placeholder="Yapılacak işin detaylı açıklaması..."
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">{{ old('work_description') }}</textarea>
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
                        <a href="{{ route('admin.work-permits.index') }}"
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
        // Departman değiştiğinde pozisyonları yükle
        document.getElementById('department_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const positionsUrl = selectedOption.getAttribute('data-positions-url');
            const positionSelect = document.getElementById('position_id');

            if (!positionsUrl) {
                positionSelect.innerHTML = '<option value="">Önce departman seçin</option>';
                return;
            }

            // Loading state
            positionSelect.innerHTML = '<option value="">Yükleniyor...</option>';
            positionSelect.disabled = true;

            // AJAX ile pozisyonları getir
            fetch(positionsUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(positions => {
                    positionSelect.innerHTML = '<option value="">Pozisyon seçin</option>';

                    if (positions && positions.length > 0) {
                        positions.forEach(position => {
                            const option = document.createElement('option');
                            option.value = position.id;
                            option.textContent = position.name;
                            positionSelect.appendChild(option);
                        });
                    } else {
                        positionSelect.innerHTML =
                            '<option value="">Bu departmanda pozisyon bulunamadı</option>';
                    }

                    positionSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Pozisyonlar yüklenirken hata:', error);
                    positionSelect.innerHTML = '<option value="">Pozisyonlar yüklenemedi</option>';
                    positionSelect.disabled = false;
                });
        });

        // Sayfa yüklendiğinde eğer departman seçiliyse pozisyonları yükle
        document.addEventListener('DOMContentLoaded', function() {
            const departmentSelect = document.getElementById('department_id');
            if (departmentSelect.value) {
                departmentSelect.dispatchEvent(new Event('change'));
            }
        });

        // Risk ve önlem alanları için fonksiyonlar
        function addRiskField() {
            const container = document.getElementById('risks-container');
            const div = document.createElement('div');
            div.className = 'flex gap-2';
            div.innerHTML = `
            <input type="text" name="risks[]" placeholder="Risk tanımı"
                class="flex-1 border border-red-300 rounded-lg px-4 py-2 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition">
            <button type="button" onclick="this.parentElement.remove()"
                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                <i class="fas fa-minus"></i>
            </button>
        `;
            container.appendChild(div);
        }

        function addMeasureField() {
            const container = document.getElementById('measures-container');
            const div = document.createElement('div');
            div.className = 'flex gap-2';
            div.innerHTML = `
            <input type="text" name="control_measures[]" placeholder="Önlem açıklaması"
                class="flex-1 border border-green-300 rounded-lg px-4 py-2 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition">
            <button type="button" onclick="this.parentElement.remove()"
                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                <i class="fas fa-minus"></i>
            </button>
        `;
            container.appendChild(div);
        }

        // Form validation
        document.getElementById('workPermitForm').addEventListener('submit', function(e) {
            const startDate = new Date(document.getElementById('start_date').value);
            const endDate = new Date(document.getElementById('end_date').value);

            if (endDate <= startDate) {
                e.preventDefault();
                alert('Bitiş tarihi başlangıç tarihinden sonra olmalıdır.');
                return false;
            }

            // Risks and measures validation
            const risks = document.querySelectorAll('input[name="risks[]"]');
            const measures = document.querySelectorAll('input[name="control_measures[]"]');

            let hasRisks = false;
            let hasMeasures = false;

            risks.forEach(input => {
                if (input.value.trim() !== '') hasRisks = true;
            });

            measures.forEach(input => {
                if (input.value.trim() !== '') hasMeasures = true;
            });

            if (!hasRisks) {
                e.preventDefault();
                alert('En az bir risk tanımlanmalıdır.');
                return false;
            }

            if (!hasMeasures) {
                e.preventDefault();
                alert('En az bir kontrol önlemi tanımlanmalıdır.');
                return false;
            }
        });
    </script>
@endpush
