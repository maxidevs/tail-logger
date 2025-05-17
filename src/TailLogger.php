<?php

namespace Maxidev\Logger;

use Monolog\Logger as MonoLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class TailLogger
{
    public static function saveLog(string $message, string $relativePath = '', string $level = 'info', array $context = []): void
    {
        $filename = date('Y-m-d') . '.log';
        $fullDir = rtrim(storage_path('logs/' . trim($relativePath, '/')), '/');
        $fullPath = "$fullDir/$filename";

        if (!is_dir($fullDir)) {
            mkdir($fullDir, 0777, true);
        }

        $formattedContext = (is_array($context) && count($context)) || (is_object($context) && count(get_object_vars($context)))
            ? json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE, 512)
            : null;

        $handler = new StreamHandler($fullPath, MonoLogger::DEBUG);

        $levelName = $level ? strtoupper($level) . ': ' : '';
        $format = "[%datetime%] $levelName%message%\n";
        if ($formattedContext) {
            $format .= "$formattedContext\n";
        }

        $formatter = new LineFormatter($format, "Y-m-d H:i:s", true, true);
        $handler->setFormatter($formatter);

        $logger = new MonoLogger('');
        $logger->pushHandler($handler);

        match (strtolower($level)) {
            'warning' => $logger->warning($message, $context),
            'danger', 'error' => $logger->error($message, $context),
            'success' => $logger->notice($message, $context),
            default => $logger->info($message, $context),
        };
    }
}
