<?php
// Database helpers for managing, seeding, loading, and filtering job listings.
// Uses database_connection() from settings.php and falls back to jobs_seed.php if MySQL is unavailable.
require_once __DIR__ . '/../settings.php';

// Creates the jobs table if it does not exist yet, or adds any missing columns to an older 7-column table.
function jobs_ensure_table(PDO $db): void
{
    // Define the extended columns once so both CREATE TABLE and ALTER TABLE stay in sync.
    // TEXT and LONGTEXT columns stay nullable because MySQL does not allow defaults on them when altering a populated table.
    $extraCols = [
        'category_en' => "VARCHAR(120) NOT NULL DEFAULT ''",
        'category_vi' => "VARCHAR(120) NOT NULL DEFAULT ''",
        'description_en' => 'TEXT NULL',
        'description_vi' => 'TEXT NULL',
        'reports_to_en' => "VARCHAR(120) NOT NULL DEFAULT ''",
        'reports_to_vi' => "VARCHAR(120) NOT NULL DEFAULT ''",
        'salary_display_en' => "VARCHAR(80) NOT NULL DEFAULT ''",
        'salary_display_vi' => "VARCHAR(80) NOT NULL DEFAULT ''",
        'salary_min' => 'INT UNSIGNED NULL DEFAULT NULL',
        'salary_max' => 'INT UNSIGNED NULL DEFAULT NULL',
        'location_key' => "VARCHAR(20) NOT NULL DEFAULT ''",
        'job_type_key' => "VARCHAR(20) NOT NULL DEFAULT ''",
        'experience_key' => "VARCHAR(20) NOT NULL DEFAULT ''",
        'experience_years' => 'TINYINT UNSIGNED NOT NULL DEFAULT 0',
        'experience_en' => "VARCHAR(80) NOT NULL DEFAULT ''",
        'experience_vi' => "VARCHAR(80) NOT NULL DEFAULT ''",
        'responsibilities_json' => 'LONGTEXT NULL',
        'requirements_json' => 'LONGTEXT NULL',
        'benefits_json' => 'LONGTEXT NULL',
        'apply_position' => "VARCHAR(30) NOT NULL DEFAULT ''",
        'sort_order' => 'TINYINT UNSIGNED NOT NULL DEFAULT 1',
    ];

    $extraSql = '';
    foreach ($extraCols as $col => $def) {
        $extraSql .= ",\n        $col $def";
    }

    // Keep the original 7 columns from job_matches.php so skill matching keeps working.
    // Avoid separate CREATE INDEX statements because the local t1_local user only has PRIMARY KEY / ALTER privileges.
    $db->exec("CREATE TABLE IF NOT EXISTS jobs (
        reference CHAR(5) NOT NULL PRIMARY KEY,
        title_en VARCHAR(120) NOT NULL,
        title_vi VARCHAR(120) NOT NULL,
        company_en VARCHAR(120) NOT NULL,
        company_vi VARCHAR(120) NOT NULL,
        place_en VARCHAR(80) NOT NULL,
        place_vi VARCHAR(80) NOT NULL$extraSql
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Upgrade older 7-column tables in place by adding whichever extended columns are missing.
    $existing = array_map('strtolower', $db->query('SHOW COLUMNS FROM jobs')->fetchAll(PDO::FETCH_COLUMN));
    foreach ($extraCols as $col => $def) {
        if (!in_array($col, $existing, true)) {
            $db->exec("ALTER TABLE jobs ADD COLUMN $col $def");
        }
    }
}

// Inserts the default jobs from jobs_seed.php when the table is empty, and fixes HCW01's heading if needed.
function jobs_seed_if_empty(PDO $db): void
{
    // Only seed when the table has zero rows so manual edits or extra jobs are never overwritten.
    if ((int) $db->query('SELECT COUNT(*) FROM jobs')->fetchColumn() === 0) {
        $insert = $db->prepare(
            'INSERT INTO jobs (
                reference, title_en, title_vi, company_en, company_vi, place_en, place_vi,
                category_en, category_vi, description_en, description_vi,
                reports_to_en, reports_to_vi, salary_display_en, salary_display_vi,
                salary_min, salary_max, location_key, job_type_key, experience_key,
                experience_years, experience_en, experience_vi,
                responsibilities_json, requirements_json, benefits_json, apply_position, sort_order
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
        );

        foreach (require __DIR__ . '/jobs_seed.php' as $job) {
            // Store bilingual bullet lists as UTF-8 JSON strings so one column holds both EN and VI arrays.
            $insert->execute([
                $job['reference'],
                $job['title']['en'],
                $job['title']['vi'],
                $job['company']['en'],
                $job['company']['vi'],
                $job['location_display']['en'],
                $job['location_display']['vi'],
                $job['category_heading']['en'],
                $job['category_heading']['vi'],
                $job['description']['en'],
                $job['description']['vi'],
                $job['reports_to']['en'],
                $job['reports_to']['vi'],
                $job['salary_display']['en'],
                $job['salary_display']['vi'],
                $job['salary_min'],
                $job['salary_max'],
                $job['location_key'],
                $job['job_type_key'],
                $job['experience_key'],
                $job['experience_years'],
                $job['experience_display']['en'],
                $job['experience_display']['vi'],
                json_encode($job['responsibilities'], JSON_UNESCAPED_UNICODE),
                json_encode($job['requirements'], JSON_UNESCAPED_UNICODE),
                json_encode($job['benefits'], JSON_UNESCAPED_UNICODE),
                $job['apply_position'],
                $job['sort_order'],
            ]);
        }

        // Record the seed in app_migrations when running alongside setup_portals.php.
        if ($db->query("SHOW TABLES LIKE 'app_migrations'")->fetchColumn()) {
            $db->exec("INSERT IGNORE INTO app_migrations (name) VALUES ('jobs_seed')");
        }
    }

    // Normalize HCW01 if an older row still has an empty heading or the old "Description" label.
    $db->exec(
        "UPDATE jobs
         SET category_en = 'Content Writing | Full-time', category_vi = 'Viết nội dung | Toàn thời gian'
         WHERE reference = 'HCW01'
           AND (category_en IS NULL OR category_en = '' OR LOWER(TRIM(category_en)) = 'description')"
    );
}

// Loads all job listings ordered for display, falling back to jobs_seed.php if MySQL fails.
function jobs_fetch_all(): array
{
    try {
        // Make sure the table exists and is seeded before querying.
        $db = database_connection();
        jobs_ensure_table($db);
        jobs_seed_if_empty($db);

        $rows = $db->query('SELECT * FROM jobs ORDER BY sort_order ASC, reference ASC')->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) {
            return require __DIR__ . '/jobs_seed.php';
        }

        // Decode JSON list columns safely and guarantee both 'en' and 'vi' keys exist.
        $decode = static fn(?string $json): array =>
            (is_array($d = json_decode($json ?? '', true)) ? $d : []) + ['en' => [], 'vi' => []];

        // Reshape flat MySQL columns back into the nested bilingual structure expected by templates.
        $jobs = [];
        foreach ($rows as $row) {
            $jobs[] = [
                'reference' => $row['reference'],
                'title' => ['en' => $row['title_en'], 'vi' => $row['title_vi']],
                'company' => ['en' => $row['company_en'], 'vi' => $row['company_vi']],
                'category_heading' => ['en' => (string) $row['category_en'], 'vi' => (string) $row['category_vi']],
                'description' => ['en' => (string) $row['description_en'], 'vi' => (string) $row['description_vi']],
                'reports_to' => ['en' => (string) $row['reports_to_en'], 'vi' => (string) $row['reports_to_vi']],
                'salary_display' => ['en' => (string) $row['salary_display_en'], 'vi' => (string) $row['salary_display_vi']],
                'salary_min' => $row['salary_min'] === null ? null : (int) $row['salary_min'],
                'salary_max' => $row['salary_max'] === null ? null : (int) $row['salary_max'],
                'location_key' => (string) $row['location_key'],
                'location_display' => ['en' => $row['place_en'], 'vi' => $row['place_vi']],
                'job_type_key' => (string) $row['job_type_key'],
                'experience_key' => (string) $row['experience_key'],
                'experience_years' => (int) $row['experience_years'],
                'experience_display' => ['en' => (string) $row['experience_en'], 'vi' => (string) $row['experience_vi']],
                'responsibilities' => $decode($row['responsibilities_json']),
                'requirements' => $decode($row['requirements_json']),
                'benefits' => $decode($row['benefits_json']),
                'apply_position' => (string) $row['apply_position'],
                'sort_order' => (int) $row['sort_order'],
            ];
        }
        return $jobs;
    } catch (Throwable $e) {
        // Log the error for debugging, but keep the Jobs and Apply pages working using the local seed array.
        error_log('jobs_fetch_all: ' . $e->getMessage());
        return require __DIR__ . '/jobs_seed.php';
    }
}

