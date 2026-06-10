<?php
/**
 * One-off optimiser for recipe images that were uploaded before the
 * image pipeline existed (some are ~2 MB each).
 *
 * Usage (from project root):
 *   php deploy/optimize-existing-images.php           # dry run: report savings
 *   php deploy/optimize-existing-images.php --apply    # optimise in place
 *
 * With --apply, every original is first copied to user/images/_backup_original/
 * so nothing is lost. Re-running is safe (already-small images are skipped).
 */

require_once __DIR__ . '/../includes/image-helper.php';

$dir     = __DIR__ . '/../user/images';
$backup  = $dir . '/_backup_original';
$apply   = in_array('--apply', $argv, true);
$maxW    = 1200;
$quality = 82;

if (!is_dir($dir)) {
    fwrite(STDERR, "Image dir not found: $dir\n");
    exit(1);
}
if ($apply && !is_dir($backup)) {
    mkdir($backup, 0775, true);
}

$files = glob($dir . '/*.{jpg,jpeg,png,gif,JPG,JPEG,PNG,GIF}', GLOB_BRACE);
$totalBefore = 0;
$totalAfter  = 0;
$changed     = 0;

foreach ($files as $file) {
    $before = filesize($file);
    $totalBefore += $before;

    // Estimate result by optimising into a temp file.
    $tmp = tempnam(sys_get_temp_dir(), 'frsimg');
    frs_save_optimized_image($file, $tmp, $maxW, $quality);
    $after = filesize($tmp);

    $name = basename($file);
    if ($after > 0 && $after < $before * 0.95) {
        printf("%-40s %8s -> %8s  (-%d%%)\n", $name,
            human($before), human($after), round(100 * (1 - $after / $before)));
        $changed++;
        if ($apply) {
            copy($file, $backup . '/' . $name);  // keep original
            rename($tmp, $file);                  // replace with optimised
            $totalAfter += $after;
            continue;
        }
    } else {
        printf("%-40s %8s  (already optimal, skipped)\n", $name, human($before));
        $totalAfter += $before;
    }
    @unlink($tmp);
    if (!$apply) {
        $totalAfter += min($after ?: $before, $before);
    }
}

echo str_repeat('-', 70) . "\n";
printf("Files: %d   Candidates: %d\n", count($files), $changed);
printf("Total: %s -> %s  (saving ~%s)\n",
    human($totalBefore), human($totalAfter), human(max(0, $totalBefore - $totalAfter)));
echo $apply
    ? "Applied. Originals backed up in user/images/_backup_original/\n"
    : "Dry run. Re-run with --apply to optimise in place.\n";

function human(int $bytes): string
{
    if ($bytes >= 1048576) return round($bytes / 1048576, 1) . 'M';
    if ($bytes >= 1024)    return round($bytes / 1024) . 'K';
    return $bytes . 'B';
}
