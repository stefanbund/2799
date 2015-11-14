#! /usr/bin/php
<?php
// include shared code
include '../lib/config.php';
include '../lib/db.php';
include '../lib/POP3Client.php';

// retrieve users to receive digest messages
$query = sprintf('SELECT EMAIL_ADDR FROM %sMAILLIST_USER WHERE IS_DIGEST = 1',
    DB_TBL_PREFIX);
$result = mysql_query($query, $GLOBALS['DB']);

// open digest file
$time = strtotime('-1 day');
$digest = QUEUE_DIR . '/digest-' . date('Ymd', $time) . '.txt';
if (!file_exists($digest))
{
    // no digest file
    mysql_free_result($result);
    exit();
}
$body = file_get_contents($digest);

// send mail to each recipient
while ($row = mysql_fetch_assoc($result))
{
    mail($row['EMAIL_ADDR'], 'List Digest for ' . date('m/d/Y', $time),
        $body,
        'From: ' . LIST_EMAIL . "\r\n" .
        'Reply-To: ' . LIST_EMAIL . "\r\n");
}
mysql_free_result($result);
mysql_close($GLOBALS['DB']);

// delete digest file
unlink($digest);
?>
