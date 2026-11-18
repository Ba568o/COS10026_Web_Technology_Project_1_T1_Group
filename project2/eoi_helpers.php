<?php
function eoi_escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function eoi_validate(array $post): array
{
    $data = [];
    $errors = [];
    $limits = ['first_name'=>50, 'last_name'=>50, 'date_of_birth'=>10, 'gender'=>10,
        'email'=>254, 'phone'=>15, 'street_address'=>100, 'suburb'=>50, 'state'=>3,
        'postcode'=>4, 'job_reference'=>5, 'position'=>30, 'other_skills'=>500, 'terms'=>2];
    foreach ($limits as $field => $limit) {
        $raw = $post[$field] ?? '';
        if (!is_string($raw)) {
            $errors[] = "Invalid field: $field.";
            $raw = '';
        }
        $value = trim(stripslashes($raw));
        $data[$field] = $value;
        if (($value === '' && !in_array($field, ['other_skills'], true)) ||
            !preg_match('//u', $value) || mb_strlen($value, 'UTF-8') > $limit) {
            $errors[] = "$field is required and must be at most $limit characters (other_skills is optional).";
        }
    }
    foreach (['first_name', 'last_name'] as $field) {
        if (!preg_match("/^[\\p{L}\\p{M}][\\p{L}\\p{M} '\\-]{1,49}$/u", $data[$field])) {
            $errors[] = "$field must contain 2–50 letters, spaces, apostrophes or hyphens.";
        }
    }
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $data['date_of_birth']);
    if (!$date || $date->format('Y-m-d') !== $data['date_of_birth'] || $date >= new DateTimeImmutable('today')) {
        $errors[] = 'Enter a valid date of birth in the past.';
    }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (!preg_match('/^[0-9 +\-]{8,15}$/', $data['phone']) || strlen(preg_replace('/\D/', '', $data['phone'])) < 8) $errors[] = 'Phone must contain at least 8 digits and be at most 15 characters.';
    if (!preg_match('/^[\p{L}\p{M} \-]{2,50}$/u', $data['suburb'])) $errors[] = 'Enter a valid suburb/city (2–50 letters, spaces or hyphens).';
    if (!preg_match('/^\d{4}$/', $data['postcode'])) $errors[] = 'Postcode must contain exactly four digits.';
    if (!preg_match('/^[A-Za-z0-9]{5}$/', $data['job_reference'])) $errors[] = 'Job reference must contain exactly five letters or digits.';
    foreach (['gender'=>['male','female','other'], 'state'=>['VIC','NSW','QLD','SA','WA','TAS','NT','ACT'], 'position'=>['president','vice-president','manager','developer','designer'], 'terms'=>['on']] as $field => $allowed) {
        if (!in_array($data[$field], $allowed, true)) $errors[] = "Please select a valid $field.";
    }
    $skills = $post['skills'] ?? [];
    $allowed = ['html-css','javascript','php','sql','python','project-management'];
    $data['skills'] = [];
    if (!is_array($skills) || count($skills) > count($allowed)) {
        $errors[] = 'Invalid skills selection.';
    } else {
        foreach ($skills as $skill) {
            if (!is_string($skill) || !in_array($skill, $allowed, true)) $errors[] = 'Invalid skills selection.';
            else $data['skills'][] = $skill;
        }
        $data['skills'] = array_values(array_unique($data['skills']));
    }
    return [$data, $errors];
}
