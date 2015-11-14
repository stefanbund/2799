<?php
// include shared code
include '../lib/common.php';
include '../lib/db.php';
include '../lib/functions.php';
include '../lib/User.php';

// 401 file included because user should be logged in to access this page
include '401.php';

// user must have appropriate permissions to use this page
$user = User::getById($_SESSION['userId']);
if (~$user->permission & User::CREATE_FORUM)
{
    die('<p>Sorry, you do not have sufficient privileges to create new ' .
        'forums.</p>');
}

// validate incoming values
$forum_name = (isset($_POST['forum_name'])) ? trim($_POST['forum_name']) : '';
$forum_desc = (isset($_POST['forum_desc'])) ? trim($_POST['forum_desc']) : '';

// add entry to the database if the form was submitted and the necessary
// values were supplied in the form
if (isset($_POST['submitted']) && $forum_name && $forum_desc)
{
    $query = sprintf('INSERT INTO %sFORUM (FORUM_NAME, DESCRIPTION) ' .
        'VALUES ("%s", "%s")', DB_TBL_PREFIX,
        mysql_real_escape_string($forum_name, $GLOBALS['DB']),
        mysql_real_escape_string($forum_desc, $GLOBALS['DB']));
    mysql_query($query, $GLOBALS['DB']);

    // redirect user to list of forums after new record has been stored
    header('Location: view.php');
}

// form was submitted but not all the information was correctly filled in
else if (isset($_POST['submitted']))
{
    $message = '<p>Not all information was provided.  Please correct ' .
        'and resubmit.</p>';
}

// generate the form
ob_start();
if (isset($message))
{
    echo $message;
}
?>
<form action="<?php htmlspecialchars($_SERVER['PHP_SELF']); ?>"
 method="post">
 <div>
  <label for="forum_name">Forum Name:</label>
  <input type="input" id="forum_name" name="forum_name" value="<?php
   echo htmlspecialchars($forum_name); ?>"/><br/>
  <label for="forum_desc">Description:</label>
  <input type="input" id="forum_desc" name="forum_desc" value="<?php
   echo htmlspecialchars($forum_desc); ?>"/>
  <br/>
  <input type="hidden" name="submitted" value="true"/>
  <input type="submit" value="Create"/>
 </div>
</form>
<?php
$GLOBALS['TEMPLATE']['content'] = ob_get_clean();

// display the page
include '../templates/template-page.php';
?>

