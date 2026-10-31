<?php

namespace App\Modules\SuperAdmin\Terminal\Services;

use App\Models\AuditLog;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;

class TerminalService
{
    /**
     * Allowed command prefixes (Framework, Composer, Git, and full Linux / Server management).
     */
    protected array $allowedPrefixes = [
        // Framework & Runtimes
        'php artisan',
        'composer',
        'php',
        'git',

        // Linux & Server File Inspection & Listing
        'ls',
        'dir',
        'pwd',
        'cat',
        'head',
        'tail',
        'grep',
        'find',
        'which',
        'where',
        'wc',
        'diff',
        'file',

        // File Manipulation & Management
        'rm',
        'del',
        'cp',
        'copy',
        'mv',
        'move',
        'mkdir',
        'md',
        'rmdir',
        'rd',
        'touch',

        // Linux Permissions & Ownership
        'chmod',
        'chown',
        'chgrp',

        // System, Memory & Disk Utilities
        'df',
        'du',
        'free',
        'uptime',
        'whoami',
        'id',
        'uname',
        'ps',
        'top',
        'date',
        'cal',

        // Compression & Archives
        'zip',
        'unzip',
        'tar',
        'gzip',
        'gunzip',

        // Networking & Web Downloads
        'curl',
        'wget',
        'ping',

        // Shell Utilities & Text Processing
        'echo',
        'clear',
        'cls',
        'sed',
        'awk',
        'sort',
        'uniq',
        'tr',
        'cut',
        'export',

        // Built-in Web Text Editors
        'nano',
        'vi',
        'vim',
    ];

    /**
     * Blocked interactive or dangerous commands.
     */
    protected array $blockedPatterns = [
        'tinker',
        'less',
        'more',
        'tail -f',
        'tail --follow',
        'rm -rf /',
        'rm -rf /*',
        'rm -fr /',
        'rm -fr /*',
        'rm -rf ~',
        'rm -rf .',
        'rm -rf *',
        ':(){ :|:& };:',
        'rmdir /s /q c:',
        'del /f /s /q c:',
        'format ',
        'mkfs',
        'dd if=',
        'shutdown',
        'reboot',
        'poweroff',
        'init 0',
        'init 6',
        'halt',
        'ssh ',
        'telnet ',
    ];

    public function isCommandAllowed(string $command): bool
    {
        $trimmed = trim($command);

        // Check against blocked dangerous patterns first
        $lower = strtolower($trimmed);
        foreach ($this->blockedPatterns as $blocked) {
            if (str_contains($lower, $blocked)) {
                return false;
            }
        }

        // Check if starts with an allowed prefix
        foreach ($this->allowedPrefixes as $prefix) {
            if ($trimmed === $prefix || str_starts_with($trimmed, $prefix . ' ') || str_starts_with($trimmed, $prefix . ' -')) {
                return true;
            }
        }

        return false;
    }

    public function getPhpBinary(): string
    {
        $finder = new PhpExecutableFinder();
        $php = $finder->find(false);
        if ($php && file_exists($php)) {
            return $php;
        }

        if (defined('PHP_BINARY') && file_exists(PHP_BINARY)) {
            return PHP_BINARY;
        }

        return 'php';
    }

    public function getComposerBinary(): string
    {
        $userProfile = getenv('USERPROFILE') ?: 'C:/Users/' . (get_current_user() ?: 'Alvin');
        $appData = getenv('APPDATA') ?: ($userProfile . '/AppData/Roaming');

        $candidates = [
            'C:/laragon/bin/composer/composer.bat',
            'C:/laragon/bin/composer/composer.phar',
            'C:/ProgramData/ComposerSetup/bin/composer.bat',
            $appData . '/Composer/vendor/bin/composer.bat',
        ];

        foreach ($candidates as $candidate) {
            if ($candidate && file_exists($candidate)) {
                return $candidate;
            }
        }

        return 'composer';
    }

