<?php
/** Hoang
 * lang.php — Shared language helpers for T1 Health & Wellness.
 * Reads the t1_lang cookie saved by preferences.php ('en' default, 'vi' for Vietnamese).
 */

/** Returns the active language code: 'vi' or 'en'. */
function t1_lang(): string
{
    return (($_COOKIE['t1_lang'] ?? '') === 'vi') ? 'vi' : 'en';
}

/** Returns true when Vietnamese is active. */
function t1_is_vi(): bool
{
    return t1_lang() === 'vi';
}

/** Returns the English or Vietnamese string for the active language. */
function t1_text(string $en, string $vi): string
{
    return t1_is_vi() ? $vi : $en;
}

/** Returns the HTML-escaped English or Vietnamese string for the active language. */
function t1_e(string $en, string $vi): string
{
    return htmlspecialchars(t1_text($en, $vi), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Picks the active language entry from a ['en' => ..., 'vi' => ...] array. */
function t1_pick(array $map, string $fallback = ''): string
{
    $lang = t1_lang();
    if (isset($map[$lang]) && is_string($map[$lang]) && $map[$lang] !== '') {
        return $map[$lang];
    }
    if (isset($map['en']) && is_string($map['en']) && $map['en'] !== '') {
        return $map['en'];
    }
    return $fallback;
}

/** Picks the active language list from a ['en' => [...], 'vi' => [...]] array. */
function t1_pick_list(array $map): array
{
    $lang = t1_lang();
    if (isset($map[$lang]) && is_array($map[$lang]) && $map[$lang] !== []) {
        return $map[$lang];
    }
    if (isset($map['en']) && is_array($map['en'])) {
        return $map['en'];
    }
    return [];
}