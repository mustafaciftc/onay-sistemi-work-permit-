<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>İş İzni Onaylandı</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #28a745; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
        .info-item { margin-bottom: 10px; }
        .info-label { font-weight: bold; color: #555; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ İş İzniniz Onaylandı</h1>
        </div>

        <div class="content">
            <p>Sayın {{ $user->name ?? 'Kullanıcı' }},</p>

            <p><strong>{{ $workPermit->permit_code ?? 'Bilinmeyen' }}</strong> numaralı iş izniniz onaylanmıştır.</p>

            <h3>İş İzni Bilgileri:</h3>
            <div class="info-item">
                <span class="info-label">İş Başlığı:</span> {{ $workPermit->title ?? '—' }}
            </div>
            <div class="info-item">
                <span class="info-label">Çalışan:</span> {{ $workPermit->worker_name ?? '—' }}
            </div>
            <div class="info-item">
                <span class="info-label">Lokasyon:</span> {{ $workPermit->location ?? '—' }}
            </div>
            <div class="info-item">
                <span class="info-label">Başlangıç:</span> {{ isset($workPermit->start_date) ? $workPermit->start_date->format('d.m.Y H:i') : '—' }}
            </div>
            <div class="info-item">
                <span class="info-label">Bitiş:</span> {{ isset($workPermit->end_date) ? $workPermit->end_date->format('d.m.Y H:i') : '—' }}
            </div>

            <p style="margin-top: 20px;">Detaylı bilgi için ekteki PDF belgesini inceleyebilirsiniz.</p>
        </div>

        <div class="footer">
            <p><em>İş İzni Sistemi</em></p>
            <p><small>Email gönderilme tarihi: {{ $approvalDate ?? now()->format('d.m.Y H:i') }}</small></p>
        </div>
    </div>
</body>
</html>