    protected function prepareExecutableCommand(string $command, bool $confirmed = false): string
    {
        $trimmed = trim($command);
        $phpBinary = $this->getPhpBinary();
        $composerBinary = $this->getComposerBinary();

        // If confirmed execution of artisan command in production/destructive, append --force
        if ($confirmed && str_starts_with($trimmed, 'php artisan') && !str_contains($trimmed, '--force')) {
            $trimmed .= ' --force';
        }

        // 1. If starts with php
        if ($trimmed === 'php') {
            return '"' . $phpBinary . '"';
        }
        if (str_starts_with($trimmed, 'php ')) {
            return '"' . $phpBinary . '" ' . substr($trimmed, 4);
        }

        // 2. If starts with composer
        if ($trimmed === 'composer') {
            if (str_ends_with(strtolower($composerBinary), '.phar')) {
                return '"' . $phpBinary . '" "' . $composerBinary . '" --no-interaction';
            }
            return '"' . $composerBinary . '" --no-interaction';
        }
        if (str_starts_with($trimmed, 'composer ')) {
            $rest = substr($trimmed, 9);
            if (!str_contains($rest, '--no-interaction')) {
                $rest .= ' --no-interaction';
            }
            if (str_ends_with(strtolower($composerBinary), '.phar')) {
                return '"' . $phpBinary . '" "' . $composerBinary . '" ' . $rest;
            }
            return '"' . $composerBinary . '" ' . $rest;
        }

        return $trimmed;
    }

    public function isConfirmableCommand(string $command): bool
    {
        $lower = strtolower(trim($command));

        // Always require confirmation for destructive database commands in any environment
        if (str_contains($lower, 'migrate:fresh') || str_contains($lower, 'db:wipe') || str_contains($lower, 'migrate:reset')) {
            return true;
        }

        // In production environment, all migration and seeding require confirmation
        $isProduction = app()->environment('production') || config('app.env') === 'production';
        if ($isProduction) {
            if (str_starts_with($lower, 'php artisan migrate') || str_starts_with($lower, 'php artisan db:seed')) {
                return true;
            }
        }

        return false;
    }

    protected function buildEnvironment(): array
    {
        $phpDir = dirname($this->getPhpBinary());
        $composerDir = 'C:\\laragon\\bin\\composer';
        $gitUsrBin = 'C:\\Program Files\\Git\\usr\\bin';
        $gitBin = 'C:\\Program Files\\Git\\bin';
        $gitMingw = 'C:\\Program Files\\Git\\mingw64\\bin';
        $gitCmd = 'C:\\Program Files\\Git\\cmd';

        $currentPath = getenv('PATH') ?: ($_SERVER['PATH'] ?? $_SERVER['Path'] ?? '');
        $newPath = $phpDir . ';' . $composerDir . ';' . $gitUsrBin . ';' . $gitBin . ';' . $gitMingw . ';' . $gitCmd . ';' . $currentPath;

        // Resolve user directory and AppData for Windows
        $userProfile = getenv('USERPROFILE') ?: ($_SERVER['USERPROFILE'] ?? 'C:\\Users\\' . (get_current_user() ?: 'Alvin'));
        if (!file_exists($userProfile)) {
            $userProfile = 'C:\\Users\\Alvin';
        }

        $appData = getenv('APPDATA') ?: ($_SERVER['APPDATA'] ?? ($userProfile . '\\AppData\\Roaming'));
        $localAppData = getenv('LOCALAPPDATA') ?: ($_SERVER['LOCALAPPDATA'] ?? ($userProfile . '\\AppData\\Local'));
        $composerHome = getenv('COMPOSER_HOME') ?: ($_SERVER['COMPOSER_HOME'] ?? ($appData . '\\Composer'));

        if (!file_exists($composerHome)) {
            @mkdir($composerHome, 0777, true);
        }

        return array_merge($_SERVER, [
            'PATH' => $newPath,
            'Path' => $newPath,
            'APPDATA' => $appData,
            'USERPROFILE' => $userProfile,
            'LOCALAPPDATA' => $localAppData,
            'COMPOSER_HOME' => $composerHome,
            'HOME' => $userProfile,
            'HOMEDRIVE' => getenv('HOMEDRIVE') ?: 'C:',
            'HOMEPATH' => getenv('HOMEPATH') ?: ('\\Users\\' . (get_current_user() ?: 'Alvin')),
            'SystemRoot' => getenv('SystemRoot') ?: 'C:\\WINDOWS',
            'WINDIR' => getenv('WINDIR') ?: 'C:\\WINDOWS',
            'ComSpec' => getenv('ComSpec') ?: 'C:\\WINDOWS\\system32\\cmd.exe',
            'PATHEXT' => getenv('PATHEXT') ?: '.COM;.EXE;.BAT;.CMD;.VBS;.VBE;.JS;.JSE;.WSF;.WSH;.MSC',
            'TEMP' => getenv('TEMP') ?: sys_get_temp_dir(),
            'TMP' => getenv('TMP') ?: sys_get_temp_dir(),
            'COMPOSER_MEMORY_LIMIT' => '-1',
            'COMPOSER_ALLOW_SUPERUSER' => '1',
            'COMPOSER_DISCARD_CHANGES' => '1',
        ]);
    }

