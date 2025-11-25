@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Yeni Form Şablonu</h1>
                <p class="text-gray-600">İş izni formunuzu özelleştirin</p>
            </div>

            <form action="{{ route('form-templates.store') }}" method="POST" id="templateForm">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Sol: Temel Bilgiler -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow p-6 sticky top-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Temel Bilgiler</h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Şablon Adı</label>
                                    <input type="text" name="name" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="Örn: Elektrik İşleri Formu">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Açıklama</label>
                                    <textarea name="description" rows="3"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="Şablon açıklaması..."></textarea>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" name="is_default" id="is_default" value="1"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <label for="is_default" class="ml-2 text-sm text-gray-700">
                                        Varsayılan şablon olarak ayarla
                                    </label>
                                </div>

                                <div class="pt-4 border-t border-gray-200">
                                    <button type="submit"
                                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        Şablonu Kaydet
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Orta: Form Alanları -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Onay Süreci -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Onay Süreci</h3>
                            <div class="space-y-3" id="workflowContainer">
                                @foreach ($workflowSteps as $key => $label)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="workflow[]" value="{{ $key }}" checked
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 workflow-checkbox">
                                        <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Form Alanları -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Form Alanları</h3>
                                <button type="button" onclick="addField()"
                                    class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                                    + Alan Ekle
                                </button>
                            </div>

                            <div class="space-y-4" id="fieldsContainer">
                                <!-- Alanlar buraya JavaScript ile eklenecek -->
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        let fieldCount = 0;

        function addField(fieldData = {}) {
            fieldCount++;
            const fieldId = `field_${fieldCount}`;

            const fieldHtml = `
        <div class="border border-gray-200 rounded-lg p-4 field-item" data-field-id="${fieldId}">
            <div class="flex justify-between items-start mb-3">
                <h4 class="font-medium text-gray-900">Yeni Alan</h4>
                <button type="button" onclick="removeField('${fieldId}')"
                        class="text-red-600 hover:text-red-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alan Etiketi</label>
                    <input type="text" name="fields[${fieldCount}][label]"
                           value="${fieldData.label || ''}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Örn: Çalışan Adı">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alan Türü</label>
                    <select name="fields[${fieldCount}][type]" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 field-type"
                            onchange="handleFieldTypeChange(this, ${fieldCount})">
                        @foreach ($fieldTypes as $key => $label)
                            <option value="{{ $key }}" ${fieldData.type === '{{ $key }}' ? 'selected' : ''}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-3 field-options" id="options_${fieldCount}"
                 style="${fieldData.type === 'select' || fieldData.type === 'radio' ? '' : 'display: none;'}">
                <label class="block text-sm font-medium text-gray-700 mb-1">Seçenekler</label>
                <textarea name="fields[${fieldCount}][options]"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Her satıra bir seçenek yazın">${fieldData.options || ''}</textarea>
                <p class="text-xs text-gray-500 mt-1">Her satıra bir seçenek yazın</p>
            </div>

            <div class="mt-3 flex items-center">
                <input type="checkbox" name="fields[${fieldCount}][required]" value="1"
                       ${fieldData.required ? 'checked' : ''}
                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label class="ml-2 text-sm text-gray-700">Zorunlu alan</label>
            </div>
        </div>
    `;

            document.getElementById('fieldsContainer').insertAdjacentHTML('beforeend', fieldHtml);
        }

        function removeField(fieldId) {
            const fieldElement = document.querySelector(`[data-field-id="${fieldId}"]`);
            if (fieldElement) {
                fieldElement.remove();
            }
        }

        function handleFieldTypeChange(selectElement, fieldIndex) {
            const optionsDiv = document.getElementById(`options_${fieldIndex}`);
            const fieldType = selectElement.value;

            if (fieldType === 'select' || fieldType === 'radio') {
                optionsDiv.style.display = 'block';
            } else {
                optionsDiv.style.display = 'none';
            }
        }

        // Sayfa yüklendiğinde bir boş alan ekle
        document.addEventListener('DOMContentLoaded', function() {
            addField();
        });
    </script>
@endsection
