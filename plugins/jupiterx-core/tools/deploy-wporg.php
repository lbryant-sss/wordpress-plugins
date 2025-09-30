#!/usr/bin/env php
<?php
declare(strict_types=1);

// Git → WordPress.org SVN deploy (PHP CLI, Termwind-only)
// Usage (interactive):
//   php tools/deploy-wporg.php
// Or (after chmod +x):
//   ./tools/deploy-wporg.php
// Flags (optional):
//   --tag <git-tag>      Prefill tag prompt with this tag
// The script prompts for tag, messages, then SVN credentials.

// Load root autoloader (required)
$__root_bootstrap = realpath(__DIR__.'/..');
if ($__root_bootstrap && file_exists($__root_bootstrap.'/vendor/autoload.php')) {
    /** @psalm-suppress UnresolvableInclude */
    require_once $__root_bootstrap.'/vendor/autoload.php';
}
use function Termwind\render;

if (!function_exists('Termwind\\render')
    || !function_exists('Laravel\\Prompts\\select')
    || !function_exists('Laravel\\Prompts\\text')
    || !function_exists('Laravel\\Prompts\\password')
    || !function_exists('Laravel\\Prompts\\confirm')
) {
    fwrite(STDERR, "Missing CLI UI dependencies. Run:\n  composer install -d wp-content/plugins/jupiterx-core\n  composer require --dev laravel/prompts -d wp-content/plugins/jupiterx-core\n");
    exit(1);
}

/**
 * Render an informational line.
 */
function say(string $msg): void { render("<div><span class='px-1 bg-green-600 text-white'>OK</span> <span class='text-green-600'>".htmlspecialchars($msg)."</span></div>"); }

/**
 * Render a warning line.
 */
function warn(string $msg): void { render("<div><span class='px-1 bg-yellow-600 text-white'>WARN</span> <span class='text-yellow-600'>".htmlspecialchars($msg)."</span></div>"); }

/**
 * Render an error line and exit.
 */
function diex(string $msg, int $code = 1): void { render("<div><span class='px-1 bg-red-700 text-white'>ERR</span> <span class='text-red-700'>".htmlspecialchars($msg)."</span></div>"); terminalRawMode(false); exit($code); }

function run(string $cmd, ?string $cwd = null, bool $allowFail = false): array {
    $descriptors = [1 => ['pipe','w'], 2 => ['pipe','w']];
    $env = null;
    $proc = proc_open($cmd, $descriptors, $pipes, $cwd ?: null, $env);
    if (!\is_resource($proc)) {
        if ($allowFail) return [1, '', ''];
        diex("Failed to start command: $cmd");
    }
    $out = stream_get_contents($pipes[1]); fclose($pipes[1]);
    $err = stream_get_contents($pipes[2]); fclose($pipes[2]);
    $code = proc_close($proc);
    if ($code !== 0 && !$allowFail) {
        diex("Command failed ($code): $cmd\n$err");
    }
    return [$code, $out, $err];
}

function runStream(string $cmd, ?string $cwd = null, bool $allowFail = false): array {
    $descriptors = [1 => ['pipe','w'], 2 => ['pipe','w']];
    $proc = proc_open($cmd, $descriptors, $pipes, $cwd ?: null, null);
    if (!\is_resource($proc)) {
        if ($allowFail) return [1, '', ''];
        diex("Failed to start command: $cmd");
    }
    stream_set_blocking($pipes[1], false);
    stream_set_blocking($pipes[2], false);
    $bufOut = '';
    $bufErr = '';
    while (true) {
        $o = stream_get_contents($pipes[1]);
        if ($o !== false && $o !== '') { $bufOut .= $o; echo $o; }
        $e = stream_get_contents($pipes[2]);
        if ($e !== false && $e !== '') { $bufErr .= $e; echo $e; }
        $status = proc_get_status($proc);
        if (!$status['running']) { break; }
        usleep(100000);
    }
    fclose($pipes[1]);
    fclose($pipes[2]);
    $code = proc_close($proc);
    if ($code !== 0 && !$allowFail) {
        diex("Command failed ($code): $cmd\n".trim($bufErr));
    }
    return [$code, $bufOut, $bufErr];
}

