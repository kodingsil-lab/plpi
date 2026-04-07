<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { size: A4; margin: 18mm 16mm; }
        body {
            font-family: "Times New Roman", "Times", "Liberation Serif", serif;
            font-size: 12pt;
            color: #111;
        }
        .small { font-size: 9pt; color: #444; }
        .tiny { font-size: 8.7pt; color: #444; }
        .hr { border-top: 2px solid #000; margin: 0 0 10px; }
        .center { text-align: center; }
        .justify { text-align: justify; line-height: 1.35; }
        .nowrap { white-space: nowrap; }
        .avoid { page-break-inside: avoid; }
        .meta { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .meta td { padding: 1px 0; vertical-align: top; }
        .meta .k { width: 34mm; }
        .meta .s { width: 4mm; }
        .pdf-header { width: 100%; border-collapse: collapse; margin: 0; }
        .pdf-header td { vertical-align: middle; text-align: center; }
        .hdr-title { font-size: 18pt; font-weight: 700; line-height: 1.2; margin: 0; text-transform: uppercase; }
        .hdr-sub { font-size: 16pt; font-weight: 400; line-height: 1.2; margin: 0; }
        .hdr-meta { font-size: 9pt; line-height: 1.25; margin: 0; }
        .hdr-meta p { margin: 0; }
        .footer-meta { position: fixed; left: 0; right: 0; bottom: 0; padding-top: 8px; border-top: 1px solid #9ca3af; font-size: 10pt; line-height: 1.18; color: #111; }
        .footer-meta p { margin: 0 0 1px 0; }
    </style>
</head>
<body>
<?php
$publisherName = trim((string) ($publisher['name'] ?? 'PUSAT LAYANAN PUBLIKASI ILMIAH'));
$journalName = trim((string) ($journal['name'] ?? '-'));
$publisherPhone = trim((string) ($publisher['phone'] ?? '-'));
$publisherEmail = trim((string) ($publisher['email'] ?? '-'));
$eissnText = trim((string) ($journal['e_issn'] ?? ($journal['issn'] ?? '-')));
$websiteUrl = trim((string) ($journal['website_url'] ?? '-'));

$authorItems = [];
foreach (($authors ?? []) as $a) {
    $raw = trim((string) (is_array($a) ? ($a['name'] ?? '') : $a));
    if ($raw !== '') {
        $parts = preg_split('/\s*,\s*/', $raw) ?: [];
        foreach ($parts as $p) {
            $p = trim((string) $p);
            if ($p !== '') {
                $authorItems[] = $p;
            }
        }
    }
}
$authorItems = array_values(array_unique($authorItems));
$authorsText = '-';
if (count($authorItems) === 1) {
    $authorsText = $authorItems[0];
} elseif (count($authorItems) === 2) {
    $authorsText = $authorItems[0] . ' dan ' . $authorItems[1];
} elseif (count($authorItems) > 2) {
    $last = array_pop($authorItems);
    $authorsText = implode(', ', $authorItems) . ', dan ' . $last;
}

$aff = array_values(array_filter(array_map(static fn($x) => trim((string) $x), $affiliations ?? [])));
$affText = $aff ? implode(', ', $aff) : '-';

$editionParts = [];
if (! empty($letter['volume'])) { $editionParts[] = 'Volume ' . $letter['volume']; }
if (! empty($letter['issue_number'])) { $editionParts[] = 'Nomor ' . $letter['issue_number']; }
if (! empty($letter['published_at'])) {
    $editionParts[] = date('Y', strtotime((string) $letter['published_at']));
} elseif (! empty($letter['published_year'])) {
    $editionParts[] = (string) $letter['published_year'];
}
$editionText = ! empty($editionParts) ? implode(', ', $editionParts) : 'edisi yang ditetapkan redaksi';
?>

<table class="pdf-header avoid">
    <tr>
        <td width="15%">
            <?php if (! empty($publisherLogoBase64)): ?>
                <img src="<?= esc((string) $publisherLogoBase64) ?>" style="height:2.55cm; width:auto;">
            <?php endif; ?>
        </td>
        <td width="70%">
            <div class="hdr-title"><?= esc($publisherName) ?></div>
            <div class="hdr-sub"><?= esc($journalName) ?></div>
            <div class="hdr-meta">
                <p>HP: <?= esc($publisherPhone) ?> ; E-Mail: <?= esc($publisherEmail) ?> ; E-ISSN: <?= esc($eissnText) ?></p>
            </div>
        </td>
        <td width="15%">
            <?php if (! empty($logoBase64)): ?>
                <img src="<?= esc((string) $logoBase64) ?>" style="height:2.95cm; width:auto;">
            <?php endif; ?>
        </td>
    </tr>
</table>

<div class="hr"></div>

<div class="center avoid" style="margin-top:34px; margin-bottom:12px; line-height:1.1;">
    <div style="font-weight:700; text-decoration: underline; font-size:18pt;">Letter of Acceptance (LoA)</div>
    <div style="font-weight:700; font-size:12pt;">No: <?= esc((string) ($loaNumber ?? '-')) ?></div>
</div>

<div class="justify">
    Dengan ini, redaksi <b><?= esc($journalName) ?></b> memberitahukan bahwa naskah Anda dengan identitas berikut:
</div>

<table class="meta avoid">
    <tr><td class="k">Judul</td><td class="s">:</td><td><?= esc((string) ($letter['title'] ?? '-')) ?></td></tr>
    <tr><td class="k">Penulis</td><td class="s">:</td><td><?= esc($authorsText) ?></td></tr>
    <tr><td class="k">Afiliasi</td><td class="s">:</td><td><?= esc($affText) ?></td></tr>
    <tr><td class="k">Email</td><td class="s">:</td><td><?= esc((string) ($letter['corresponding_email'] ?? '-')) ?></td></tr>
</table>

<div class="justify" style="margin-top:6px;">
    Telah melalui proses seleksi dan penelaahan (review) sesuai standar dan kebijakan editorial yang berlaku.
    Berdasarkan hasil evaluasi tersebut, naskah dinyatakan <b>diterima (accepted)</b> dan layak untuk dipublikasikan pada edisi
    <b><?= esc($editionText) ?></b>.
</div>

<div class="justify" style="margin-top:6px;">
    Sehubungan dengan prinsip etika publikasi ilmiah dan untuk menghindari duplikasi terbitan, kami mengharapkan agar naskah/artikel tersebut tidak dikirimkan maupun dipublikasikan pada jurnal atau penerbit lain.
</div>

<div class="justify" style="margin-top:6px;">
    Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya. Atas kepercayaan, partisipasi, dan kerja sama yang baik, kami sampaikan terima kasih.
</div>

<div class="avoid" style="margin-top:28px;">
    <table width="100%" style="border-collapse:collapse;">
        <tr>
            <td width="40%" valign="top">
                <div style="font-weight:700; margin-bottom:6px;">Verifikasi</div>
                <div class="tiny">Keaslian LoA dapat diperiksa melalui:</div>
                <div class="small" style="margin-top:4px; word-break:break-word;"><?= esc((string) ($verifyUrl ?? '-')) ?></div>
                <div class="tiny" style="margin-top:4px; font-weight:700;">No: <?= esc((string) ($loaNumber ?? '-')) ?></div>
            </td>
            <td width="60%" valign="top" style="padding-left:88px;">
                <div class="nowrap"><?= esc((string) ($journal['city'] ?? 'Kupang')) ?>, <?= esc((string) ($issuedDate ?? '')) ?></div>
                <div class="nowrap"><?= esc((string) ($journal['default_signer_title'] ?? 'Editor in Chief')) ?>,</div>
                <div style="height:12px;"></div>
                <div style="position:relative; width:300px; height:116px; margin:0;">
                    <?php if (! empty($sigBase64)): ?>
                        <img src="<?= esc((string) $sigBase64) ?>" style="position:absolute; left:<?= esc((string) ($journal['pdf_sig_left_px'] ?? 28)) ?>px; top:<?= esc((string) ($journal['pdf_sig_top_px'] ?? 0)) ?>px; height:<?= esc((string) ($journal['pdf_sig_height_px'] ?? 135)) ?>px; width:auto; z-index:2;">
                    <?php endif; ?>
                </div>
                <div style="font-weight:700; margin-top:-10px;" class="nowrap"><?= esc((string) ($journal['default_signer_name'] ?? '-')) ?></div>
            </td>
        </tr>
    </table>
</div>

<div class="footer-meta avoid">
    <p><b>Kontak Redaksi</b></p>
    <p>Email: <?= esc($publisherEmail) ?></p>
    <p>Whatsapp: <?= esc($publisherPhone) ?></p>
    <p>Alamat: <?= esc($websiteUrl !== '' ? $websiteUrl : '-') ?></p>
</div>
</body>
</html>
