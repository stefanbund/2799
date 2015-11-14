<?php
// include shared code
include '../lib/common.php';
include '../lib/db.php';
include '../lib/functions.php';
include '../lib/User.php';

// construct password request form HTML
ob_start();
?>
<form action="<?php echo htmlspecialchars($_SEVER['PHP_SELF']); ?>"
 method="post">
<p>Enter your username. A new password will be sent to the email address on
 file.</p>
<table>
<tr>
 <td><label for="username">Username</label></td>
 <td><input type="text" name="username" id="username"
  value="<?php if (isset($_POST['username']))
  echo htmlspecialchars($_POST['username']); ?>"/></td>
</tr><tr>
 <td> </td>
 <td><input type="submit" value="Submit"/></td>
 <td><input type="hidden" name="submitted" value="1"/></td>
</tr><tr>
</table>
</form>
<?php
$form = ob_get_clean();

// show the form if this is the first time the page is viewed
if (!isset($_POST['submitted']))
{
    $GLOBALS['TEMPLATE']['content'] = $form;
}
// otherwise process incoming data
else
{
    // validate username
    if (User::validateUsername($_POST['username']))
    {
        $user = User::getByUsername($_POST['username']);
        if (!$user->userId)
        {
            $GLOBALS['TEMPLATE']['content'] = '<p><strong>Sorry, that ' .
                'account does not exist.</strong></p> <p>Please try a ' .
                'different username.</p>';
            $GLOBALS['TEMPLATE']['content'] .= $form;
        }
        else
        {
            // generate new password
            $password = random_text(8);

            // send the new password to the email address on record
            $message = 'Your new password is: ' . $password;
            mail($user->emailAddr, 'New password', $message);

            $GLOBALS['TEMPLATE']['content'] = '<p><strong>A new ' .
                'password has been emailed to you.</strong></p>';

            // store the new password
            $user->password = $password;
            $user->save();
        }
    }
    // there was invalid data
    else
    {
        $GLOBALS['TEMPLATE']['content'] .= '<p><strong>You did not ' .
            'provide a valid username.</strong></p> <p>Please try ' .
            'again.</p>';
        $GLOBALS['TEMPLATE']['content'] .= $form;
    }
}

// display the page
include '../templates/template-page.php';
?>
