<?php

namespace App\Libraries;

/**
 * Layanan pemantauan & pemeliharaan berkas log aplikasi harian (writable/logs/log-YYYY-MM-DD.log).
 * Dirancang hemat memori (stream baris-per-baris) dan aman dari path traversal.
 */
class AppLogService
{
    private const FILE_PATTERN  = '/^log-(\d{4}-\d{2}-\d{2})\.log$/';
    private const ENTRY_PATTERN = '/^(EMERGENCY|ALERT|CRITICAL|ERROR|WARNING|NOTICE|INFO|DEBUG)\s+-\s+(\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2})\s+-->\s+(.*)$/';
    private const REF_PATTERN   = '/\b(ECC-\d{8}-[A-Z0-9]{4,8})\b/';

    private string $logDir;

    public function __construct(?string $logDir = null)
    {
        if ($logDir !== null) {
            $this->logDir = rtrim($logDir, '\\/ ');
        } elseif (defined('WRITEPATH')) {
            $this->logDir = rtrim(WRITEPATH, '\\/ ') . DIRECTORY_SEPARATOR . 'logs';
        } else {
            $this->logDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'writable' . DIRECTORY_SEPARATOR . 'logs';
        }
    }

    /**
     * Ringkasan jumlah berkas, ukuran total, dan daftar tanggal log yang tersedia.
     *
     * @return array{file_count:int,total_bytes:int,total_mb:float,oldest_date:?string,newest_date:?string,dates:list<string>}
     */
    public function getStorageStats(): array
    {
        $files = $this->listLogFiles();
        $totalBytes = 0;
        $dates = [];

        foreach ($files as $date => $info) {
            $totalBytes += $info['size'];
            $dates[] = $date;
        }

        // Urutkan terbaru ke terlama
        rsort($dates);

        return [
            'file_count'  => count($dates),
            'total_bytes' => $totalBytes,
            'total_mb'    => round($totalBytes / 1048576, 2),
            'oldest_date' => !empty($dates) ? $dates[count($dates) - 1] : null,
            'newest_date' => $dates[0] ?? null,
            'dates'       => $dates,
        ];
    }

    /**
     * Membaca entri log aplikasi secara streaming dengan filter tanggal, level, dan kata kunci / kode rujukan.
     *
     * @return array{
     *   selected_date:?string,
     *   available_dates:list<string>,
     *   file_size_mb:float,
     *   counts:array{critical:int,error:int,warning:int},
     *   total_matched:int,
     *   entries:list<array{level:string,timestamp:string,ref_id:?string,message:string,detail:string}>
     * }
     */
    public function getEntries(?string $date = null, string $levelFilter = 'ERROR_CRITICAL', string $search = '', int $limit = 100): array
    {
        $files = $this->listLogFiles();
        $dates = array_keys($files);
        rsort($dates);

        $selectedDate = null;
        if ($date !== null && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) === 1 && isset($files[$date])) {
            $selectedDate = $date;
        } elseif (!empty($dates)) {
            $selectedDate = $dates[0];
        }

        $counts = ['critical' => 0, 'error' => 0, 'warning' => 0];
        if ($selectedDate === null) {
            return [
                'selected_date'   => null,
                'available_dates' => [],
                'file_size_mb'    => 0.0,
                'counts'          => $counts,
                'total_matched'   => 0,
                'entries'         => [],
            ];
        }

        $allowedLevels = match (strtoupper(trim($levelFilter))) {
            'CRITICAL'   => ['EMERGENCY', 'ALERT', 'CRITICAL'],
            'ERROR'      => ['ERROR'],
            'WARNING'    => ['WARNING'],
            'ALL_ISSUES' => ['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR', 'WARNING'],
            default      => ['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR'],
        };

        $limit = max(10, min(200, $limit));
        $searchClean = trim($search);
        $fileInfo = $files[$selectedDate];
        $handle = @fopen($fileInfo['path'], 'rb');

        if ($handle === false) {
            return [
                'selected_date'   => $selectedDate,
                'available_dates' => $dates,
                'file_size_mb'    => round($fileInfo['size'] / 1048576, 2),
                'counts'          => $counts,
                'total_matched'   => 0,
                'entries'         => [],
            ];
        }

        $matchedWindow = [];
        $totalMatched = 0;
        $currentEntry = null;
        $currentDetailLines = 0;

