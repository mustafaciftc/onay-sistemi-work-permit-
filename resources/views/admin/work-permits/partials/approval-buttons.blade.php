@php
    $currentStep = $workPermit->currentStep;
    $stepConfig = [
        'unit_manager' => [
            'title' => 'Birim Amiri Onayı',
            'fields' => [],
        ],
        'area_manager' => [
            'title' => 'Alan Amiri Onayı',
            'fields' => [
                [
                    'name' => 'approval_data[energy_cut_off]',
                    'label' => 'Enerji Kesildi mi?',
                    'type' => 'checkbox',
                ],
                [
                    'name' => 'approval_data[area_cleaned]',
                    'label' => 'Alan Temizlendi mi?',
                    'type' => 'checkbox',
                ],
                [
                    'name' => 'approval_data[no_conflict_with_other_works]',
                    'label' => 'Diğer İşlerle Çakışma Var mı?',
                    'type' => 'checkbox',
                    'inverse' => true,
                ],
                [
                    'name' => 'approval_data[notes]',
                    'label' => 'Ek Notlar',
                    'type' => 'textarea',
                ],
            ],
        ],
        'safety_specialist' => [
            'title' => 'İSG Uzmanı Onayı',
            'fields' => [
                [
                    'name' => 'approval_data[gas_measurement_done]',
                    'label' => 'Gaz Ölçümü Yapıldı mı?',
                    'type' => 'checkbox',
                ],
                [
                    'name' => 'approval_data[ppe_checked]',
                    'label' => 'KKD Kontrolü Yapıldı mı?',
                    'type' => 'checkbox',
                ],
                [
                    'name' => 'approval_data[additional_procedures_verified]',
                    'label' => 'Ek Prosedürler Doğrulandı mı?',
                    'type' => 'checkbox',
                ],
                [
                    'name' => 'approval_data[notes]',
                    'label' => 'Teknik Notlar',
                    'type' => 'textarea',
                ],
            ],
        ],
        'employer_representative' => [
            'title' => 'İşveren Vekili Onayı',
            'fields' => [
                [
                    'name' => 'approval_data[notes]',
                    'label' => 'Final Notlar',
                    'type' => 'textarea',
                ],
            ],
        ],
    ];

    $currentConfig = $stepConfig[$currentStep->step] ?? [];
@endphp

<div class="bg-white rounded-lg shadow p-6 mt-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $currentConfig['title'] ?? 'Onay İşlemi' }}</h3>

    <form action="{{ route('admin.work-permits.approve', $workPermit) }}" method="POST">
        @csrf

        @if (!empty($currentConfig['fields']))
            <div class="space-y-4 mb-6">
                @foreach ($currentConfig['fields'] as $field)
                    <div>
                        @if ($field['type'] === 'checkbox')
                            <label class="flex items-center">
                                <input type="checkbox" name="{{ $field['name'] }}" value="1"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">
                                    {{ $field['label'] }}
                                    @if (isset($field['inverse']) && $field['inverse'])
                                        <span class="text-red-600">(Yok ise işaretleyin)</span>
                                    @endif
                                </span>
                            </label>
                        @elseif($field['type'] === 'textarea')
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ $field['label'] }}
                            </label>
                            <textarea name="{{ $field['name'] }}" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="{{ $field['label'] }}"></textarea>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <div class="space-y-3">
            <label class="block text-sm font-medium text-gray-700">Onay Notu (Opsiyonel)</label>
            <textarea name="comments" rows="2"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Onay veya reddetme sebebinizi yazın..."></textarea>
        </div>

        <div class="flex justify-end space-x-3 mt-6">
            <button type="submit" name="action" value="reject"
                class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                Reddet
            </button>
            <button type="submit" name="action" value="approve"
                class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                Onayla
            </button>
        </div>
    </form>
</div>