    public function execute(string $command, bool $confirmed = false): array
    {
        $trimmed = trim($command);

        // 1. Intercept clear / cls command
        if ($trimmed === 'clear' || $trimmed === 'cls') {
            return [
                'success' => true,
                'action' => 'clear_terminal',
                'command' => $trimmed,
                'output' => '',
                'exit_code' => 0,
                'duration_ms' => 0,
                'timestamp' => now()->format('H:i:s'),
            ];
        }

        // 2. Intercept nano / vi / vim for built-in Web Editor
        if (preg_match('/^(?:nano|vi|vim)\b\s*(.*)$/i', $trimmed, $matches)) {
            return $this->handleNanoEditor($trimmed, trim($matches[1] ?? ''));
        }

        // 3. Check for production / destructive commands requiring confirmation
        if (!$confirmed && !str_contains($trimmed, '--force') && $this->isConfirmableCommand($trimmed)) {
            $isProd = app()->environment('production') || config('app.env') === 'production';
            $title = $isProd ? 'Application In Production!' : 'Konfirmasi Tindakan Berbahaya!';
            $warning = (str_contains(strtolower($trimmed), 'migrate:fresh') || str_contains(strtolower($trimmed), 'db:wipe'))
                ? "PERINGATAN KRITIS: Perintah `{$trimmed}` akan MENGHAPUS SEMUA TABEL & DATA pada database server!"
                : "Aplikasi sedang berjalan di mode PRODUCTION! Perintah `{$trimmed}` akan memodifikasi struktur/data database server!";

            return [
                'success' => false,
                'requires_confirmation' => true,
                'action' => 'confirm_prompt',
                'command' => $trimmed,
                'title' => $title,
                'warning' => $warning,
                'prompt' => 'Do you really wish to run this command? (yes/no)',
                'output' => "**************************************\n*     {$title}     *\n**************************************\n\n{$warning}\n\nDo you really wish to run this command? (yes/no)",
                'exit_code' => 0,
                'duration_ms' => 0,
                'timestamp' => now()->format('H:i:s'),
            ];
        }

        if (!$this->isCommandAllowed($trimmed)) {
            return [
                'success' => false,
                'command' => $trimmed,
                'output' => "Akses Ditolak: Perintah ini dibatasi demi keamanan server.\nPerintah yang didukung meliputi:\n- Laravel & PHP: php artisan ..., composer ..., php ...\n- Linux & Server: ls, cat, nano, rm, cp, mv, mkdir, pwd, grep, head, tail, chmod, df, du, free, ps, curl, tar, zip, dll.\n- Git: git status, git log, git pull, dll.",
                'exit_code' => 126,
                'duration_ms' => 0,
                'timestamp' => now()->format('H:i:s'),
            ];
        }

        $execCommand = $this->prepareExecutableCommand($trimmed, $confirmed);
        $environment = $this->buildEnvironment();

        @set_time_limit(300);
        $startTime = microtime(true);

        try {
            $process = Process::fromShellCommandline($execCommand, base_path(), $environment);

            // Timeout 300 detik (5 menit)
            $process->setTimeout(300);
            $process->run();

            $durationMs = (int) round((microtime(true) - $startTime) * 1000);
            $exitCode = $process->getExitCode() ?? 0;
            $output = $process->getOutput();
            $errorOutput = $process->getErrorOutput();

            $finalOutput = $output;
            if (!empty($errorOutput)) {
                $finalOutput .= ($finalOutput ? "\n" : '') . $errorOutput;
            }

            if (empty(trim($finalOutput))) {
                $finalOutput = "(Perintah selesai tanpa output teks - Exit Code: {$exitCode})";
            }

            // Log to AuditLog
            $this->logCommandAudit($trimmed, $exitCode, $durationMs);

            return [
                'success' => $exitCode === 0,
                'command' => $trimmed,
                'output' => $finalOutput,
                'exit_code' => $exitCode,
                'duration_ms' => $durationMs,
                'timestamp' => now()->format('H:i:s'),
            ];
        } catch (\Throwable $e) {
            $durationMs = (int) round((microtime(true) - $startTime) * 1000);

            $this->logCommandAudit($trimmed, 1, $durationMs);

            return [
                'success' => false,
                'command' => $trimmed,
                'output' => "Terjadi kesalahan saat mengeksekusi perintah:\n" . $e->getMessage(),
                'exit_code' => 1,
                'duration_ms' => $durationMs,
                'timestamp' => now()->format('H:i:s'),
            ];
        }
    }

