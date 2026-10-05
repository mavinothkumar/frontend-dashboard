<?php
/**
 * Universal WordPress debug.log Sentinel
 *
 * Usage:
 *   php check-debug-log.php snapshot      # Save current position in debug.log
 *   php check-debug-log.php verify        # Check if new errors occurred since snapshot
 *   php check-debug-log.php tail [lines]  # Print the last N lines (default: 20)
 *   php check-debug-log.php clear         # Truncate debug.log to empty
 */

$action = $argv[1] ?? 'verify';
$lines_to_tail = isset($argv[2]) ? (int) $argv[2] : 20;

// Resolve debug.log path relative to plugin or root
$possible_paths = array(
    dirname(__DIR__, 2) . '/debug.log',                      // .../wp-content/debug.log (from inside plugin)
    __DIR__ . '/wordpress/wp-content/debug.log',            // from repo root
    __DIR__ . '/wp-content/debug.log',                      // from repo root
    dirname(__DIR__, 4) . '/wordpress/wp-content/debug.log', // fallback
);

$log_file = null;
foreach ($possible_paths as $path) {
    if (file_exists($path) || is_dir(dirname($path))) {
        $log_file = realpath($path) ?: $path;
        break;
    }
}

if (!$log_file) {
    echo "[ERROR] Could not locate WordPress wp-content/debug.log\n";
    exit(1);
}

$state_file = sys_get_temp_dir() . '/wp_debug_offset_' . md5($log_file) . '.txt';

switch ($action) {
    case 'snapshot':
        $size = file_exists($log_file) ? filesize($log_file) : 0;
        file_put_contents($state_file, (string) $size);
        echo "[SNAPSHOT] Debug log position recorded: $size bytes ($log_file)\n";
        exit(0);

    case 'clear':
        if (file_exists($log_file)) {
            file_put_contents($log_file, '');
            echo "[CLEARED] $log_file has been emptied.\n";
        }
        if (file_exists($state_file)) {
            file_put_contents($state_file, '0');
        }
        exit(0);

    case 'tail':
        if (!file_exists($log_file)) {
            echo "[INFO] debug.log does not exist yet.\n";
            exit(0);
        }
        $lines = file($log_file, FILE_IGNORE_NEW_LINES);
        $slice = array_slice($lines, -$lines_to_tail);
        echo "=== Last $lines_to_tail lines of $log_file ===\n";
        echo implode("\n", $slice) . "\n";
        exit(0);

    case 'verify':
    default:
        if (!file_exists($log_file)) {
            echo "[SUCCESS] No debug.log found (0 errors).\n";
            exit(0);
        }

        $prev_size = file_exists($state_file) ? (int) file_get_contents($state_file) : 0;
        $curr_size = filesize($log_file);

        if ($curr_size <= $prev_size) {
            echo "[SUCCESS] Zero new errors logged to debug.log.\n";
            exit(0);
        }

        $fp = fopen($log_file, 'rb');
        fseek($fp, $prev_size);
        $new_content = stream_get_contents($fp);
        fclose($fp);

        if (empty(trim($new_content))) {
            echo "[SUCCESS] Zero new errors logged to debug.log.\n";
            exit(0);
        }

        // Analyze new lines for actual errors vs benign logs
        $lines = explode("\n", $new_content);
        $errors_found = array();

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed)) {
                continue;
            }

            // Ignore benign background logs
            if (strpos($trimmed, 'Automatic updates starting') !== false ||
                strpos($trimmed, 'Automatic updates complete') !== false) {
                continue;
            }

            // Flag real PHP errors and database failures
            if (preg_match('/(PHP Fatal error|PHP Parse error|PHP Warning|PHP Notice|PHP Deprecated|WordPress database error|Uncaught Exception|Uncaught Error)/i', $trimmed)) {
                $errors_found[] = $trimmed;
            }
        }

        if (!empty($errors_found)) {
            echo "\n=========================================================================\n";
            echo " [ALARM] " . count($errors_found) . " NEW PHP / DATABASE ERRORS DETECTED IN debug.log!\n";
            echo "=========================================================================\n";
            foreach ($errors_found as $idx => $err) {
                echo ($idx + 1) . ") " . $err . "\n\n";
            }
            echo "=========================================================================\n";
            exit(1);
        }

        echo "[SUCCESS] Zero new errors detected (only benign logs recorded).\n";
        exit(0);
}
