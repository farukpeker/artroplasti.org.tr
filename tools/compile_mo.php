<?php
/**
 * Compiles a .po file into a binary .mo file (GNU gettext format).
 *
 * Usage:
 *   php tools/compile_mo.php <input.po> <output.mo>
 *   php tools/compile_mo.php  (uses default theme en_GB files)
 */

if (php_sapi_name() !== 'cli') { echo "CLI only.\n"; exit(1); }

$po_file = $argv[1] ?? dirname(__DIR__) . '/wp-content/themes/artroplasti/languages/artroplasti-en_GB.po';
$mo_file = $argv[2] ?? dirname(__DIR__) . '/wp-content/themes/artroplasti/languages/artroplasti-en_GB.mo';

if (!file_exists($po_file)) {
    echo "ERROR: .po file not found: {$po_file}\n"; exit(1);
}

// ── Parse .po file ────────────────────────────────────────────────────────────
function parse_po(string $file): array {
    $content = file_get_contents($file);
    $entries = [];
    $msgid   = null;
    $msgstr  = null;
    $in      = null; // 'id' | 'str'

    $lines = explode("\n", $content);
    foreach ($lines as $line) {
        $line = rtrim($line);

        if (str_starts_with($line, 'msgid "')) {
            // Save previous entry
            if ($msgid !== null && $msgstr !== null) {
                $entries[] = [$msgid, $msgstr];
            }
            $msgid  = stripcslashes(substr($line, 7, -1));
            $msgstr = null;
            $in     = 'id';
        } elseif (str_starts_with($line, 'msgstr "')) {
            $msgstr = stripcslashes(substr($line, 8, -1));
            $in     = 'str';
        } elseif (str_starts_with($line, '"') && str_ends_with($line, '"')) {
            $cont = stripcslashes(substr($line, 1, -1));
            if ($in === 'id')  $msgid  .= $cont;
            if ($in === 'str') $msgstr .= $cont;
        } elseif (trim($line) === '' || str_starts_with($line, '#')) {
            $in = null;
        }
    }
    // Last entry
    if ($msgid !== null && $msgstr !== null) {
        $entries[] = [$msgid, $msgstr];
    }
    return $entries;
}

// ── Build .mo binary ──────────────────────────────────────────────────────────
function build_mo(array $entries): string {
    // Filter out header (empty msgid) — we keep it first
    $header = null;
    $pairs  = [];
    foreach ($entries as [$id, $str]) {
        if ($id === '') {
            $header = [$id, $str];
        } else {
            $pairs[] = [$id, $str];
        }
    }
    // Sort by original string (required by some gettext implementations)
    usort($pairs, fn($a, $b) => strcmp($a[0], $b[0]));

    if ($header !== null) {
        array_unshift($pairs, $header);
    }

    $n = count($pairs);
    $o = 28;                   // offset of original strings table
    $t = $o + $n * 8;         // offset of translated strings table
    $s = 0;                    // hash table size
    $h = $t + $n * 8;         // offset of hash table (empty)

    // Build string pool
    $orig_pool  = '';
    $trans_pool = '';
    $orig_off   = [];
    $trans_off  = [];
    $pool_start = $h; // strings start right after header region

    $orig_cursor  = 0;
    $trans_cursor = 0;

    foreach ($pairs as [$id, $str]) {
        $orig_off[]   = [strlen($id),  $orig_cursor];
        $trans_off[]  = [strlen($str), $trans_cursor];
        $orig_pool   .= $id  . "\0";
        $trans_pool  .= $str . "\0";
        $orig_cursor  += strlen($id)  + 1;
        $trans_cursor += strlen($str) + 1;
    }

    $orig_data_start  = $pool_start;
    $trans_data_start = $pool_start + strlen($orig_pool);

    // Header
    $mo  = pack('V', 0x950412de); // magic (little-endian)
    $mo .= pack('V', 0);          // revision
    $mo .= pack('V', $n);
    $mo .= pack('V', $o);
    $mo .= pack('V', $t);
    $mo .= pack('V', $s);
    $mo .= pack('V', $h);

    // Original strings table
    foreach ($orig_off as [$len, $off]) {
        $mo .= pack('V', $len);
        $mo .= pack('V', $orig_data_start + $off);
    }

    // Translated strings table
    foreach ($trans_off as [$len, $off]) {
        $mo .= pack('V', $len);
        $mo .= pack('V', $trans_data_start + $off);
    }

    // String data
    $mo .= $orig_pool;
    $mo .= $trans_pool;

    return $mo;
}

$entries = parse_po($po_file);

// Remove entries with empty msgstr (untranslated)
$entries = array_filter($entries, fn($e) => $e[0] === '' || $e[1] !== '');
$entries = array_values($entries);

echo "Parsed " . (count($entries) - 1) . " translated entries from {$po_file}\n";

$mo_data = build_mo($entries);
file_put_contents($mo_file, $mo_data);

echo "Written " . strlen($mo_data) . " bytes to {$mo_file}\n";
echo "Done.\n";
