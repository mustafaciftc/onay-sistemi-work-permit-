<div class="flex items-center justify-between">
    @php
        $steps = [
            'unit_manager' => [
                'label' => 'Birim Amiri',
                'status' => $workPermit->status === 'pending_unit_approval' ? 'current' :
                           (in_array($workPermit->status, ['pending_area_approval', 'pending_safety_approval', 'pending_employer_approval', 'approved', 'closing_requested', 'pending_area_closing', 'pending_safety_closing', 'pending_employer_closing', 'completed']) ? 'completed' : 'pending')
            ],
            'area_manager' => [
                'label' => 'Alan Amiri',
                'status' => $workPermit->status === 'pending_area_approval' ? 'current' :
                           (in_array($workPermit->status, ['pending_safety_approval', 'pending_employer_approval', 'approved', 'closing_requested', 'pending_area_closing', 'pending_safety_closing', 'pending_employer_closing', 'completed']) ? 'completed' : 'pending')
            ],
            'safety_specialist' => [
                'label' => 'İSG Uzmanı',
                'status' => $workPermit->status === 'pending_safety_approval' ? 'current' :
                           (in_array($workPermit->status, ['pending_employer_approval', 'approved', 'closing_requested', 'pending_area_closing', 'pending_safety_closing', 'pending_employer_closing', 'completed']) ? 'completed' : 'pending')
            ],
            'employer_representative' => [
                'label' => 'İşveren Vekili',
                'status' => $workPermit->status === 'pending_employer_approval' ? 'current' :
                           (in_array($workPermit->status, ['approved', 'closing_requested', 'pending_area_closing', 'pending_safety_closing', 'pending_employer_closing', 'completed']) ? 'completed' : 'pending')
            ],
            'work_in_progress' => [
                'label' => 'Çalışma',
                'status' => $workPermit->status === 'approved' ? 'current' :
                           (in_array($workPermit->status, ['closing_requested', 'pending_area_closing', 'pending_safety_closing', 'pending_employer_closing', 'completed']) ? 'completed' : 'pending')
            ]
        ];

        if($workPermit->isInClosingProcess() || $workPermit->status === 'completed') {
            $steps = array_merge($steps, [
                'closing_request' => [
                    'label' => 'Kapatma Talebi',
                    'status' => $workPermit->status === 'closing_requested' ? 'current' :
                               (in_array($workPermit->status, ['pending_area_closing', 'pending_safety_closing', 'pending_employer_closing', 'completed']) ? 'completed' : 'pending')
                ],
                'area_closing' => [
                    'label' => 'Alan Kapatma',
                    'status' => $workPermit->status === 'pending_area_closing' ? 'current' :
                               (in_array($workPermit->status, ['pending_safety_closing', 'pending_employer_closing', 'completed']) ? 'completed' : 'pending')
                ],
                'safety_closing' => [
                    'label' => 'İSG Kapatma',
                    'status' => $workPermit->status === 'pending_safety_closing' ? 'current' :
                               (in_array($workPermit->status, ['pending_employer_closing', 'completed']) ? 'completed' : 'pending')
                ],
                'employer_closing' => [
                    'label' => 'Final Onay',
                    'status' => $workPermit->status === 'pending_employer_closing' ? 'current' :
                               ($workPermit->status === 'completed' ? 'completed' : 'pending')
                ]
            ]);
        }
    @endphp

    @foreach($steps as $step)
        <div class="flex items-center">
            <div class="flex flex-col items-center">
                @php
                    $stepClasses = [
                        'completed' => 'bg-emerald-500 text-white',
                        'current' => 'bg-sky-500 text-white',
                        'pending' => 'bg-slate-300 text-slate-600'
                    ];
                    $stepClass = $stepClasses[$step['status']];
                @endphp
                <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $stepClass }}">
                    @if($step['status'] === 'completed')
                        <i class="fas fa-check text-xs"></i>
                    @else
                        <span class="text-xs">{{ $loop->iteration }}</span>
                    @endif
                </div>
                <span class="text-xs mt-1 text-slate-600">{{ $step['label'] }}</span>
            </div>
            @if(!$loop->last)
                @php
                    $lineClass = $step['status'] === 'completed' ? 'bg-emerald-500' : 'bg-slate-300';
                @endphp
                <div class="w-16 h-1 {{ $lineClass }}"></div>
            @endif
        </div>
    @endforeach
</div>