    protected function logCommandAudit(string $command, int $exitCode, int $durationMs): void
    {
        try {
            $user = auth()->user();
            AuditLog::create([
                'user_id' => $user?->id,
                'user_name' => $user?->nama ?? 'Super Administrator',
                'user_email' => $user?->email ?? '-',
                'user_role' => $user?->role_label ?? 'SuperAdmin',
                'action' => 'terminal_command',
                'category' => 'maintenance',
                'description' => "Eksekusi Web Terminal: `{$command}` (Exit: {$exitCode}, {$durationMs}ms)",
                'properties' => [
                    'command' => $command,
                    'exit_code' => $exitCode,
                    'duration_ms' => $durationMs,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Throwable) {
            // Fail silently on audit log to not interrupt command execution
        }
    }

    protected function handleNanoEditor(string $command, string $fileArg): array
    {
        if (empty($fileArg)) {
            return [
                'success' => false,
                'command' => $command,
                'output' => "Penggunaan: nano <nama_file>\nContoh: nano .env atau nano config/database.php\nTips: Editor GNU nano terintegrasi langsung di Web Terminal AkuSehat.",
                'exit_code' => 1,
                'duration_ms' => 0,
                'timestamp' => now()->format('H:i:s'),
            ];
        }

        $fileArg = trim($fileArg, '"\' ');
        $base = realpath(base_path());
        $cleanRel = ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $fileArg), DIRECTORY_SEPARATOR);
        $targetPath = $base . DIRECTORY_SEPARATOR . $cleanRel;

        $content = '';
        $isNew = true;
        if (file_exists($targetPath) && is_file($targetPath)) {
            $content = @file_get_contents($targetPath) ?: '';
            $isNew = false;
        }

        $lines = substr_count($content, "\n") + (strlen($content) > 0 ? 1 : 0);

        return [
            'success' => true,
            'action' => 'open_nano',
            'command' => $command,
            'file' => $fileArg,
            'content' => $content,
            'is_new' => $isNew,
            'lines' => $lines,
            'output' => $isNew
                ? "[ nano: Membuka berkas baru '{$fileArg}' di editor nano... ]"
                : "[ nano: Membuka '{$fileArg}' ({$lines} baris) di editor nano... ]",
            'exit_code' => 0,
            'duration_ms' => 1,
            'timestamp' => now()->format('H:i:s'),
        ];
    }

    public function saveFile(string $filePath, string $content): array
    {
        $base = realpath(base_path());
        $cleanRel = ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, trim($filePath, '"\' ')), DIRECTORY_SEPARATOR);

        if (str_contains($cleanRel, "\0")) {
            return [
                'success' => false,
                'message' => 'Path berkas tidak valid.',
            ];
        }

        $targetPath = $base . DIRECTORY_SEPARATOR . $cleanRel;
        $targetDir = dirname($targetPath);

        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        // Automatic backup before overwrite
        if (file_exists($targetPath)) {
            @copy($targetPath, $targetPath . '.bak');
        }

        $bytes = @file_put_contents($targetPath, $content);

        if ($bytes === false) {
            return [
                'success' => false,
                'message' => "Gagal menyimpan berkas ke '{$cleanRel}'. Periksa izin/permission server.",
            ];
        }

        $lines = substr_count($content, "\n") + (strlen($content) > 0 ? 1 : 0);

        try {
            $user = auth()->user();
            AuditLog::create([
                'user_id' => $user?->id,
                'user_name' => $user?->nama ?? 'Super Administrator',
                'user_email' => $user?->email ?? '-',
                'user_role' => $user?->role_label ?? 'SuperAdmin',
                'action' => 'terminal_edit_file',
                'category' => 'maintenance',
                'description' => "Sunting Berkas Web Terminal: `{$cleanRel}` ({$lines} baris, {$bytes} byte)",
                'properties' => [
                    'file' => $cleanRel,
                    'lines' => $lines,
                    'bytes' => $bytes,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Throwable) {}

        return [
            'success' => true,
            'message' => "Berkas '{$cleanRel}' berhasil disimpan ({$lines} baris, {$bytes} byte). Salinan cadangan otomatis dibuat ({$cleanRel}.bak).",
            'file' => $cleanRel,
            'lines' => $lines,
            'bytes' => $bytes,
        ];
    }

    public function getSystemInfo(): array
    {
        $latestAppRelease = \App\Models\SoftwareApp::where('status', 'active')->orderBy('release_date', 'desc')->first();

        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'app_version' => $latestAppRelease ? 'v' . $latestAppRelease->version : 'v1.0.0',
            'os' => PHP_OS_FAMILY . ' (' . php_uname('s') . ' ' . php_uname('r') . ')',
            'base_path' => base_path(),
            'environment' => app()->environment(),
        ];
    }
}