function ensureCmd(string $cmd, string $hint = ''): void {
    [$code] = run("command -v ".$cmd, null, true);
    if ($code !== 0) {
        $msg = $hint !== '' ? ($cmd.' not found. '.$hint) : ($cmd.' not found. Please install it.');
        diex($msg);
    }
}

function prompt(string $label, bool $secret = false): string {
    if ($secret) {
        Termwind\render("<div><span class='text-yellow-600'>".htmlspecialchars($label)."</span></div>");
        if (stripos(PHP_OS, 'WIN') === 0) {
            $line = trim(fgets(STDIN));
            return $line;
        }
        shell_exec('stty -echo');
        $line = trim(fgets(STDIN));
        shell_exec('stty echo');
        fwrite(STDOUT, "\n");
        return $line;
    }
    Termwind\render("<div><span class='text-yellow-600'>".htmlspecialchars($label)."</span></div>");
    return trim(fgets(STDIN));
}

function promptDefault(string $label, string $default): string {
    $suffix = $default !== '' ? ' ['.$default.']' : '';
    Termwind\render("<div><span class='text-yellow-600'>".htmlspecialchars($label.$suffix)."</span></div>");
    $val = trim(fgets(STDIN));
    return $val === '' ? $default : $val;
}

function promptMasked(string $label): string {
    Termwind\render("<div><span class='text-yellow-600'>".htmlspecialchars($label)."</span></div>");
    if (stripos(PHP_OS, 'WIN') === 0) {
        // Fallback on Windows: no masking
        $line = fgets(STDIN);
        return $line === false ? '' : rtrim($line, "\r\n");
    }
    $buffer = '';
    terminalRawMode(true);
    try {
        while (true) {
            $ch = fread(STDIN, 1);
            if ($ch === "\n" || $ch === "\r") { break; }
            if ($ch === "\x7F" || $ch === "\b") {
                if ($buffer !== '') {
                    $buffer = substr($buffer, 0, -1);
                    fwrite(STDOUT, "\x08 \x08"); // backspace, erase, backspace
                }
                continue;
            }
            if ($ch === "\x03") { // Ctrl-C
                terminalRawMode(false);
                exit(1);
            }
            if ($ch === '' || ord($ch) < 32) { continue; }
            $buffer .= $ch;
            fwrite(STDOUT, '*');
        }
    } finally {
        terminalRawMode(false);
        fwrite(STDOUT, "\n");
    }
    return $buffer;
}

function gitTags(string $root, int $limit = 15): array {
    [$code, $out] = run('git tag --sort=-creatordate', $root, true);
    if ($code !== 0 || trim($out) === '') return [];
    $tags = array_values(array_filter(array_map('trim', preg_split('/\r?\n/', trim($out)) ?: [])));
    return array_slice($tags, 0, $limit);
}

function terminalRawMode(bool $enable): void {
    if (stripos(PHP_OS, 'WIN') === 0) return; // raw mode unsupported on Windows here
    if ($enable) {
        shell_exec('stty -icanon -echo min 1 time 0');
    } else {
        shell_exec('stty sane');
    }
}

function isInteractiveTty(): bool {
    if (function_exists('stream_isatty')) {
        return @stream_isatty(STDIN) && @stream_isatty(STDOUT);
    }
    return false;
}

