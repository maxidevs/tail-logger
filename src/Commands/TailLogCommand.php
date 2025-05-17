<?php

namespace Maxidev\Logger\Commands;

use Illuminate\Console\Command;

class TailLogCommand extends Command
{
    protected $signature = 'log:tail {path? : Relative path from storage/logs (e.g. api/fingerprints)}
                                      {--date= : YYYY-MM-DD date of log file (defaults to today)}
                                      {--live : Monitor file continuously like tail -f}';

    protected $description = 'Tail a log file with optional live mode and colored output';

    public function handle(): int
    {
        $relativePath = trim($this->argument('path') ?? '', '/');
        $date = $this->option('date') ?? date('Y-m-d');
        $filename = "$date.log";
        $logPath = storage_path("logs/" . ($relativePath ? "$relativePath/" : "") . $filename);

        if (!file_exists($logPath)) {
            $this->error("Archivo no encontrado: $logPath");
            return 1;
        }

        $this->info("Viendo logs en: $logPath\n");

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        if ($this->option('live')) {
            $isWindows ? $this->tailLiveWindows($logPath) : $this->tailLiveUnix($logPath);
        } else {
            $this->readOnce($logPath);
        }

        return 0;
    }

    private function readOnce(string $logPath): void
    {
        $lines = file($logPath, FILE_IGNORE_NEW_LINES);
        foreach ($lines as $line) {
            echo $this->colorize($line) . PHP_EOL;
        }
    }

    private function tailLiveWindows(string $logPath): void
    {
        $lastSize = 0;

        while (true) {
            clearstatcache();
            $size = filesize($logPath);

            if ($size > $lastSize) {
                $fp = fopen($logPath, 'r');
                fseek($fp, $lastSize);

                while (($line = fgets($fp)) !== false) {
                    echo $this->colorize(trim($line)) . PHP_EOL;
                }

                fclose($fp);
                $lastSize = $size;
            }

            usleep(500000);
        }
    }

    private function tailLiveUnix(string $logPath): void
    {
        $fp = fopen($logPath, 'r');
        if (!$fp) {
            $this->error("No se pudo abrir el archivo.");
            return;
        }

        fseek($fp, 0, SEEK_END);

        while (true) {
            $line = fgets($fp);

            if ($line !== false) {
                echo $this->colorize(trim($line)) . PHP_EOL;
            } else {
                usleep(500000);
            }
        }
    }

    private function colorize(string $line): string
    {
        $lower = strtolower($line);

        return match (true) {
            str_contains($lower, 'error:') => "\033[0;31m$line\033[0m",
            str_contains($lower, 'warning:') => "\033[1;33m$line\033[0m",
            str_contains($lower, '[success]') => "\033[0;32m$line\033[0m",
            str_contains($lower, 'notice:') => "\033[0;36m$line\033[0m",
            default => "\033[0;37m$line\033[0m",
        };
    }
}
