#! /usr/bin/php
<?php
// include shared code
include '../lib/config.php';
include '../lib/db.php';
include '../lib/POP3Client.php';

// open digest file for append, create if it doesn't exist
$digest = fopen(QUEUE_DIR . '/digest-' . date('Ymd') . '.txt', 'a+');

// establish a connection to the POP3 server
$pop = new POP3Client();
$pop->connect(POP3_SERVER, POP3_PORT);
$pop->user(LIST_USER) && $pop->pass(LIST_PASSWORD);

// process each message in inbox
foreach (array_keys($pop->_list()) as $id)
{
    // fetch message
    $message = $pop->retr($id);

    // retrieve the Date, From and Subject headers and multipart boundary
    // marker from the message headers
    preg_match_all('/Date: (.+)|From: (.+)|Subject: (.+)|boundary="(.+)"/',
        $message, $matches);
    $date = trim($matches[1][0]);
    $from = trim($matches[2][1]);
    $subject = trim($matches[3][2]);
    $boundary = (isset($matches[4][3])) ? $matches[4][3] : false;

    // discard messages not from subscribed users
    $query = sprintf('SELECT EMAIL_ADDR FROM %sMAILLIST_USER WHERE ' .
        'EMAIL_ADDR = "%s"', DB_TBL_PREFIX,
        mysql_real_escape_string($from, $GLOBALS['DB']));
    $result = mysql_query($query, $GLOBALS['DB']);
    if (!mysql_num_rows($result))
    {
        mysql_free_result($result);

        $pop->dele($id);
        continue;
    }
    mysql_free_result($result);

    // multipart messages
    if ($boundary)
    {
        // split the message into chunks
        $chunks = preg_split('/' . $boundary . '/', $message);
        array_shift($chunks); // drop headers before MIME boundary
        array_shift($chunks); // again to drop headers after MIME boundary
        array_pop($chunks);   // drop trailing --

        // use just the text/plain chunk
        foreach ($chunks as $chunk)
        {
            list($header, $body) = explode("\r\n\r\n", $chunk, 2);
            if (strpos($header, 'Content-Type: text/plain;') !== false)
            {
                break;
            }
        }
    }
    else
    {
        // plain text email
        list($header, $body) = explode("\r\n\r\n", $message, 2);
    }

    // retrieve users to receive individual messages
    $query = sprintf('SELECT EMAIL_ADDR FROM %sMAILLIST_USER WHERE ' .
        'IS_DIGEST = 0', DB_TBL_PREFIX);
    $result = mysql_query($query, $GLOBALS['DB']);

    // forward a copy of the mail
    while ($user = mysql_fetch_assoc($result))
    {
        mail($user['EMAIL_ADDR'], $subject, $body,
            'From: ' . LIST_EMAIL . "\r\n" .
            'Reply-To: ' . LIST_EMAIL . "\r\n");
    }
    mysql_free_result($result);

    // append message to digest
    fwrite($digest, $subject . "\r\n");
    fwrite($digest, $from . "\r\n");
    fwrite($digest, $date . "\r\n\r\n");
    fwrite($digest, $body . "\r\n");
    fwrite($digest, str_repeat('-', 70) . "\r\n");

    // mark message for delete
    $pop->dele($id);
}

$pop->quit();
fclose($digest);
mysql_close($GLOBALS['DB']);
?>