function selectInteractive(string $title, array $options, int $defaultIndex = 0): int {
    if (empty($options)) return -1;
    $index = max(0, min($defaultIndex, count($options) - 1));
    terminalRawMode(true);
    try {
    while (true) {
        // Clear screen
        echo "\033[2J\033[H"; // clear and move cursor home
        Termwind\render("<div class='mb-1'><span class='font-bold'>".htmlspecialchars($title)."</span></div>");
        foreach ($options as $i => $opt) {
            $selected = $i === $index;
            $label = htmlspecialchars($opt);
            if ($selected) {
                Termwind\render("<div><span class='px-1 bg-blue-600 text-white'>»</span> <span class='text-blue-600 font-bold'>".$label."</span></div>");
            } else {
                Termwind\render("<div><span class='px-1 text-gray-500'> </span> <span class='text-gray-500'>".$label."</span></div>");
            }
        }
        Termwind\render("<div class='mt-1 text-gray-500'>Use ↑/↓ or j/k, Enter to select, q to quit</div>");

        $key = fread(STDIN, 3); // read up to 3 bytes for escape sequences
        if ($key === "\n" || $key === "\r") { break; }
        if ($key === 'q') { terminalRawMode(false); diex('Aborted'); }
        if ($key === 'j') { $index = ($index + 1) % count($options); continue; }
        if ($key === 'k') { $index = ($index - 1 + count($options)) % count($options); continue; }
        if ($key !== '' && $key[0] === "\x1B") {
            // ESC [ A/B
            $seq = $key;
            if (strlen($seq) < 3) { $seq .= fread(STDIN, 3 - strlen($seq)); }
            if ($seq === "\x1B[A") { $index = ($index - 1 + count($options)) % count($options); }
            if ($seq === "\x1B[B") { $index = ($index + 1) % count($options); }
        }
    }
    } finally {
        terminalRawMode(false);
    }
    // Move past the menu
    echo "\033[2J\033[H";
    return $index;
}

// (removed duplicate promptDefault and gitTags definitions)

$argv = $_SERVER['argv']; array_shift($argv);
$tag = '';
for ($i = 0; $i < count($argv); $i++) {
    if ($argv[$i] === '--tag' && isset($argv[$i+1])) { $tag = $argv[$i+1]; $i++; continue; }
}

$root = realpath(__DIR__.'/..') ?: diex('Cannot resolve root directory');
$slug = 'jupiterx-core';

ensureCmd('svn', 'Install Subversion (e.g., apt install subversion).');
ensureCmd('rsync', 'Install rsync (e.g., apt install rsync).');
ensureCmd('git', 'Install git (e.g., apt install git).');
ensureCmd('composer', 'Install Composer (e.g., https://getcomposer.org/download/).');
ensureCmd('unzip', 'Install unzip (e.g., apt install unzip).');

// Fetch and choose tag interactively if not provided
run('git fetch --tags --quiet', $root, true);
if ($tag === '') {
    $tags = gitTags($root, 20);
    if (!empty($tags)) {
        if (function_exists('Laravel\\Prompts\\select')) {
            $choice = \Laravel\Prompts\select('Select a git tag to release', $tags, $tags[0]);
            if (is_string($choice) && $choice !== '') { $tag = $choice; }
        } else {
            $idx = selectInteractive('Select a git tag to release', $tags, 0);
            if ($idx >= 0) { $tag = $tags[$idx]; }
        }
    }
    if ($tag === '') {
        $tag = promptDefault('Which tag to release?', '');
    }
}
[$tagCheck] = run(sprintf('git rev-parse -q --verify %s^{tag}', escapeshellarg($tag)), $root, true);
if ($tagCheck !== 0) diex('Git tag not found: '.$tag);
// Worktree approach to get a clean copy at the tag
$workdir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'wporgdeploy_'.bin2hex(random_bytes(6));
if (!mkdir($workdir, 0700, true) && !is_dir($workdir)) diex('Failed to create tmp directory');
$srcDir = $workdir.'/src';
run(sprintf("git worktree add --detach %s %s", escapeshellarg($srcDir), escapeshellarg($tag)), $root);

$main = $srcDir.'/jupiterx-core.php';
$readme = $srcDir.'/readme.txt';
// $distignore reserved for future use if we add filtering

if (!is_file($main)) diex("Main plugin file not found (tag $tag): $main");
if (!is_file($readme)) diex("readme.txt not found (tag $tag): $readme");

