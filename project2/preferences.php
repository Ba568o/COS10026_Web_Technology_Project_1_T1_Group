<?php
/**
 * preferences.php — saves the display settings from the gear menu in nav.inc
 * (dark mode, language, font size) so every page opens with the same choices.
 *
 * Flow (no JavaScript):
 *   click an option in nav.inc --POST (one setting)--> preferences.php --303 redirect--> the same page
 *   header.inc reads the cookies on every page and nav.inc prints "checked" on the saved options.
 *   The existing CSS (:has(#dark-toggle:checked), #language-vi:checked, #font-large:checked ...)
 *   then shows the right theme, language and text size without any change.
 *
 * Storage: three cookies (t1_theme, t1_lang, t1_font), kept for one year.
 * Cookies are used instead of the session so the choice survives closing the browser
 * and works for visitors who never log in. No personal data is stored.
 *
 * Not to be confused with settings.php, which holds the database settings.
 */

// Allowed values. Anything else is ignored and the default is kept.
const PREF_THEMES = ['light', 'dark'];
const PREF_LANGUAGES = ['en', 'vi'];
const PREF_FONTS = ['super-small', 'small', 'normal', 'large', 'super-large'];
const PREF_COOKIE_DAYS = 365;

/**
 * Returns a safe page to go back to: a .php file that exists in this folder, plus its query string.
 * The value comes from a hidden form field, so it is checked strictly:
 * no other folder, no other website (prevents an "open redirect").
 */
function preferences_return_page($value): string
{
    if (!is_string($value) || $value === '') return 'index.php';
    $parts = parse_url($value);
    // A full address (scheme/host) or a missing path is not allowed.
    if ($parts === false || isset($parts['scheme']) || isset($parts['host']) || empty($parts['path'])) return 'index.php';

    $page = $parts['path'];
    // Only plain file names such as "jobs.php" or "hr_login.php"; no slashes, no "..".
    if (!preg_match('/^[a-z0-9_]+\.php$/', $page) || !is_file(__DIR__ . '/' . $page)) return 'index.php';
    // Pages that only accept POST would just bounce, so send the visitor to a normal page instead.
    if (in_array($page, ['preferences.php', 'process_eoi.php', 'logout.php'], true)) return 'index.php';

    // Rebuild the query string from its parsed values so nothing unexpected is passed on.
    $query = [];
    if (!empty($parts['query'])) parse_str($parts['query'], $query);
    return $page . ($query ? '?' . http_build_query($query) : '');
}

/** Saves one cookie for this site's folder, readable by PHP only (HttpOnly). */
function preferences_save(string $name, string $value): void
{
    // Limit the cookie to the folder of this site (e.g. /project2/ on XAMPP, / with php -S).
    $path = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/') . '/';
    setcookie($name, $value, [
        'expires' => time() + PREF_COOKIE_DAYS * 24 * 60 * 60,
        'path' => $path,
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

// Only the gear-menu form may use this page. Opening it directly goes to the home page.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php', true, 303);
    exit;
}

// Each button in the gear menu sends ONE setting (e.g. language=vi). Only the settings that were sent
// are saved; the others keep their current cookie. Values outside the allowed lists are ignored.
$theme = is_string($_POST['theme'] ?? null) && in_array($_POST['theme'], PREF_THEMES, true) ? $_POST['theme'] : null;
$language = is_string($_POST['language'] ?? null) && in_array($_POST['language'], PREF_LANGUAGES, true) ? $_POST['language'] : null;
$font = is_string($_POST['font_size'] ?? null) && in_array($_POST['font_size'], PREF_FONTS, true) ? $_POST['font_size'] : null;

if ($theme !== null) preferences_save('t1_theme', $theme);
if ($language !== null) preferences_save('t1_lang', $language);
if ($font !== null) preferences_save('t1_font', $font);

// 303 makes the browser load the page again with GET, now with the saved choices.
header('Location: ' . preferences_return_page($_POST['return'] ?? ''), true, 303);
exit;
