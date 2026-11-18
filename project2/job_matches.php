<?php
/** Open roles from jobs.php, tagged with the same skill values as the apply form. */
function job_catalog(): array
{
    return [
        ['reference' => 'DHS02', 'title' => ['Digital Health Support Specialist', 'Chuyên viên hỗ trợ sức khỏe số'], 'company' => ['T1 Health & Wellness', 'T1 Sức khỏe & Wellness'], 'place' => ['Ho Chi Minh City', 'Thành phố Hồ Chí Minh'], 'skills' => ['html-css', 'project-management']],
        ['reference' => 'UXD04', 'title' => ['Healthcare UX/UI Designer', 'Nhà thiết kế UX/UI chăm sóc sức khỏe'], 'company' => ['Vinmec Digital Health', 'Vinmec Sức khỏe số'], 'place' => ['Hanoi', 'Hà Nội'], 'skills' => ['html-css', 'javascript']],
        ['reference' => 'DHD05', 'title' => ['Digital Health Software Developer', 'Lập trình viên phần mềm sức khỏe số'], 'company' => ['FPT Smart Healthcare', 'FPT Chăm sóc sức khỏe thông minh'], 'place' => ['Da Nang', 'Đà Nẵng'], 'skills' => ['html-css', 'javascript', 'php', 'sql', 'python']],
        ['reference' => 'QAT06', 'title' => ['Healthcare Quality Assurance Tester', 'Kiểm thử đảm bảo chất lượng chăm sóc sức khỏe'], 'company' => ['Medlatec Digital', 'Medlatec Số'], 'place' => ['Hanoi', 'Hà Nội'], 'skills' => ['javascript', 'php', 'sql']],
        ['reference' => 'HDA03', 'title' => ['Health Data Analyst', 'Chuyên viên phân tích dữ liệu sức khỏe'], 'company' => ['VNG HealthTech', 'VNG HealthTech'], 'place' => ['Ho Chi Minh City', 'Thành phố Hồ Chí Minh'], 'skills' => ['sql', 'python']],
        ['reference' => 'HCW01', 'title' => ['Healthcare Content Writer', 'Biên tập viên nội dung chăm sóc sức khỏe'], 'company' => ['eDoctor Vietnam', 'eDoctor Việt Nam'], 'place' => ['Can Tho', 'Cần Thơ'], 'skills' => ['html-css', 'project-management']],
    ];
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
 * With no selected skills, every open role stays available.
 */
function job_match_list(array $userSkills, array $query, int $perPage = 3): array
{
    $userSkills = job_known_skills($userSkills);
    $skill = $query['skill'] ?? '';
    if (!is_string($skill) || !in_array($skill, $userSkills ?: array_keys(job_skill_labels()), true)) {
        $skill = '';
    }
    $q = $query['q'] ?? '';
    $q = is_string($q) && preg_match('//u', $q) ? mb_substr(trim($q), 0, 80) : '';
    $needle = mb_strtolower($q, 'UTF-8');

    $rows = [];
    foreach (job_catalog() as $job) {
        $overlap = array_values(array_intersect($job['skills'], $userSkills));
        if ($userSkills && !$overlap) {
            continue;
        }
        if ($skill !== '' && !in_array($skill, $job['skills'], true)) {
            continue;
        }
        if ($needle !== '') {
            $haystack = mb_strtolower(implode(' ', [$job['reference'], $job['title'][0], $job['title'][1], $job['company'][0], $job['company'][1], $job['place'][0], $job['place'][1]]), 'UTF-8');
            if (!str_contains($haystack, $needle)) {
                continue;
            }
        }
        $job['overlap'] = $userSkills ? $overlap : $job['skills'];
        $job['score'] = count($overlap);
        $rows[] = $job;
    }
    usort($rows, function (array $a, array $b): int {
        if ($a['score'] !== $b['score']) {
            return $b['score'] <=> $a['score'];
        }
        return $a['reference'] <=> $b['reference'];
    });

    $perPage = max(1, min(10, $perPage));
    $total = count($rows);
    $pages = max(1, (int) ceil($total / $perPage));
    $page = filter_var($query['page'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $page = max(1, min($pages, $page ?: 1));
    return [
        'rows' => array_slice($rows, ($page - 1) * $perPage, $perPage),
        'total' => $total,
        'page' => $page,
        'pages' => $pages,
        'skill' => $skill,
        'q' => $q,
        'user_skills' => $userSkills,
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