say('Validating version coherence');
$mainContent = file_get_contents($main) ?: diex('Failed to read main plugin file');
if (!preg_match('/\n\s*\*\s*Version:\s*([0-9.]+)/', $mainContent, $m)) diex('Could not parse Version header from main plugin file');
$versionPhp = $m[1];

$stableTag = null;
$fh = fopen($readme, 'r');
while (($line = fgets($fh)) !== false) {
    if (preg_match('/^Stable tag:\s*(.+)$/', trim($line), $rm)) { $stableTag = trim($rm[1]); break; }
}
fclose($fh);
if ($stableTag === null) diex('Could not find Stable tag in readme.txt');

if ($versionPhp !== $stableTag) diex("Version mismatch between main file ($versionPhp) and readme Stable tag ($stableTag)");
$version = $versionPhp;
// Validate version format (WordPress.org requirement)
if (!preg_match('/^[0-9]+\.[0-9]+(\.[0-9]+)?(-[a-zA-Z0-9\.\-]+)?$/', $version)) {
    diex('Invalid version format: '.$version.' (WordPress.org requires semantic versioning like 1.2.3)');
}

// Abort if SVN tag already exists
$svnBaseUrl = 'https://plugins.svn.wordpress.org/'.$slug;
[$svnTagCode] = run(sprintf('svn ls %s', escapeshellarg($svnBaseUrl.'/tags/'.$version)), null, true);
if ($svnTagCode === 0) {
    diex('SVN tag already exists: '.$svnBaseUrl.'/tags/'.$version);
}

// Ask for commit and tag messages with sensible defaults
$defaultCommit = 'Release '.$slug.' '.$version;
$defaultTagMsg = 'Tag '.$slug.' '.$version;
Termwind\render("<div class='mt-1 text-gray-500'>Default commit message: ".htmlspecialchars($defaultCommit)."</div>");
Termwind\render("<div class='mb-1 text-gray-500'>Default tag message: ".htmlspecialchars($defaultTagMsg)."</div>");
if (function_exists('Laravel\\Prompts\\text')) {
    $commitMsg = (string)\Laravel\Prompts\text('SVN commit message', $defaultCommit);
    if ($commitMsg === '') { $commitMsg = $defaultCommit; }
    $tagMsg = (string)\Laravel\Prompts\text('SVN tag message', $defaultTagMsg);
    if ($tagMsg === '') { $tagMsg = $defaultTagMsg; }
} else {
    $commitMsg = promptDefault('SVN commit message', $defaultCommit);
    $tagMsg = promptDefault('SVN tag message', $defaultTagMsg);
}

// Build is a separate process handled externally

say('Preparing clean export from built ZIP');
$defaultZipVersioned = $root.'/jupiterx-core-'.$version.'.zip';
$defaultZip = is_file($defaultZipVersioned) ? $defaultZipVersioned : ($root.'/jupiterx-core.zip');
$zipPath = promptDefault('Path to built ZIP (from gulp release)', is_file($defaultZip) ? $defaultZip : '');
if ($zipPath === '' || !is_file($zipPath)) {
    diex('Built ZIP not found. Run the build first: npm ci && npx gulp release');
}
$unzipDir = $workdir.'/unzipped';
if (!mkdir($unzipDir, 0755, true) && !is_dir($unzipDir)) diex('Failed to create unzip directory');
run(sprintf('unzip -q %s -d %s', escapeshellarg($zipPath), escapeshellarg($unzipDir)));
// Find inner directory
$buildDir = $unzipDir;
$entries = scandir($unzipDir) ?: [];
foreach ($entries as $e) {
    if ($e === '.' || $e === '..') continue;
    if (is_dir($unzipDir.'/'.$e)) { $buildDir = $unzipDir.'/'.$e; break; }
}

// The ZIP created by gulp typically contains a top-level plugin folder 'jupiterx-core'
if (basename($buildDir) !== $slug && is_dir($buildDir.'/'.$slug)) {
    $buildDir = $buildDir.'/'.$slug;
}

