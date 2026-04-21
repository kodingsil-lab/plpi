<?php
/**
 * Email Template untuk Notifikasi Letter of Acceptance (LoA)
 * 
 * @var array $letter Data surat LoA
 * @var string $journalName Nama jurnal
 * @var string $editorName Nama editor
 * @var string $journalUrl URL jurnal
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: "Times New Roman", "Times", serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #0066cc;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #0066cc;
            margin: 0 0 5px 0;
            font-size: 24px;
        }
        .journal-name {
            color: #666;
            font-size: 14px;
            margin: 0;
        }
        .journal-link {
            display: inline-block;
            margin-top: 6px;
            font-size: 13px;
            color: #0066cc;
            text-decoration: none;
            word-break: break-all;
        }
        .content {
            margin: 20px 0;
        }
        .content p {
            margin: 10px 0;
            text-align: justify;
        }
        .loa-details {
            background: #f5f5f5;
            padding: 15px;
            border-left: 4px solid #0066cc;
            margin: 20px 0;
            font-size: 14px;
        }
        .loa-details .detail-row {
            margin: 8px 0;
            display: flex;
        }
        .loa-details .label {
            font-weight: bold;
            width: 120px;
            color: #0066cc;
        }
        .loa-details .value {
            flex: 1;
            word-break: break-word;
        }
        .footer {
            border-top: 1px solid #ddd;
            padding-top: 20px;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
        .signature {
            margin-top: 20px;
            padding-top: 20px;
        }
        .best-regards {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Letter of Acceptance (LoA)</h1>
        <p class="journal-name"><?= esc($journalName ?? 'Jurnal') ?></p>
    </div>

    <div class="content">
        <p>Dengan hormat,</p>

        <p>Kami dengan senang hati mengumumkan bahwa naskah Anda telah melalui proses review dan diterima untuk dipublikasikan di <?= esc($journalName ?? 'jurnal kami') ?>.</p>

        <p>Berikut adalah rincian naskah Anda:</p>

        <div class="loa-details">
            <div class="detail-row">
                <div class="label">Nomor LoA:</div>
                <div class="value"><strong><?= esc($letter['loa_number'] ?? '-') ?></strong></div>
            </div>
            <div class="detail-row">
                <div class="label">Judul:</div>
                <div class="value"><?= esc($letter['title'] ?? '-') ?></div>
            </div>
            <?php if (!empty($authors)): ?>
            <div class="detail-row">
                <div class="label">Penulis:</div>
                <div class="value"><?= esc($authors) ?></div>
            </div>
            <?php endif; ?>
            <?php if (!empty($letter['published_at'])): ?>
            <div class="detail-row">
                <div class="label">Tanggal Terbit:</div>
                <div class="value"><?= esc(date('d F Y', strtotime($letter['published_at']))) ?></div>
            </div>
            <?php endif; ?>
        </div>

        <p>Letter of Acceptance (LoA) dalam format PDF telah kami sertakan sebagai lampiran email ini. Anda dapat mengunduh dan menyimpannya untuk keperluan administratif.</p>

        <p>Untuk verifikasi keaslian Letter of Acceptance (LoA), Anda dapat memindai QR Code yang terdapat di dokumen PDF atau mengunjungi halaman verifikasi kami dengan menggunakan nomor LoA di atas.</p>

        <p>Terima kasih atas kontribusi berharga Anda. Kami sangat menghargai kesempatan untuk menerbitkan karya berkualitas ini.</p>

        <div class="signature">
            <div class="best-regards">Salam hormat,</div>
            <p><strong><?= esc($editorName ?? 'Pimpinan Redaksi') ?></strong></p>
            <p><?= esc($journalName ?? 'Jurnal') ?></p>
            <?php if (! empty($journalUrl ?? '')): ?>
            <p>
                <a class="journal-link" href="<?= esc($journalUrl) ?>" target="_blank" rel="noopener noreferrer"><?= esc($journalUrl) ?></a>
            </p>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">
        <p>Harap tidak membalas email otomatis ini. Jika Anda memiliki pertanyaan atau membutuhkan bantuan, silakan hubungi kami melalui kontak redaksi yang tersedia.</p>
        <p><em>Email ini dikirim secara otomatis oleh sistem manajemen publikasi ilmiah kami.</em></p>
    </div>
</body>
</html>
