#! /usr/bin/php
<?php
// include shared code
include '../lib/config.php';
include '../lib/db.php';
include '../lib/POP3Client.php';

// establish a connection to the POP3 server
$pop = new POP3Client();
$pop->connect(POP3_SERVER, POP3_PORT);
$pop->user(MANAGE_USER)
$pop->pass(MANAGE_PASSWORD);

// process each message in inbox
foreach (array_keys($pop->_list()) as $id)
{
    // fetch message
    $message = $pop->retr($id);

    // retrieve the email address and subject headers
    preg_match_all('/From: (.+)|Subject: (.+)/i', $message, $matches);
    $from = trim($matches[1][0]);
    $subject = trim($matches[2][1]);

    // determine if the email address exists
    $query = sprintf('SELECT EMAIL_ADDR FROM %sMAILLIST_USER WHERE ' .
        'EMAIL_ADDR = "%s"', DB_TBL_PREFIX,
        mysql_real_escape_string($from, $GLOBALS['DB']));
    $result = mysql_query($query, $GLOBALS['DB']);
    $exists = (mysql_num_rows($result));
    mysql_free_result($result);

    // send appropriate response
    switch (strtoupper($subject))
    {
        // subscribe email address to list
        case 'SUBSCRIBE':
            if ($exists)
            {
                // address already exists
                $response_file = 'already_subscribed.txt';
            }
            else
            {
                $query = sprintf('INSERT INTO %sMAILLIST_USER ' .
                    '(EMAIL_ADDR) VALUES ("%s")', DB_TBL_PREFIX,
                    mysql_real_escape_string($from, $GLOBALS['DB']));

                $response_file = (mysql_query($query, $GLOBALS['DB'])) ?
                    'subscribe.txt' : 'error.txt';
            }
            break;

        // remove email address from list
        case 'UNSUBSCRIBE':
            if ($exists)
            {
                $query = sprintf('DELETE FROM %sMAILLIST_USER WHERE ' .
                    'EMAIL_ADDR = "%s"', DB_TBL_PREFIX,
                    mysql_real_escape_string($from, $GLOBALS['DB']));

                $response_file = (mysql_query($query, $GLOBALS['DB'])) ?
                    'unsubscribe.txt' : 'error.txt';
            }
            else
            {
                // address does not exist
                $response_file = 'not_subscribed.txt';
            }
            break;

        // set preference for digest
        case 'SET +DIGEST':
            if ($exists)
            {
                $query = sprintf('UPDATE %sMAILLIST_USER SET ' .
                    'IS_DIGEST = 1 WHERE EMAIL_ADDR = "%s"', DB_TBL_PREFIX,
                    mysql_real_escape_string($from, $GLOBALS['DB']));

                $response_file = (mysql_query($query, $GLOBALS['DB'])) ?
                    'digest_on.txt' : 'error.txt';
            }
            else
            {
                // address does not exist
                $response_file = 'not_subscribed.txt';
            }
            break;

        // set preference for individual messages
        case 'SET -DIGEST':
            if ($exists)
            {
                $query = sprintf('UPDATE %sMAILLIST_USER SET ' .
                    'IS_DIGEST = 0 WHERE EMAIL_ADDR = "%s"', DB_TBL_PREFIX,
                    mysql_real_escape_string($from, $GLOBALS['DB']));

                $response_file = (mysql_query($query, $GLOBALS['DB'])) ?
                    'digest_off.txt' : 'error.txt';
            }
            else
            {
                // address does not exist
                $response_file = 'not_subscribed.txt';
            }
            break;

        // use help message
        case 'HELP':
            $response_file = 'help.txt';

        // unknown command was received
        default:
            $response_file = 'unknown.txt';
    }
    // Read the data
    $response = file_get_contents(REPLY_DIR . '/' . $response_file);

    // these placeholders will be swapped in the message templates
    $replace = array('<list_email>' => LIST_EMAIL,
                     '<manage_email>' => MANAGE_EMAIL);
    $response = str_replace(array_keys($replace), array_values($replace),
        $response);

    // send message
    mail($from, 'RE: ' . $subject, $response,
        'From: ' . LIST_EMAIL . "\r\n" .
        'Reply-To: ' . LIST_EMAIL . "\r\n");

    // mark message for delete
    $pop->dele($id);
}

$pop->quit();
mysql_close($GLOBALS['DB']);
?>