// Finds a single job by its 5-character reference code (ignoring case and extra spaces).
function jobs_find_by_reference(string $ref): ?array
{
    $target = strtoupper(trim($ref));
    if ($target === '') {
        return null;
    }
    foreach (jobs_fetch_all() as $job) {
        if (strtoupper(trim($job['reference'])) === $target) {
            return $job;
        }
    }
    return null;
}

// Checks whether a job's USD salary range matches the selected filter bucket or custom min/max range.
function jobs_matches_salary(array $job, string $salaryKey, ?int $customMin = null, ?int $customMax = null): bool
{
    if ($salaryKey === 'all') {
        return true;
    }

    $min = isset($job['salary_min']) && is_numeric($job['salary_min']) ? (int) $job['salary_min'] : null;
    $max = isset($job['salary_max']) && is_numeric($job['salary_max']) ? (int) $job['salary_max'] : null;
    $negotiable = ($min === null || $max === null);

    // Handle negotiable jobs separately so null bounds never evaluate as < 1200 and leak into numeric buckets.
    if ($salaryKey === 'negotiable') {
        return $negotiable;
    }
    if ($negotiable) {
        return false;
    }

    // For custom "other" ranges, swap reversed inputs and check if [min, max] overlaps [customMin, customMax].
    if ($salaryKey === 'other') {
        if ($customMin !== null && $customMax !== null && $customMin > $customMax) {
            [$customMin, $customMax] = [$customMax, $customMin];
        }
        return ($customMin === null || $max >= $customMin) && ($customMax === null || $min <= $customMax);
    }

    // Match preset USD salary buckets by checking interval overlap with the job's [min, max] range.
    return match ($salaryKey) {
        'under-1200' => $min < 1200,
        '400-800' => $max >= 400 && $min <= 800,
        '800-1200' => $max >= 800 && $min < 1200,
        '1200-2000' => $max >= 1200 && $min <= 2000,
        'over-2000' => $max > 2000,
        default => false,
    };
}