        $flushEntry = static function (?array $entry) use (&$matchedWindow, &$totalMatched, $searchClean, $limit): void {
            if ($entry === null) {
                return;
            }
            if ($searchClean !== '') {
                $haystack = $entry['timestamp'] . ' ' . $entry['level'] . ' ' . ($entry['ref_id'] ?? '') . ' ' . $entry['message'] . ' ' . $entry['detail'];
                if (stripos($haystack, $searchClean) === false) {
                    return;
                }
            }
            $totalMatched++;
            $matchedWindow[] = $entry;
            if (count($matchedWindow) > $limit) {
                array_shift($matchedWindow);
            }
        };

        try {
            while (($line = fgets($handle)) !== false) {
                $trimmed = rtrim($line, "\r\n");
                if (preg_match(self::ENTRY_PATTERN, $trimmed, $m) === 1) {
                    $flushEntry($currentEntry);
                    $currentEntry = null;
                    $currentDetailLines = 0;

                    $lvl = strtoupper($m[1]);
                    if (in_array($lvl, ['EMERGENCY', 'ALERT', 'CRITICAL'], true)) {
                        $counts['critical']++;
                    } elseif ($lvl === 'ERROR') {
                        $counts['error']++;
                    } elseif ($lvl === 'WARNING') {
                        $counts['warning']++;
                    }

                    if (!in_array($lvl, $allowedLevels, true)) {
                        continue;
                    }

                    $msg = trim($m[3]);
                    $refId = null;
                    if (preg_match(self::REF_PATTERN, $msg, $rm) === 1) {
                        $refId = $rm[1];
                    }

                    $currentEntry = [
                        'level'     => $lvl,
                        'timestamp' => $m[2],
                        'ref_id'    => $refId,
                        'message'   => $msg,
                        'detail'    => '',
                    ];
                } elseif ($currentEntry !== null && $currentDetailLines < 12 && strlen($currentEntry['detail']) < 1500) {
                    if ($trimmed !== '') {
                        $currentEntry['detail'] .= ($currentEntry['detail'] === '' ? '' : "\n") . $trimmed;
                        $currentDetailLines++;
                        if ($currentEntry['ref_id'] === null && preg_match(self::REF_PATTERN, $trimmed, $rm) === 1) {
                            $currentEntry['ref_id'] = $rm[1];
                        }
                    }
                }
            }
            $flushEntry($currentEntry);
        } finally {
            fclose($handle);
        }

        // Tampilkan entri terbaru lebih dulu
        $matchedWindow = array_reverse($matchedWindow);

        return [
            'selected_date'   => $selectedDate,
            'available_dates' => $dates,
            'file_size_mb'    => round($fileInfo['size'] / 1048576, 2),
            'counts'          => $counts,
            'total_matched'   => $totalMatched,
            'entries'         => $matchedWindow,
        ];
    }

    /**
     * Menghapus berkas log harian yang berusia lebih lama dari jumlah hari retensi.
     *
     * @return array{deleted_files:int,freed_bytes:int,freed_mb:float,cutoff_date:string,retention_days:int}
     */
    public function purgeOlderThanDays(int $retentionDays): array
    {
        if (!in_array($retentionDays, [7, 14, 30, 90], true)) {
            $retentionDays = 30;
        }

        $cutoffDate = date('Y-m-d', strtotime("-{$retentionDays} days"));
        $files = $this->listLogFiles();
        $deletedFiles = 0;
        $freedBytes = 0;

        foreach ($files as $date => $info) {
            if ($date >= $cutoffDate) {
                continue;
            }
            $safeName = basename($info['path']);
            if (preg_match(self::FILE_PATTERN, $safeName) !== 1) {
                continue;
            }
            $fullPath = $this->logDir . DIRECTORY_SEPARATOR . $safeName;
            if (is_file($fullPath) && @unlink($fullPath)) {
                $deletedFiles++;
                $freedBytes += $info['size'];
            }
        }

        return [
            'deleted_files'  => $deletedFiles,
            'freed_bytes'    => $freedBytes,
            'freed_mb'       => round($freedBytes / 1048576, 2),
            'cutoff_date'    => $cutoffDate,
            'retention_days' => $retentionDays,
        ];
    }

    /**
     * @return array<string, array{path:string,size:int}>
     */
    private function listLogFiles(): array
    {
        if (!is_dir($this->logDir)) {
            return [];
        }

        $entries = @scandir($this->logDir);
        if (!is_array($entries)) {
            return [];
        }

        $result = [];
        foreach ($entries as $name) {
            if (preg_match(self::FILE_PATTERN, $name, $m) !== 1) {
                continue;
            }
            $fullPath = $this->logDir . DIRECTORY_SEPARATOR . $name;
            if (!is_file($fullPath)) {
                continue;
            }
            $result[$m[1]] = [
                'path' => $fullPath,
                'size' => (int) (@filesize($fullPath) ?: 0),
            ];
        }

        return $result;
    }
}
