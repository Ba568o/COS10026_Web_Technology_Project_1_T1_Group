<?php
/*
 * search_engine.php — PHP partial & fuzzy search helpers.
 * Shared by search.php (global search) and jobs.php (scoped job filter).
 */

/* Sanitize a raw query parameter: reject non-strings (?q[]=...) and cap at 100 chars. */
function t1_search_clean_query(mixed $raw): string
{
    if (!is_string($raw)) {
        return '';
    }
    $trimmed = trim($raw);
    if ($trimmed === '') {
        return '';
    }
    return function_exists('mb_substr')
        ? mb_substr($trimmed, 0, 100, 'UTF-8')
        : substr($trimmed, 0, 100);
}

/* Normalize text to lowercase ASCII with Vietnamese diacritics folded to plain letters. */
function t1_search_normalize(string $text): string
{
    $lower = function_exists('mb_strtolower')
        ? mb_strtolower($text, 'UTF-8')
        : strtolower($text);

    // Fold accented Vietnamese characters so unaccented queries (e.g. "thiet ke") still match.
    static $vietnameseMap = [
        'à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a',
        'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a',
        'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a',
        'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e',
        'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e',
        'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i',
        'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o',
        'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o',
        'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o',
        'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u',
        'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u',
        'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y',
        'đ' => 'd',
    ];

    $folded = strtr($lower, $vietnameseMap);
    $clean = preg_replace('/[^a-z0-9]+/', ' ', $folded);
    return trim((string) $clean);
}

/* Score a record against a query across weighted [text, weight] pairs.
 * Tiers per field: exact match (10x) -> partial substring (5x) -> fuzzy token match (1x-3x).*/
function t1_fuzzy_score(string $query, array $fields): int
{
    $normQuery = t1_search_normalize($query);
    if ($normQuery === '') {
        return 0;
    }

    $queryTokens = array_values(array_filter(
        explode(' ', $normQuery),
        static fn(string $tok): bool => strlen($tok) >= 2
    ));

    $total = 0;
    foreach ($fields as $entry) {
        if (!is_array($entry) || count($entry) < 2) {
            continue;
        }
        $weight = (int) $entry[1];
        $normField = t1_search_normalize((string) $entry[0]);
        if ($weight <= 0 || $normField === '') {
            continue;
        }

        // Tier 1: exact full-field match (e.g. job code "DHS02").
        if ($normField === $normQuery) {
            $total += $weight * 10;
            continue;
        }

        // Tier 2: partial / substring match (e.g. "ana" -> "Analyst").
        if (strpos($normField, $normQuery) !== false) {
            $total += $weight * 5;
            continue;
        }

        if ($queryTokens === []) {
            continue;
        }

        // Tier 3: token-level prefix/substring and typo-tolerant Levenshtein match.
        $fieldTokens = array_values(array_unique(array_filter(
            explode(' ', $normField),
            static fn(string $tok): bool => strlen($tok) >= 2
        )));

        foreach ($queryTokens as $qTok) {
            $qLen = strlen($qTok);
            $maxDist = $qLen >= 6 ? 2 : 1;
            $bestTokScore = 0;

            foreach ($fieldTokens as $fTok) {
                if (strpos($fTok, $qTok) !== false) {
                    $bestTokScore = $weight * 3;
                    break;
                }
                // PHP levenshtein() returns -1 if either string exceeds 255 bytes.
                if ($qLen >= 3 && $qLen <= 255 && strlen($fTok) <= 255) {
                    $dist = levenshtein($qTok, $fTok);
                    if ($dist > 0 && $dist <= $maxDist) {
                        $tokScore = $dist === 1 ? $weight * 2 : $weight;
                        if ($tokScore > $bestTokScore) {
                            $bestTokScore = $tokScore;
                        }
                    }
                }
            }

            $total += $bestTokScore;
        }
    }

    return $total;
}

/* Suggest the closest term from $vocabulary when no search results match, or null. */
function t1_search_suggest(string $query, array $vocabulary): ?string
{
    $normQuery = t1_search_normalize($query);
    $qLen = strlen($normQuery);
    if ($normQuery === '' || $qLen > 255) {
        return null;
    }

    $maxDist = $qLen >= 8 ? 3 : 2;
    $bestDist = $maxDist + 1;
    $bestWord = null;

    foreach ($vocabulary as $word) {
        $candidate = trim((string) $word);
        $normWord = t1_search_normalize($candidate);
        if ($normWord === '' || $normWord === $normQuery || strlen($normWord) > 255) {
            continue;
        }
        $dist = levenshtein($normQuery, $normWord);
        if ($dist > 0 && $dist < $bestDist) {
            $bestDist = $dist;
            $bestWord = $candidate;
        }
    }

    return $bestWord;
}

/* Return a whitespace-normalized, truncated, HTML-escaped excerpt for safe rendering. */
function t1_search_excerpt(string $text, int $maxLength = 160): string
{
    $collapsed = trim((string) preg_replace('/\s+/u', ' ', $text));
    if ($maxLength >= 4) {
        $len = function_exists('mb_strlen') ? mb_strlen($collapsed, 'UTF-8') : strlen($collapsed);
        if ($len > $maxLength) {
            $slice = function_exists('mb_substr')
                ? mb_substr($collapsed, 0, $maxLength - 3, 'UTF-8')
                : substr($collapsed, 0, $maxLength - 3);
            $collapsed = rtrim($slice) . '...';
        }
    }
    return htmlspecialchars($collapsed, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
