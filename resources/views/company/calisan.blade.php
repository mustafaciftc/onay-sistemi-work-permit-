
@section('title', 'Çalışan Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Hoş Geldiniz, {{ Auth::user()->name }}</h1>
        <span class="badge badge-secondary">Çalışan</span>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Toplam İzinlerim
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['my_permits'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Onay Bekleyen
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_permits'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Onaylanan İzinler
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['approved_permits'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- İş İzinlerim Tablosu -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">İş İzinlerim</h6>
            <a href="{{ route('company.work-permits.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Yeni İş İzni
            </a>
        </div>
        <div class="card-body">
            @if($myWorkPermits->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>İzin Kodu</th>
                                <th>Başlık</th>
                                <th>Departman</th>
                                <th>Durum</th>
                                <th>Oluşturulma</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myWorkPermits as $permit)
                            <tr>
                                <td>{{ $permit->permit_code }}</td>
                                <td>{{ $permit->title }}</td>
                                <td>{{ $permit->department->name }}</td>
                                <td>
                                    @if($permit->status === 'approved')
                                        <span class="badge badge-success">Onaylandı</span>
                                    @elseif($permit->status === 'rejected')
                                        <span class="badge badge-danger">Reddedildi</span>
                                    @else
                                        <span class="badge badge-warning">Onay Bekliyor</span>
                                    @endif
                                </td>
                                <td>{{ $permit->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.work-permits.show', $permit) }}"
                                       class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Detay
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Henüz iş izni oluşturmadınız.</p>
                    <a href="{{ route('company.work-permits.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> İlk İş İznini Oluştur
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
