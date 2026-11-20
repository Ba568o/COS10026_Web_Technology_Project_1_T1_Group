<?php
require_once __DIR__.'/auth/session.php';
auth_require_hr();
$id=filter_input(INPUT_GET,'eoi',FILTER_VALIDATE_INT);
if(!$id || $id<1){http_response_code(400);exit('Invalid EOI.');}
try {
 $stmt=auth_database()->prepare('SELECT cv_name,cv_mime,cv FROM eoi WHERE EOInumber=?');$stmt->execute([$id]);$cv=$stmt->fetch();
 if(!$cv || $cv['cv']===''){http_response_code(404);exit('CV not found.');}
 $name=str_replace(["\r","\n"],'',basename($cv['cv_name']));
 header('X-Content-Type-Options: nosniff');
 header("Content-Security-Policy: sandbox; default-src 'none'");
 header('Content-Type: application/octet-stream');
 header("Content-Disposition: attachment; filename=\"cv-".$id."\"; filename*=UTF-8''".rawurlencode($name));
 header('Content-Length: '.strlen($cv['cv']));echo $cv['cv'];
} catch(PDOException $e){error_log($e->getMessage());http_response_code(503);echo 'CV unavailable.';}
