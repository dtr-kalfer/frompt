#!/usr/bin/env php
<?php
	/* This file is part of a copyrighted work; 
	it is distributed with NO WARRANTY. --F.Tumulak
	 */
	 
function buildTree($dir, $prefix = "", $depth = 0, $maxDepth = null, $ignoreList = [])
{
    if (!is_dir($dir)) {
        return "❌ Invalid directory: $dir\n";
    }

    if ($maxDepth !== null && $depth >= $maxDepth) {
        return "";
    }

    $files = @scandir($dir);
    if ($files === false) {
        return $prefix . "└── ⚠️  [Permission Denied]\n";
    }
    
    $files = array_diff($files, [".", ".."]);

    if (!empty($ignoreList)) {
        $files = array_filter($files, function ($file) use ($ignoreList) {
            return !in_array($file, $ignoreList);
        });
    }

    sort($files);

    $output = "";
    $count = count($files);
    $i = 0;

    foreach ($files as $file) {
        $i++;
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        $isLast = ($i === $count);
        $connector = $isLast ? "└── " : "├── ";

        if (is_dir($path)) {
            $output .= $prefix . $connector . "📁 " . $file . PHP_EOL;

            $newPrefix = $prefix . ($isLast ? "    " : "│   ");
            $output .= buildTree(
                $path,
                $newPrefix,
                $depth + 1,
                $maxDepth,
                $ignoreList
            );
        } else {
            $output .= $prefix . $connector . "📙 " . $file . PHP_EOL;
        }
    }

    return $output;
}

/**
 * Auto-detects OS and copies text to clipboard.
 * Optimized for Windows, macOS, Linux Desktop, and Headless Docker/SSH.
 */
function copyToClipboard($text)
{
    $os = strtoupper(substr(PHP_OS, 0, 3));
    
    // 1. Native Windows Host (e.g., Wamp64 environment)
    if ($os === 'WIN') {
        $cmd = 'powershell -NoProfile -Command "[Console]::InputEncoding = [System.Text.Encoding]::UTF8; $input | Set-Clipboard"';
        $descriptorspec = [0 => ["pipe", "r"], 1 => ["pipe", "w"], 2 => ["pipe", "w"]];
        $process = proc_open($cmd, $descriptorspec, $pipes);
        
        if (is_resource($process)) {
            fwrite($pipes[0], $text);
            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);
            return true;
        }
    }
    
    // 2. Native macOS Host
    if ($os === 'DAR') { 
        $process = @popen('pbcopy', 'w');
        if (is_resource($process)) {
            fwrite($process, $text);
            pclose($process);
            return true;
        }
    }
    
    // 3. Desktop Linux Environments (with a GUI)
		if ($os === 'LIN') {
        // Appending 2>/dev/null silences the "not found" shell warnings
        foreach (['xclip -selection clipboard 2>/dev/null', 'xsel -b 2>/dev/null'] as $clipCmd) {
            $process = @popen($clipCmd, 'w');
            if (is_resource($process)) {
                fwrite($process, $text);
                pclose($process);
                return true;
            }
        }
    }

    // 4. Fallback: Headless Linux / Docker Container / SSH Session
    // Emits an OSC 52 sequence. Your terminal emulator handles copying it to your local machine.
    echo "\e]52;c;" . base64_encode($text) . "\a";
    return true;
}

/* ---------- CLI OPTIONS & INPUT ARGS ---------- */

$folder = '.';
$maxDepth = null;
$ignoreList = [];
$copyToClipboard = false;

for ($i = 1; $i < $argc; $i++) {
    $arg = $argv[$i];

    if (strpos($arg, '--depth=') === 0) {
        $maxDepth = (int)substr($arg, 8);
    } elseif ($arg === '--depth') {
        $maxDepth = isset($argv[$i + 1]) ? (int)$argv[++$i] : null;
    } elseif (strpos($arg, '--ignore=') === 0) {
        $ignoreList = explode(',', substr($arg, 9));
    } elseif ($arg === '--ignore') {
        $ignoreList = isset($argv[$i + 1]) ? explode(',', $argv[++$i]) : [];
    } elseif ($arg === '--copy') {
        $copyToClipboard = true;
    } elseif ($arg === '--help' || $arg === '-h') {
        echo "Usage: fppt.php [directory] [--depth=N] [--ignore=dir1,file2] [--copy]\n";
        exit(0);
    } else {
        $folder = $arg;
    }
}

/* ---------- GENERATE OUTPUT ---------- */

$realPath = realpath($folder);
if (!$realPath || !is_dir($realPath)) {
    echo "❌ Invalid directory: $folder\n";
    exit(1);
}

$treeOutput = "```txt" . PHP_EOL;
$treeOutput .= basename($realPath) . "/" . PHP_EOL;
$treeOutput .= buildTree($realPath, "", 0, $maxDepth, $ignoreList);
$treeOutput .= "```" . PHP_EOL;

// Display to terminal
echo $treeOutput;

// Copy execution
if ($copyToClipboard) {
    if (copyToClipboard($treeOutput)) {
        echo "📋 Tree view copied to clipboard successfully!" . PHP_EOL;
    } else {
        echo "⚠️  Failed to copy to clipboard automatically." . PHP_EOL;
    }
}