// Verify built ZIP version and readme Stable tag match selected tag
$builtMain = $buildDir.'/jupiterx-core.php';
if (!is_file($builtMain)) diex('Built ZIP missing jupiterx-core.php');
$builtMainContent = file_get_contents($builtMain) ?: diex('Failed to read built main plugin file');
if (!preg_match('/\n\s*\*\s*Version:\s*([0-9.]+)/', $builtMainContent, $bm)) diex('Could not parse Version header from built ZIP');
$builtVersion = $bm[1];
if ($builtVersion !== $version) diex('Built ZIP version '.$builtVersion.' does not match selected tag '.$version);

$builtReadme = $buildDir.'/readme.txt';
if (is_file($builtReadme)) {
    $br = fopen($builtReadme, 'r');
    $builtStable = null;
    while (($line = fgets($br)) !== false) {
        if (preg_match('/^Stable tag:\s*(.+)$/', trim($line), $rm2)) { $builtStable = trim($rm2[1]); break; }
    }
    fclose($br);
    if ($builtStable !== null && $builtStable !== $version) {
        diex('Built ZIP readme Stable tag '.$builtStable.' does not match selected tag '.$version);
    }
}

// Validate built content aligns with gulp release filters
$forbidden = ['node_modules','vendor','build','cypress','visual-diff','wpcs','.git','.github'];
foreach ($forbidden as $f) {
    if (is_dir($buildDir.'/'.$f)) {
        diex('Built ZIP should not contain directory: '.$f);
    }
}
if (is_dir($buildDir.'/includes/extensions/raven/assets/src')) {
    diex('Built ZIP should not contain Raven assets/src');
}

// Required built assets for Raven extension
$cssDir = $buildDir.'/includes/extensions/raven/assets/css';
$jsDir = $buildDir.'/includes/extensions/raven/assets/js';
$fontsDir = $buildDir.'/includes/extensions/raven/assets/fonts';
if (!is_dir($cssDir) || count(glob($cssDir.'/*.css')) === 0) {
    diex('Missing built CSS assets in Raven (includes/extensions/raven/assets/css)');
}
if (!is_dir($jsDir) || count(glob($jsDir.'/*.js')) === 0) {
    diex('Missing built JS assets in Raven (includes/extensions/raven/assets/js)');
}
if (!is_dir($fontsDir) || count(glob($fontsDir.'/*.{ttf,eot,woff,woff2,svg}', GLOB_BRACE)) === 0) {
    diex('Missing built fonts in Raven (includes/extensions/raven/assets/fonts)');
}

// Disallow dev files at plugin root (mirrors gulp release ignores)
$badRoot = glob($buildDir.'/*.{lock,json,xml,js,yml}', GLOB_BRACE) ?: [];
if (!empty($badRoot)) {
    $badNames = array_map('basename', $badRoot);
    diex('Built ZIP should not contain dev files at root: '.implode(', ', $badNames));
}

say('Checking out SVN trunk');
$svnUrl = 'https://plugins.svn.wordpress.org/'.$slug;
$svnDir = $workdir.'/svn';
if (!mkdir($svnDir, 0755, true) && !is_dir($svnDir)) diex('Failed to create SVN directory');
run(sprintf('svn checkout %s %s', escapeshellarg($svnUrl.'/trunk'), escapeshellarg($svnDir.'/trunk')));

// Check current trunk version
$trunkMainFile = $svnDir.'/trunk/jupiterx-core.php';
if (is_file($trunkMainFile)) {
    $trunkContent = file_get_contents($trunkMainFile) ?: '';
    if (preg_match('/\n\s*\*\s*Version:\s*([0-9.]+)/', $trunkContent, $tm)) {
        $trunkVersion = $tm[1];
        say('Current trunk version: '.$trunkVersion);
        if ($trunkVersion === $version) {
            warn('Trunk already contains version '.$version.' - this might explain why no changes are detected');
        }
    }
} else {
    warn('Main plugin file not found in trunk - this might be a fresh repository');
}

