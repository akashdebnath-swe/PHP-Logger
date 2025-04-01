<?php

/**
 * Log Rotation Test Script
 * 
 * This script demonstrates the log rotation feature by writing logs
 * until the log file is rotated multiple times.
 * 
 * Usage: php test_rotation.php [size_kb] [entry_count]
 * Example: php test_rotation.php 50 500
 */

// Include the logger
require_once __DIR__ . '/log.php';

// Get arguments
$maxSizeKb = isset($argv[1]) && is_numeric($argv[1]) ? (int)$argv[1] : 100; // Default 100KB
$numEntries = isset($argv[2]) && is_numeric($argv[2]) ? (int)$argv[2] : 1000; // Default 1000 entries

// Convert to bytes
$maxSizeBytes = $maxSizeKb * 1024;

// Get the logger
$logger = $GLOBALS['logger'];

// Configure logger for testing
$logger->configureRotation(true, $maxSizeBytes);
$logFilePath = dirname(__FILE__) . '/main.log';

echo "Log Rotation Test\n";
echo "----------------\n";
echo "Max log size: {$maxSizeKb}KB ({$maxSizeBytes} bytes)\n";
echo "Entries to write: {$numEntries}\n";
echo "Log file: {$logFilePath}\n\n";

// Generate a message with reasonable size
$message = str_repeat("This is a test message for log rotation. ", 10);
$messageSize = strlen($message);
echo "Approximate message size: " . $messageSize . " bytes\n";
echo "Estimated entries per rotation: ~" . floor($maxSizeBytes / ($messageSize + 60)) . "\n\n";

// Function to format file size
function formatSize($bytes)
{
    if ($bytes >= 1048576) {
        return round($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return round($bytes / 1024, 2) . ' KB';
    }
    return $bytes . ' bytes';
}

// Track rotations
$rotationCount = 0;
$lastSize = 0;

// Add a rotation detector by monitoring file size
$detectRotation = function () use ($logFilePath, &$rotationCount, &$lastSize) {
    clearstatcache(true, $logFilePath);

    if (!file_exists($logFilePath)) {
        echo "Rotation detected: File does not exist (will be created)\n";
        $rotationCount++;
        $lastSize = 0;
        return;
    }

    $currentSize = filesize($logFilePath);

    // If size suddenly decreased, rotation occurred
    if ($lastSize > 0 && $currentSize < $lastSize) {
        $rotationCount++;
        echo "Rotation detected: Size went from " . formatSize($lastSize) . " to " . formatSize($currentSize) . "\n";
    } elseif ($currentSize > $lastSize) {
        echo "Current size: " . formatSize($currentSize) . "\n";
    }

    $lastSize = $currentSize;
};

// Initial check
$detectRotation();

echo "\nStarting test...\n";
echo "----------------\n";

// Write entries
for ($i = 1; $i <= $numEntries; $i++) {
    // Log a message
    $logger("Entry #{$i}: " . $message, "INFO");

    // Check for rotation every 50 entries
    if ($i % 50 === 0) {
        $detectRotation();
        echo "Progress: {$i}/{$numEntries} entries written (" . round($i / $numEntries * 100, 1) . "%)\n";
        // Small delay to make output readable
        usleep(100000); // 100ms
    }
}

// Final check
$detectRotation();

echo "\nTest completed\n";
echo "----------------\n";
echo "Total entries written: {$numEntries}\n";
echo "Rotation count: {$rotationCount}\n";
echo "Current log file size: " . (file_exists($logFilePath) ? formatSize(filesize($logFilePath)) : "File not found") . "\n";
echo "Log rotation is " . ($rotationCount > 0 ? "working correctly" : "not working as expected") . "\n";
