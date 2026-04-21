<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail = 'noreply@plpi.id';
    public string $fromName = 'PLPI - Sistem Manajemen LoA';
    public string $recipients = '';
    public string $userAgent = 'CodeIgniter';
    public string $protocol = 'smtp';
    public string $mailPath = '/usr/sbin/sendmail';
    public string $SMTPHost = '127.0.0.1';
    public string $SMTPAuthMethod = 'login';
    public string $SMTPUser = '';
    public string $SMTPPass = '';
    public int $SMTPPort = 1025;
    public int $SMTPTimeout = 5;
    public bool $SMTPKeepAlive = false;
    public string $SMTPCrypto = '';
    public bool $wordWrap = true;
    public int $wrapChars = 76;
    public string $mailType = 'html';
    public string $charset = 'UTF-8';
    public bool $validate = false;
    public int $priority = 3;
    public string $CRLF = "\r\n";
    public string $newline = "\r\n";
    public bool $BCCBatchMode = false;
    public int $BCCBatchSize = 200;
    public bool $DSN = false;

    public function __construct()
    {
        parent::__construct();

        $this->protocol = (string) env('email.protocol', $this->protocol);
        $this->SMTPHost = (string) env('email.host', $this->SMTPHost);
        $this->SMTPPort = (int) env('email.port', $this->SMTPPort);
        $this->SMTPUser = (string) env('email.username', $this->SMTPUser);
        $this->SMTPPass = (string) env('email.password', $this->SMTPPass);
        $this->SMTPCrypto = (string) env('email.crypto', $this->SMTPCrypto);
        $this->SMTPTimeout = (int) env('email.timeout', $this->SMTPTimeout);
        $this->fromEmail = (string) env('email.from_email', $this->fromEmail);
        $this->fromName = (string) env('email.from_name', $this->fromName);

        // MailHog lokal tidak membutuhkan autentikasi SMTP.
        if ($this->SMTPHost === 'localhost' || $this->SMTPHost === '127.0.0.1') {
            $this->SMTPUser = trim($this->SMTPUser);
            $this->SMTPPass = trim($this->SMTPPass);
            $this->SMTPCrypto = trim($this->SMTPCrypto);
        }
    }
}
