<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    @page { size: A4; margin: 18mm 16mm; }
    body {
      font-family: "Times New Roman", "Times", "Liberation Serif", "DejaVu Serif", serif;
      font-size: 12pt;
      color: #111;
    }
    .small { font-size:9pt; color:#444; }
    .tiny { font-size:8.7pt; color:#444; }
    .hr { border-top:2px solid #000; margin: 0 0 10px; }
    .center { text-align: center; }
    .justify { text-align:justify; line-height:1.35; }
    .nowrap { white-space: nowrap; }
    .avoid { page-break-inside: avoid; }
    .meta { width:100%; border-collapse:collapse; margin-top: 6px; }
    .meta td { padding:1px 0; vertical-align:top; }
    .meta .k { width:34mm; }
    .meta .s { width:4mm; }
    .value { overflow-wrap: break-word; word-break: normal; text-align: justify; }
    .url { word-break: break-word; overflow-wrap: anywhere; }
    p { margin: 6px 0; }
    .pdf-header { width: 100%; border-collapse: collapse; margin: 0; }
    .pdf-header td { vertical-align: middle; text-align: center; }
    .pdf-header-logo img { object-fit: contain; width: auto; max-width: 100%; }
    .hdr-title { font-size: 18pt; font-weight: 700; line-height: 1.2; margin: 0; text-transform: uppercase; }
    .hdr-sub { font-size: 16pt; font-weight: 400; line-height: 1.2; margin: 0; }
    .hdr-meta { font-size: 9pt; line-height: 1.25; margin: 0; }
    .hdr-meta p { margin: 0; }
    .hdr-meta .mono { letter-spacing: 0.2px; }
    .footer-meta { position: fixed; left: 0; right: 0; bottom: 0; padding-top: 8px; border-top: 1px solid #9ca3af; font-size: 10pt; line-height: 1.18; color: #111; padding-left: 16mm; padding-right: 16mm; }
    .footer-meta p { margin: 0 0 1px 0; }
  </style>
</head>
<body>
<?php
  // Fungsi untuk membersihkan label role (Ketua:, Anggota 1:, dll)
  $cleanAuthorName = static function ($raw): string {
    $raw = trim((string) $raw);
    // Hapus label "Ketua:", "Anggota:", "Anggota 1:", dll
    $raw = preg_replace('/^(Ketua|Anggota(?:\s*\d*)?)\s*[:\-]\s*/iu', '', $raw);
    return trim($raw);
  };

  // Fungsi untuk membersihkan label afiliasi
  $cleanAffiliationText = static function ($raw): string {
    $raw = trim((string) $raw);
    // Hapus label "Afiliasi:", "UPT:", "Unit:", dll di awal
    $raw = preg_replace('/^(Afiliasi|Departemen|Unit|UPT|Institusi)\s*[:\-]\s*/iu', '', $raw);
    return trim($raw);
  };

  // Proses data penulis dari array JSON
  $authorNames = [];
  if (!empty($authors) && is_array($authors)) {
    foreach ($authors as $author) {
      $name = '';
      if (is_array($author)) {
        $name = isset($author['name']) ? $cleanAuthorName($author['name']) : '';
      } else {
        $name = $cleanAuthorName($author);
      }
      if ($name !== '') {
        $authorNames[] = $name;
      }
    }
  }
  
  // Format penulis dengan benar
  $authorsText = '-';
  $count = count($authorNames);
  if ($count === 1) {
    $authorsText = $authorNames[0];
  } elseif ($count === 2) {
    $authorsText = $authorNames[0] . ' dan ' . $authorNames[1];
  } elseif ($count > 2) {
    $last = array_pop($authorNames);
    $authorsText = implode(', ', $authorNames) . ', dan ' . $last;
  }
  
  // Proses afiliasi: hanya ambil dari penulis pertama (Ketua)
  $affText = '-';
  if (!empty($affiliations) && is_array($affiliations)) {
    // Ambil afiliasi pertama (dari Ketua)
    $firstAff = $affiliations[0] ?? null;
    if (!empty($firstAff)) {
      if (is_string($firstAff)) {
        $affText = $cleanAffiliationText($firstAff);
      } elseif (is_array($firstAff) && isset($firstAff['affiliation'])) {
        $affText = $cleanAffiliationText($firstAff['affiliation']);
      } elseif (is_array($firstAff) && isset($firstAff['name'])) {
        $affText = $cleanAffiliationText($firstAff['name']);
      }
    }
  }
  
  // Proses edisi
  $editionParts = [];
  if (!empty($letter['volume'])) {
    $editionParts[] = 'Volume ' . $letter['volume'];
  }
  if (!empty($letter['issue_number'])) {
    $editionParts[] = 'Nomor ' . $letter['issue_number'];
  }
  // Prioritas: published_year (input user) > published_at (tanggal sistem)
  if (!empty($letter['published_year'])) {
    $editionParts[] = (string) $letter['published_year'];
  } elseif (!empty($letter['published_at'])) {
    $editionParts[] = date('Y', strtotime((string) $letter['published_at']));
  }
  $editionText = !empty($editionParts) ? implode(', ', $editionParts) : 'edisi yang ditetapkan redaksi';
  
  // Pengaturan layout
  $publisherLogoHeightCm = 2.55;
  $journalLogoHeightCm = 2.95;
  $headerTextPt = (int) ($journal['pdf_header_title_pt'] ?? 18);
  $headerTextPt = max(12, min(24, $headerTextPt));
  $headerTitlePt = $headerTextPt;
  $headerSubPt = max(10, $headerTextPt - 2);
  $headerMetaPt = 9;
  $publisherPhone = trim((string) ($publisher['phone'] ?? '-'));
  $publisherEmail = trim((string) ($publisher['email'] ?? '-'));
  $eissnText = trim((string) ($journal['e_issn'] ?? ($journal['issn'] ?? '-')));
  $headerMetaText = 'HP: ' . ($publisherPhone !== '' ? $publisherPhone : '-') . ' ; E-Mail: ' . ($publisherEmail !== '' ? $publisherEmail : '-') . ' ; E-ISSN: ' . ($eissnText !== '' ? $eissnText : '-');
  
  $publisherNameRaw = trim((string) ($publisher['name'] ?? 'PUSAT LAYANAN PUBLIKASI ILMIAH'));
  $publisherNameLines = [$publisherNameRaw];
  
  $loaTitlePt = 18;
  $loaNumberPt = 12;
  $titleMarginTopPx = 34;
  $signatureMarginTopPx = (int) ($journal['pdf_signature_margin_top_px'] ?? 28);
  $overlayWidth = 300;
  $overlayHeight = 116;
  $sigLeft = (int) ($journal['pdf_sig_left_px'] ?? 28);
  $sigTop = (int) ($journal['pdf_sig_top_px'] ?? 0);
  $sigHeight = (int) ($journal['pdf_sig_height_px'] ?? 135);
  
  $journalName = trim((string) ($journal['name'] ?? '-'));
  $publisherAddress = trim((string) ($publisher['address'] ?? '-'));
  $city = trim((string) ($journal['city'] ?? 'Kupang'));
  $editorName = trim((string) ($journal['default_signer_name'] ?? '-'));
  $editorTitle = trim((string) ($journal['signer_position'] ?? 'Pimpinan Redaksi'));
  $editorNidn = trim((string) ($journal['editor_nidn'] ?? ''));
?>

  <!-- HEADER: logo kiri + teks tengah + logo kanan -->
  <table class="pdf-header avoid">
    <tr>
      <td width="15%">
        <div class="pdf-header-logo">
          <?php if (!empty($publisherLogoBase64)): ?>
            <img src="<?= esc((string) $publisherLogoBase64) ?>" style="height:<?= $publisherLogoHeightCm ?>cm; width:auto;">
          <?php endif; ?>
        </div>
      </td>
      <td width="70%">
        <div class="hdr-title" style="font-size: <?= $headerTitlePt ?>pt;">
          <?php foreach ($publisherNameLines as $line): ?>
            <div><?= esc($line) ?></div>
          <?php endforeach; ?>
        </div>
        <div class="hdr-sub" style="font-size: <?= $headerSubPt ?>pt;">
          <?= esc($journalName) ?>
        </div>
        <div class="hdr-meta" style="font-size: <?= $headerMetaPt ?>pt;">
          <p><?= esc($headerMetaText) ?></p>
        </div>
      </td>
      <td width="15%">
        <div class="pdf-header-logo">
          <?php if (!empty($logoBase64)): ?>
            <img src="<?= esc((string) $logoBase64) ?>" style="height:<?= $journalLogoHeightCm ?>cm; width:auto;">
          <?php endif; ?>
        </div>
      </td>
    </tr>
  </table>

  <div class="hr"></div>

  <!-- TITLE -->
  <div class="center avoid" style="margin-top:<?= $titleMarginTopPx ?>px; margin-bottom:12px; line-height:1.1;">
    <div style="font-weight:700; text-decoration: underline; font-size:<?= $loaTitlePt ?>pt;">Surat Keterangan Penerimaan (LoA)</div>
    <div style="font-weight:700; font-size:<?= $loaNumberPt ?>pt;">No: <?= esc((string) ($loaNumber ?? '-')) ?></div>
  </div>

  <div class="justify" style="margin-top:0;">
    Dengan ini, redaksi <b><?= esc($journalName) ?></b> memberitahukan bahwa naskah Anda dengan identitas berikut:
  </div>

  <!-- META -->
  <table class="meta avoid">
    <tr>
      <td class="k">Judul</td>
      <td class="s">:</td>
      <td class="value"><?= esc((string) ($letter['title'] ?? '-')) ?></td>
    </tr>
    <tr>
      <td class="k">Penulis</td>
      <td class="s">:</td>
      <td class="value"><?= esc($authorsText) ?></td>
    </tr>
    <tr>
      <td class="k">Afiliasi</td>
      <td class="s">:</td>
      <td class="value"><?= esc($affText) ?></td>
    </tr>
    <tr>
      <td class="k">Email</td>
      <td class="s">:</td>
      <td class="value"><?= esc((string) ($letter['corresponding_email'] ?? '-')) ?></td>
    </tr>
  </table>

  <div class="justify" style="margin-top:6px;">
    Telah melalui proses seleksi dan penelaahan sesuai standar dan kebijakan editorial yang berlaku.
    Berdasarkan hasil evaluasi tersebut, naskah dinyatakan <b>diterima</b> dan layak untuk dipublikasikan pada edisi
    <b><?= esc($editionText) ?></b>.
  </div>

  <div class="justify" style="margin-top:6px;">
    Sehubungan dengan prinsip etika publikasi ilmiah dan untuk menghindari duplikasi terbitan, kami mengharapkan agar naskah/artikel tersebut tidak dikirimkan maupun dipublikasikan pada jurnal atau penerbit lain.
  </div>

  <div class="justify" style="margin-top:6px;">
    Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya. Atas kepercayaan, partisipasi, dan kerja sama yang baik, kami sampaikan terima kasih.
  </div>

  <!-- SIGNATURE SECTION -->
  <div class="avoid" style="margin-top:<?= $signatureMarginTopPx ?>px;">
  <!-- SIGNATURE SECTION -->
  <div class="avoid" style="margin-top:<?= $signatureMarginTopPx ?>px; border-top:1px solid #ccc; padding-top:20px;">
    <table width="100%" style="border-collapse:collapse; margin:0; padding:0;">
      <tr>
        <!-- KOLOM KIRI: QR CODE -->
        <td width="35%" valign="top" style="padding-right:20px; text-align:left;">
          <!-- QR CODE & TEXT CONTAINER -->
          <div style="margin-bottom:15px;">
            <!-- QR CODE LEFT FLOAT -->
            <?php if (!empty($qrcodeBase64)): ?>
              <img src="<?= esc((string) $qrcodeBase64) ?>" style="width:100px; height:100px; border:1px solid #999; padding:4px; background:#fff; float:left; margin-right:10px; object-fit:contain;">
            <?php endif; ?>
            
            <!-- QR CODE TEXT RIGHT -->
            <div style="font-size:8pt; line-height:1.4; color:#666; padding-top:5px;">
              Keaslian LoA dapat<br>
              diperiksa dengan<br>
              memindai QR Code<br>
              di samping.
            </div>
            
            <div style="clear:both;"></div>
          </div>
          
          <!-- NOMOR LOA -->
          <div style="font-size:9pt; font-weight:700; text-align:left; color:#333; margin-top:10px;">No: <?= esc((string) ($loaNumber ?? '-')) ?></div>
        </td>

        <!-- KOLOM KANAN: TANDA TANGAN -->
        <td width="65%" valign="top" style="padding-left:30px;">
          <!-- KOTA & TANGGAL -->
          <div style="font-size:11pt; margin-bottom:20px; font-weight:400; text-align:right;">
            <?= esc($city) ?>, <?= esc((string) ($issuedDate ?? '')) ?>
          </div>
          
          <!-- JABATAN -->
          <div style="font-size:11pt; margin-bottom:50px; font-weight:500; text-align:center;">
            <?= esc($editorTitle) ?>,
          </div>
          
          <!-- TANDA TANGAN & STEMPEL -->
          <div style="position:relative; width:100%; height:<?= $overlayHeight ?>px; margin-bottom:20px; margin-top:10px;">
            <?php if (!empty($sigBase64)): ?>
              <img
                src="<?= esc((string) $sigBase64) ?>"
                style="position:absolute; left:50%; transform:translateX(-50%); top:0; height:<?= $sigHeight ?>px; width:auto; z-index:2; object-fit:contain;"
              >
            <?php endif; ?>
          </div>
          
          <!-- NAMA & GELAR -->
          <div style="font-size:11pt; font-weight:700; line-height:1.4; text-align:center;">
            <div><?= esc($editorName) ?></div>
            <?php if (!empty($editorNidn)): ?>
              <div style="font-size:9pt; font-weight:400; margin-top:3px;">NIDN. <?= esc($editorNidn) ?></div>
            <?php endif; ?>
          </div>
        </td>
      </tr>
    </table>
  </div>

  <div class="footer-meta avoid">
    <p><b>Kontak Redaksi</b></p>
    <p>Email: <?= esc($publisherEmail) ?></p>
    <p>Whatsapp: <?= esc($publisherPhone) ?></p>
    <p>Alamat: <?= esc($publisherAddress) ?></p>
  </div>
</body>
</html>
