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
//   --node <major>       Node major version for build (default: 12)
//   --skip-build         Skip npm build step
// The script prompts for tag, messages, then SVN credentials.

// Load root autoloader (required)
$__root_bootstrap = realpath(__DIR__.'/..');
if ($__root_bootstrap && file_exists($__root_bootstrap.'/vendor/autoload.php')) {
    /** @psalm-suppress UnresolvableInclude */
    require_once $__root_bootstrap.'/vendor/autoload.php';
}
use function Termwind\render;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;
use function Laravel\Prompts\password;
use function Laravel\Prompts\confirm;

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
$skipBuild = false;
$nodeMajor = 12;
for ($i = 0; $i < count($argv); $i++) {
    if ($argv[$i] === '--tag' && isset($argv[$i+1])) { $tag = $argv[$i+1]; $i++; continue; }
    if ($argv[$i] === '--node' && isset($argv[$i+1])) {
        $val = (int)$argv[$i+1];
        if ($val < 8 || $val > 22) { diex('Invalid --node value. Provide a major version like 12, 14, 16, 18.'); }
        $nodeMajor = $val; $i++; continue;
    }
    if ($argv[$i] === '--skip-build') { $skipBuild = true; continue; }
}

$root = realpath(__DIR__.'/..') ?: diex('Cannot resolve root directory');
$slug = 'jupiterx-core';

ensureCmd('svn', 'Install Subversion (e.g., apt install subversion).');
ensureCmd('rsync', 'Install rsync (e.g., apt install rsync).');
ensureCmd('git', 'Install git (e.g., apt install git).');
ensureCmd('node', 'Install Node.js (e.g., nvm use 12).');
ensureCmd('npm', 'Install Node.js/npm. Use nvm to switch versions.');
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

// Abort if SVN tag already exists
$svnBaseUrl = 'https://plugins.svn.wordpress.org/'.$slug;
[$svnTagCode] = run(sprintf('svn ls %s', escapeshellarg($svnBaseUrl.'/tags/'.$version)), null, true);
if ($svnTagCode === 0) {
    diex('SVN tag already exists: '.$svnBaseUrl.'/tags/'.$version);
}

// Ask for commit and tag messages with sensible defaults
$defaultCommit = 'Release '.$slug.' '.$version;
$defaultTagMsg = 'Tag '.$slug.' '.$version;
if (function_exists('Laravel\\Prompts\\text')) {
    $commitMsg = (string)\Laravel\Prompts\text('SVN commit message', $defaultCommit);
    $tagMsg = (string)\Laravel\Prompts\text('SVN tag message', $defaultTagMsg);
} else {
    $commitMsg = promptDefault('SVN commit message', $defaultCommit);
    $tagMsg = promptDefault('SVN tag message', $defaultTagMsg);
}

// Build is a separate process; validate Node and guide if mismatch
[$_, $nodeOut] = run('node -v', null, true);
if (!empty($nodeOut) && !preg_match('/^v'.((int)$nodeMajor).'\./', trim($nodeOut))) {
    warn('Detected '.trim($nodeOut).'. Build should be done with Node '.$nodeMajor.' (nvm use '.$nodeMajor.').');
}

say('Preparing clean export from built ZIP');
$defaultZipVersioned = $root.'/jupiterx-core-'.$version.'.zip';
$defaultZip = is_file($defaultZipVersioned) ? $defaultZipVersioned : ($root.'/jupiterx-core.zip');
$zipPath = promptDefault('Path to built ZIP (from gulp release)', is_file($defaultZip) ? $defaultZip : '');
if ($zipPath === '' || !is_file($zipPath)) {
    diex('Built ZIP not found. Run the build first: nvm use '.$nodeMajor.' && npm ci && npx gulp release');
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

say('Creating temp SVN checkout');
$svnUrl = 'https://plugins.svn.wordpress.org/'.$slug;
$svnDir = $workdir.'/svn';
if (!mkdir($svnDir, 0755, true) && !is_dir($svnDir)) diex('Failed to create svn directory');
run(sprintf('svn checkout --depth=immediates %s %s', escapeshellarg($svnUrl), escapeshellarg($svnDir)));
run(sprintf('svn checkout --depth=immediates %s %s', escapeshellarg($svnUrl.'/trunk'), escapeshellarg($svnDir.'/trunk')));
run(sprintf('svn checkout --depth=empty %s %s', escapeshellarg($svnUrl.'/tags'), escapeshellarg($svnDir.'/tags')));

say('Syncing trunk');
run(sprintf('rsync -rc --delete %s %s', escapeshellarg($buildDir.'/'), escapeshellarg($svnDir.'/trunk/')));

// No assets sync here; deploy exactly ZIP contents

// svn add/remove missing in trunk and assets
foreach (['trunk'] as $sub) {
    if ($sub === null) continue;
    $path = $svnDir.'/'.$sub;
    // add
    run('svn add --force .', $path, true);
    // remove missing
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
}

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
$svnPassword = prompt('SVN password (hidden):', true);
$svnFlags = ' --non-interactive --no-auth-cache';
if ($svnPassword !== '') { $svnFlags .= sprintf(' --password %s', escapeshellarg($svnPassword)); }

say('Committing trunk');
[$commitCode, , $commitErr] = run(sprintf('svn commit --username %s%s -m %s trunk', escapeshellarg($svnUser), $svnFlags, escapeshellarg($commitMsg)), $svnDir, true);
if ($commitCode !== 0 && stripos($commitErr, 'No changes to commit') === false) {
    diex('SVN commit failed: '.$commitErr);
}

say('Tagging '.$version);
run(sprintf('svn copy %s %s -m %s --username %s%s',
    escapeshellarg($svnUrl.'/trunk'),
    escapeshellarg($svnUrl.'/tags/'.$version),
    escapeshellarg($tagMsg),
    escapeshellarg($svnUser),
    $svnFlags
));

// Clean up worktree
run(sprintf('git worktree remove --force %s', escapeshellarg($srcDir)), $root, true);
// Cleanup temp directory
run(sprintf('rm -rf %s', escapeshellarg($workdir)), null, true);

say('Deploy complete.');
say('Verify at: '.$svnUrl);