say('Syncing trunk');
// Show what we're syncing
Termwind\render("<div class='text-gray-500'>Syncing from: ".htmlspecialchars($buildDir)."</div>");
Termwind\render("<div class='text-gray-500'>Syncing to: ".htmlspecialchars($svnDir.'/trunk')."</div>");
// Verify source directory has content
$sourceFiles = glob($buildDir.'/*');
if (empty($sourceFiles)) {
    diex('Source build directory is empty: '.$buildDir);
}
say('Source contains '.count($sourceFiles).' items');
// Run rsync with verbose output to see what's being synced
[$rsyncCode, $rsyncOut, $rsyncErr] = run(sprintf('rsync -rlptgoD --delete --compress --exclude=.svn/ --itemize-changes %s %s', escapeshellarg($buildDir.'/'), escapeshellarg($svnDir.'/trunk/')), null, true);
if ($rsyncCode !== 0) {
    diex('Rsync failed: '.$rsyncErr);
}
if (trim($rsyncOut) === '') {
    warn('Rsync completed but no changes were made - files might already be identical');
} else {
    Termwind\render("<div class='text-gray-500'>Rsync changes:</div>");
    echo $rsyncOut."\n";
}

// No assets sync here; deploy exactly ZIP contents

// svn add/remove missing in trunk
$path = $svnDir.'/trunk';
run('svn add --force .', $path, true);
[$code, $out] = run('svn status', $path, true);
if ($code === 0 && $out !== '') {
    foreach (preg_split('/\r?\n/', trim($out)) as $line) {
        if ($line === '') continue;
        if ($line[0] === '!') {
            $file = trim(substr($line, 1));
            run('svn rm --force '.escapeshellarg($file), $path, true);
        }
    }
}
// Final add to catch any remaining untracked files
run('svn add --force .', $path, true);

// Present a summary and confirm before committing
Termwind\render("<div class='mt-1'><span class='font-bold'>Summary</span></div>");
Termwind\render("<div>• <span class='font-bold'>Git tag</span>: ".htmlspecialchars($tag)."</div>");
Termwind\render("<div>• <span class='font-bold'>Version</span>: ".htmlspecialchars($version)."</div>");
Termwind\render("<div>• <span class='font-bold'>ZIP path</span>: ".htmlspecialchars($zipPath)."</div>");
[$_, $pending] = run('svn status', $svnDir.'/trunk', true);
if (!empty(trim($pending))) {
    Termwind\render("<div class='mt-1'><span class='text-yellow-600'>Pending SVN status in trunk:</span></div>");
    echo $pending."\n";
}
$confirm = strtolower(prompt('Proceed with commit and tag? [y/N]:'));
if (!in_array($confirm, ['y','yes'], true)) {
    warn('Aborted by user. Cleaning up.');
    run(sprintf('git worktree remove --force %s', escapeshellarg($srcDir)), $root, true);
    run(sprintf('rm -rf %s', escapeshellarg($workdir)), null, true);
    exit(0);
}

// Collect SVN credentials interactively
$svnUser = prompt('SVN username:');
$svnPassword = promptMasked('SVN password (pasteable, masked):');
$svnFlags = '';
// Track flags we will use for tag step as well
$svnFlagsEffective = '';
$isTty = isInteractiveTty();
if ($svnPassword === '' && !$isTty) {
    diex('No password provided and no interactive TTY available for SVN auth. Provide a password or run from an interactive terminal.');
}
// If a password is provided, use non-interactive mode without caching; otherwise allow interactive prompts
if ($svnPassword !== '') {
    $svnFlags = ' --non-interactive --no-auth-cache'.sprintf(' --password %s', escapeshellarg($svnPassword));
}
$svnFlagsEffective = $svnFlags;

say('Committing trunk');
// Quick diagnostic check
[$statusCode, $statusOut] = run('svn status', $svnDir.'/trunk', true);
if ($statusCode === 0 && trim($statusOut) !== '') {
    Termwind\render("<div class='text-gray-500'>SVN status before commit:</div>");
    echo $statusOut."\n";
    Termwind\render("<div class='text-gray-500'>svn commit (showing progress)...</div>");
    [$commitCode, $commitOut, $commitErr] = runStream(sprintf('svn commit --config-option servers:global:http-timeout=600 --username %s%s -m %s .', escapeshellarg($svnUser), $svnFlags, escapeshellarg($commitMsg)), $svnDir.'/trunk', true);
} else {
    say('No changes detected - trunk is already up to date, skipping commit');
    $commitCode = 0; // Treat as success since trunk is current
    $commitErr = '';
}

