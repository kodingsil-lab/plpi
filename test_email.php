<?php

$smtpHost = '127.0.0.1';
$smtpPort = 1025;

echo "Testing raw SMTP connection to {$smtpHost}:{$smtpPort}\n";

$smtp = @fsockopen($smtpHost, $smtpPort, $errno, $errstr, 5);

if (! $smtp) {
    echo "FAIL: Cannot connect to MailHog SMTP: {$errstr} ({$errno})\n";
    exit(1);
}

stream_set_timeout($smtp, 2);
$banner = fgets($smtp, 512);
$meta = stream_get_meta_data($smtp);
fclose($smtp);

echo "OK: MailHog is reachable\n";
if ($banner !== false) {
    echo 'Banner: ' . trim($banner) . "\n\n";
} elseif (! empty($meta['timed_out'])) {
    echo "Banner: (connected, but SMTP banner timed out)\n\n";
} else {
    echo "Banner: (connected, no banner received)\n\n";
}
echo "Application config should match this:\n";
echo "- email.protocol = 'smtp'\n";
echo "- email.host = '127.0.0.1'\n";
echo "- email.port = 1025\n";
echo "- email.username = ''\n";
echo "- email.password = ''\n";
echo "- email.crypto = ''\n";
