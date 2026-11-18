<?php
require_once __DIR__ . '/components/listing.php';

/**
 * Open roles are read from MySQL. This file does not insert any rows.
 * Seed jobs(reference, title_en, title_vi, company_en, company_vi, place_en, place_vi)
 * and job_skills(reference, skill). Skill values match the apply form:
 * html-css, javascript, php, sql, python, project-management.
 */
function job_ensure_tables(PDO $db): void
{
    $db->exec("CREATE TABLE IF NOT EXISTS jobs (
        reference CHAR(5) NOT NULL PRIMARY KEY,
        title_en VARCHAR(120) NOT NULL,
        title_vi VARCHAR(120) NOT NULL,
        company_en VARCHAR(120) NOT NULL,
        company_vi VARCHAR(120) NOT NULL,
        place_en VARCHAR(80) NOT NULL,
        place_vi VARCHAR(80) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $db->exec("CREATE TABLE IF NOT EXISTS job_skills (
        reference CHAR(5) NOT NULL,
        skill VARCHAR(30) NOT NULL,
        PRIMARY KEY (reference, skill),
        CONSTRAINT job_skills_reference FOREIGN KEY (reference) REFERENCES jobs (reference)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

function job_skill_labels(): array
{
    return [
        'html-css' => ['HTML/CSS', 'HTML/CSS'],
        'javascript' => ['JavaScript', 'JavaScript'],
        'php' => ['PHP', 'PHP'],
        'sql' => ['SQL', 'SQL'],
        'python' => ['Python', 'Python'],
        'project-management' => ['Project management', 'Quản lý dự án'],
    ];
}

/** Keeps a skill list to the values the apply form is allowed to submit. */
function job_known_skills(array $skills): array
{
    $labels = job_skill_labels();
    $known = [];
    foreach ($skills as $skill) {
        if (is_string($skill) && isset($labels[$skill])) {
            $known[$skill] = $skill;
        }
    }
    return array_values($known);
}

/**
 * Jobs that share a skill with the applicant, then filtered and paged.
 * With no selected skills, every stored role stays available.
 */
function job_match_list(PDO $db, array $userSkills, array $query, int $perPage = 3): array
{
    job_ensure_tables($db);
    $userSkills = job_known_skills($userSkills);
    $allowed = $userSkills ?: array_keys(job_skill_labels());
    $skill = $query['skill'] ?? '';
    if (!is_string($skill) || !in_array($skill, $allowed, true)) {
        $skill = '';
    }
    $q = $query['q'] ?? '';
    $q = is_string($q) && preg_match('//u', $q) ? mb_substr(trim($q), 0, 80) : '';

    $params = [];
    if ($userSkills) {
        $marks = implode(',', array_fill(0, count($userSkills), '?'));
        $join = "INNER JOIN job_skills matched ON matched.reference = jobs.reference AND matched.skill IN ($marks)";
        $params = $userSkills;
        $score = 'COUNT(DISTINCT matched.skill)';
    } else {
        $join = 'LEFT JOIN job_skills matched ON matched.reference = jobs.reference';
        $score = '0';
    }
    $where = [];
    if ($skill !== '') {
        $where[] = 'EXISTS (SELECT 1 FROM job_skills chosen WHERE chosen.reference = jobs.reference AND chosen.skill = ?)';
        $params[] = $skill;
    }
    if ($q !== '') {
        $term = '%' . strtr($q, ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
        $where[] = "(jobs.reference LIKE ? ESCAPE '!' OR jobs.title_en LIKE ? ESCAPE '!' OR jobs.title_vi LIKE ? ESCAPE '!' OR jobs.company_en LIKE ? ESCAPE '!' OR jobs.company_vi LIKE ? ESCAPE '!' OR jobs.place_en LIKE ? ESCAPE '!' OR jobs.place_vi LIKE ? ESCAPE '!')";
        array_push($params, $term, $term, $term, $term, $term, $term, $term);
    }
    $clause = $where ? ' WHERE ' . implode(' AND ', $where) : '';
    $group = ' GROUP BY jobs.reference, jobs.title_en, jobs.title_vi, jobs.company_en, jobs.company_vi, jobs.place_en, jobs.place_vi';
    $from = "FROM jobs $join$clause";

    $stored = (int) $db->query('SELECT COUNT(*) FROM jobs')->fetchColumn();
    $count = $db->prepare("SELECT COUNT(*) FROM (SELECT jobs.reference $from$group) matched_jobs");
    $count->execute($params);
    $pagination = listing_pagination((int) $count->fetchColumn(), (int) (is_scalar($query['page'] ?? 1) ? $query['page'] : 1), $perPage);

    $statement = $db->prepare("SELECT jobs.reference, jobs.title_en, jobs.title_vi, jobs.company_en, jobs.company_vi, jobs.place_en, jobs.place_vi, GROUP_CONCAT(DISTINCT matched.skill ORDER BY matched.skill SEPARATOR ',') AS skills, $score AS score $from$group ORDER BY score DESC, jobs.reference ASC LIMIT ? OFFSET ?");
    foreach ($params as $i => $value) {
        $statement->bindValue($i + 1, $value, PDO::PARAM_STR);
    }
    $statement->bindValue(count($params) + 1, $pagination['per_page'], PDO::PARAM_INT);
    $statement->bindValue(count($params) + 2, $pagination['offset'], PDO::PARAM_INT);
    $statement->execute();

    $rows = [];
    foreach ($statement->fetchAll() as $job) {
        $stored = $job['skills'] !== null && $job['skills'] !== '' ? explode(',', $job['skills']) : [];
        $rows[] = [
            'reference' => $job['reference'],
            'title' => [$job['title_en'], $job['title_vi']],
            'company' => [$job['company_en'], $job['company_vi']],
            'place' => [$job['place_en'], $job['place_vi']],
            'overlap' => $stored,
            'score' => (int) $job['score'],
        ];
    }
    return [
        'rows' => $rows,
        'total' => $pagination['total'],
        'page' => $pagination['page'],
        'pages' => $pagination['pages'],
        'skill' => $skill,
        'q' => $q,
        'user_skills' => $userSkills,
        'stored' => $stored,
        'error' => false,
    ];
}

function job_match_href(array $matches, int $page): string
{
    $params = ['page' => $page];
    if ($matches['skill'] !== '') {
        $params['skill'] = $matches['skill'];
    }
    if ($matches['q'] !== '') {
        $params['q'] = $matches['q'];
    }
    return 'eoi_confirmation.php?' . http_build_query($params);
}