if ($commitCode !== 0) {
    // Show detailed error information
    $errorDetails = trim($commitErr ?: $commitOut);
    if ($errorDetails === '') {
        $errorDetails = 'No error details captured (exit code: '.$commitCode.')';
    }
    warn('SVN commit failed with details: '.$errorDetails);
    // Attempt to resolve out-of-date errors by updating and restaging
    if (stripos($commitErr, 'E155011') !== false || stripos($commitErr, 'E160020') !== false) {
        warn('SVN working copy out of date. Recreating from fresh checkout and resyncing...');
        // Clean approach: start fresh to avoid complex conflict resolution
        run(sprintf('rm -rf %s', escapeshellarg($svnDir.'/trunk')), null, true);
        run(sprintf('svn checkout %s %s', escapeshellarg($svnUrl.'/trunk'), escapeshellarg($svnDir.'/trunk')));
        say('Re-syncing trunk after fresh checkout');
        run(sprintf('rsync -rlptgoD --delete --compress --exclude=.svn/ %s %s', escapeshellarg($buildDir.'/'), escapeshellarg($svnDir.'/trunk/')));
        run('svn add --force .', $svnDir.'/trunk', true);
        [$statusCode, $statusOut] = run('svn status', $svnDir.'/trunk', true);
        if ($statusCode === 0 && $statusOut !== '') {
            foreach (preg_split('/\r?\n/', trim($statusOut)) as $line) {
                if ($line === '') continue;
                if ($line[0] === '!') {
                    $file = trim(substr($line, 1));
                    run('svn rm --force '.escapeshellarg($file), $svnDir.'/trunk', true);
                }
            }
        }
        Termwind\render("<div class='text-gray-500'>svn commit retry (after fresh sync)...</div>");
        [$commitCode, , $commitErr] = runStream(sprintf('svn commit --config-option servers:global:http-timeout=600 --username %s%s -m %s .', escapeshellarg($svnUser), $svnFlags, escapeshellarg($commitMsg)), $svnDir.'/trunk', true);
    }
    if (stripos($commitErr, 'No changes to commit') !== false) {
        // continue
    } elseif (stripos($commitErr, 'E215004') !== false) {
        if ($isTty) {
            warn('SVN authentication failed in non-interactive mode. Retrying interactively (no password flags)...');
            $svnFlagsEffective = '';
            Termwind\render("<div class='text-gray-500'>svn commit retry (interactive)...</div>");
            [$retryCode, , $retryErr] = runStream(sprintf('svn commit --config-option servers:global:http-timeout=600 --username %s -m %s .', escapeshellarg($svnUser), escapeshellarg($commitMsg)), $svnDir.'/trunk', true);
            if ($retryCode !== 0 && stripos($retryErr, 'No changes to commit') === false) {
                diex("SVN commit failed after interactive retry. Details:\n".trim($retryErr));
            }
        } else {
            diex("SVN authentication failed in non-interactive mode and no TTY available for interactive retry. Details:\n".trim($commitErr));
        }
    } elseif (stripos($commitErr, 'E155015') !== false || stripos($commitErr, 'remains in conflict') !== false) {
        warn('SVN working copy remains in conflict. Recreating trunk and retrying...');
        run('svn revert -R .', $svnDir.'/trunk', true);
        run('svn cleanup --remove-unversioned --vacuum-pristines', $svnDir.'/trunk', true);
        Termwind\render("<div class='text-gray-500'>svn update (clean)…</div>");
        runStream('svn update --non-interactive', $svnDir.'/trunk', true);
        say('Re-syncing trunk after cleanup');
        run(sprintf('rsync -rc --delete %s %s', escapeshellarg($buildDir.'/'), escapeshellarg($svnDir.'/trunk/')));
        run('svn add --force .', $svnDir.'/trunk', true);
        [$statusCode2, $statusOut2] = run('svn status', $svnDir.'/trunk', true);
        if ($statusCode2 === 0 && $statusOut2 !== '') {
            foreach (preg_split('/\r?\n/', trim($statusOut2)) as $line2) {
                if ($line2 === '') continue;
                if ($line2[0] === '!') {
                    $file2 = trim(substr($line2, 1));
                    run('svn rm --force '.escapeshellarg($file2), $svnDir.'/trunk', true);
                }
            }
        }
        [$commitCode, , $commitErr] = run(sprintf('svn commit --username %s%s -m %s .', escapeshellarg($svnUser), $svnFlags, escapeshellarg($commitMsg)), $svnDir.'/trunk', true);
    } else {
        diex('SVN commit failed: '.trim($commitErr));
    }
}

