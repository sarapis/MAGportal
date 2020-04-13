<?php
header('Content: application/json');
echo '{}';
$tmpFn = $_FILES['files1']['tmp_name'][0] ?? $_FILES['files2']['tmp_name'][0];
$tmpFs = $tmpFn ? filesize($tmpFn) : 'no file';
file_put_contents('deltest.lastreq', str_repeat("=", 80) . "\n" . print_r(['GET' => $_GET, 'POST' => $_POST, 'FILES' => $_FILES], true) . $tmpFs, FILE_APPEND);