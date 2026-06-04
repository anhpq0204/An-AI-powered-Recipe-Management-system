<?php
/**
 * Returns Font Awesome class for a star position given an average rating.
 * Rounds avg to nearest 0.5 before comparing.
 *
 * @param float $avg  Average rating (e.g. 3.7)
 * @param int   $pos  Star position 1–5
 * @return string  'fa-star' | 'fa-star-half-o' | 'fa-star-o'
 */
function star_class(float $avg, int $pos): string {
    $rounded = round($avg * 2) / 2;
    if ($rounded >= $pos)          return 'fa-star';
    if ($rounded >= $pos - 0.5)   return 'fa-star-half-o';
    return 'fa-star-o';
}

/**
 * Renders 5 star icons for display (read-only).
 *
 * @param float $avg
 * @param int   $count
 * @param bool  $showCount
 */
function render_stars(float $avg, int $count = 0, bool $showCount = true): void {
    if ($avg <= 0) return;
    echo '<div class="recipe-stars-row">';
    for ($s = 1; $s <= 5; $s++) {
        echo '<i class="fa ' . star_class($avg, $s) . ' star-icon"></i>';
    }
    echo '<span class="star-score">' . number_format($avg, 1) . '</span>';
    if ($showCount && $count > 0) {
        echo '<span class="star-count">(' . $count . ')</span>';
    }
    echo '</div>';
}