say('Tagging '.$version);
Termwind\render("<div class='text-gray-500'>svn copy (tagging)...</div>");
[$tagCode, $tagOut, $tagErr] = runStream(sprintf('svn copy %s %s -m %s --username %s%s --config-option servers:global:http-timeout=600',
    escapeshellarg($svnUrl.'/trunk'),
    escapeshellarg($svnUrl.'/tags/'.$version),
    escapeshellarg($tagMsg),
    escapeshellarg($svnUser),
    $svnFlagsEffective
), null, true);

// Check for successful commit in output even if exit code is non-zero
$isSuccess = false;
if ($tagCode === 0) {
    $isSuccess = true;
} elseif (stripos($tagOut, 'Committed revision') !== false || stripos($tagErr, 'Committed revision') !== false) {
    // SVN sometimes returns non-zero exit codes even on successful commits
    $isSuccess = true;
    say('Tag created successfully despite non-zero exit code');
}

if (!$isSuccess) {
    if (stripos($tagErr, 'E215004') !== false) {
        if ($isTty) {
            warn('SVN authentication failed during tagging in non-interactive mode. Retrying interactively...');
            [$retryTagCode, , $retryTagErr] = runStream(sprintf('svn copy %s %s -m %s --username %s --config-option servers:global:http-timeout=600',
                escapeshellarg($svnUrl.'/trunk'),
                escapeshellarg($svnUrl.'/tags/'.$version),
                escapeshellarg($tagMsg),
                escapeshellarg($svnUser)
            ), null, true);
            if ($retryTagCode !== 0) {
                diex('SVN tag failed after interactive retry: '.trim($retryTagErr));
            }
        } else {
            diex('SVN tag failed (authentication) and no TTY available for interactive retry: '.trim($tagErr));
        }
    } else {
        diex('SVN tag failed: '.trim($tagErr));
    }
}

// Verify deployment
say('Verifying deployment');
[$verifyTagCode] = run(sprintf('svn ls %s', escapeshellarg($svnUrl.'/tags/'.$version)), null, true);
if ($verifyTagCode !== 0) {
    warn('Tag verification failed - tag may not be visible yet (SVN sync delay)');
} else {
    say('Tag verified: '.$svnUrl.'/tags/'.$version);
}

// Quick trunk verification
[$verifyTrunkCode, $verifyOut] = run(sprintf('svn ls %s', escapeshellarg($svnUrl.'/trunk')), null, true);
if ($verifyTrunkCode === 0 && strpos($verifyOut, 'jupiterx-core.php') !== false) {
    say('Trunk updated successfully');
} else {
    warn('Trunk verification inconclusive');
}

// Clean up worktree
run(sprintf('git worktree remove --force %s', escapeshellarg($srcDir)), $root, true);
// Cleanup temp directory
run(sprintf('rm -rf %s', escapeshellarg($workdir)), null, true);

say('Deploy complete.');
say('Verify at: '.$svnUrl);
say('WordPress.org will process the tag within 15-30 minutes.');


