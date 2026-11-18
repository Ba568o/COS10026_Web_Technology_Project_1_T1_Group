<?php
session_start();
require_once __DIR__ . '/eoi_helpers.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: apply.php');
    exit;
}
[$data, $errors] = eoi_validate($_POST);
$token = $_POST['csrf_token'] ?? null;
if (!is_string($token) || empty($_SESSION['eoi_token']) || !hash_equals($_SESSION['eoi_token'], $token)) {
    $errors[] = 'Your form expired. Please submit again.';
}
$cv = $_FILES['cv'] ?? [];
$cvContent = null;
$cvMime = '';
$cvName = '';
if (!isset($cv['error']) || $cv['error'] !== UPLOAD_ERR_OK || !is_string($cv['tmp_name'] ?? null) || !is_uploaded_file($cv['tmp_name'])) {
    $errors[] = 'Please upload your CV (PDF, DOC or DOCX, maximum 5 MB).';
} elseif (filesize($cv['tmp_name']) > 5 * 1024 * 1024 || filesize($cv['tmp_name']) === 0) {
    $errors[] = 'CV must be non-empty and at most 5 MB.';
} else {
    $cvName = basename(str_replace('\\', '/', $cv['name']));
    $extension = strtolower(pathinfo($cvName, PATHINFO_EXTENSION));
    $cvMime = (new finfo(FILEINFO_MIME_TYPE))->file($cv['tmp_name']);
    $types = ['pdf'=>['application/pdf'], 'doc'=>['application/msword','application/CDFV2','application/x-ole-storage'],
        'docx'=>['application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/zip']];
    $valid = isset($types[$extension]) && in_array($cvMime, $types[$extension], true);
    if ($valid && $extension === 'docx') {
        $zip = new ZipArchive();
        $valid = $zip->open($cv['tmp_name']) === true;
        if ($valid) {
            $valid = $zip->locateName('[Content_Types].xml') !== false && $zip->locateName('word/document.xml') !== false;
            $zip->close();
        }
    }
    if (!$valid || strlen($cvName) > 255 || !preg_match('//u', $cvName)) $errors[] = 'CV must be a valid PDF, DOC or DOCX file with a filename under 256 bytes.';
    else $cvContent = file_get_contents($cv['tmp_name']);
}
if (!$errors) {
    try {
        require_once __DIR__ . '/settings.php';
        $db = database_connection();
        eoi_ensure_table($db);
        $values = $data;
        $values['skills'] = json_encode($data['skills'], JSON_THROW_ON_ERROR);
        $values['terms'] = 1;
        $values['cv_name'] = $cvName;
        $values['cv_mime'] = $cvMime;
        $values['cv'] = $cvContent;
        $fields = array_keys($values);
        $stmt = $db->prepare('INSERT INTO eoi (`' . implode('`,`', $fields) . '`) VALUES (' . implode(',', array_fill(0, count($fields), '?')) . ')');
        $stmt->execute(array_values($values));
        $_SESSION['eoi_receipt'] = ['number'=>$db->lastInsertId(), 'job'=>$data['job_reference']];
        unset($_SESSION['eoi_token'], $_SESSION['eoi_old'], $_SESSION['eoi_errors']);
        header('Location: eoi_confirmation.php', true, 303);
        exit;
    } catch (PDOException $error) {
        error_log('EOI database error: ' . $error->getMessage());
        $errors[] = 'We could not save your application. Please try again later or contact T1.';
    }
}
$_SESSION['eoi_old'] = $data;
$_SESSION['eoi_errors'] = $errors;
header('Location: apply.php#skip-to-application', true, 303);
exit;
