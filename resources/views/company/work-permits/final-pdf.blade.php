<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İş İzni - {{ $workPermit->permit_code }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 20px;
        }
        .section {
            margin-bottom: 15px;
        }
        .section-title {
            background-color: #f8f9fa;
            padding: 5px 10px;
            border-left: 4px solid #007bff;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-bottom: 10px;
        }
        .info-item {
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        table th {
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>İŞ İZNİ BELGESİ</h1>
        <div>Belge No: {{ $workPermit->permit_code }}</div>
    </div>

    <div class="section">
        <div class="section-title">Temel Bilgiler</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">İş Başlığı:</span>
                <span>{{ $workPermit->title }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Çalışan:</span>
                <span>{{ $workPermit->worker_name }} - {{ $workPermit->worker_position }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">İş Türü:</span>
                <span>{{ $workPermit->work_type }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Lokasyon:</span>
                <span>{{ $workPermit->location }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Başlangıç:</span>
                <span>{{ \Carbon\Carbon::parse($workPermit->start_date)->format('d.m.Y H:i') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Bitiş:</span>
                <span>{{ \Carbon\Carbon::parse($workPermit->end_date)->format('d.m.Y H:i') }}</span>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">İş Tanımı</div>
        <div>{{ $workPermit->work_description }}</div>
    </div>

    <div class="footer">
        <p>Belge oluşturulma tarihi: {{ $currentDate }}</p>
        <p>Belge No: {{ $workPermit->permit_code }}</p>
    </div>
</body>
</html>
