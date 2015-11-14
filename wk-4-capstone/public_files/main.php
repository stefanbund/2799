<?php
// include shared code
include '../lib/common.php';
include '../lib/db.php';
include '../lib/functions.php';
include '../lib/User.php';

// 401 file referenced since user should be logged in to view this page
//include '401.php';

// generate user information form
$user = User::getById(1);
ob_start();
?>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"
 method="post">
 <table>
  <tr>
   <td><label>Username</label></td>
   <td><input type="text" name="username"  disabled="disabled"
    readonly="readonly"value="<?php echo $user->username; ?>"/></td>
  </tr><tr>
   <td><label for="email">Email Address</label></td>
   <td><input type="text" name="email" id="email"
    value="<?php echo (isset($_POST['email']))? htmlspecialchars(
$_POST['email']) : $user->emailAddr; ?>"/></td>
  </tr><tr>
   <td><label for="password">New Password</label></td>
   <td><input type="password" name="password1" id="password1"/></td>
  </tr><tr>
   <td><label for="password2">Password Again</label></td>
   <td><input type="password" name="password2" id="password2"/></td>
  </tr><tr>
  <td> </td>
   <td><input type="submit" value="Save"/></td>
   <td><input type="hidden" name="submitted" value="1"/></td>
  </tr><tr>
 </table>
</form>
<?php
$form = ob_get_clean();
$GLOBALS['TEMPLATE']['content'] = $form;

// show the form if this is the first time the page is viewed
if (!isset($_POST['submitted']))
{
    $GLOBALS['TEMPLATE']['content'] = $form;
}
// otherwise process incoming data
else
{
    // validate password
    $password1 = (isset($_POST['password1']) && $_POST['password1']) ?
        sha1($_POST['password1']) : $user->password;
    $password2 = (isset($_POST['password2']) && $_POST['password2']) ?
        sha1($_POST['password2']) : $user->password;
    $password = ($password1 == $password2) ? $password1 : '';

    // update the record if the input validates
    if (User::validateEmailAddr($_POST['email']) && $password)
    {
        $user->emailAddr = $_POST['email'];
        $user->password = $password;
        $user->save();

        $GLOBALS['TEMPLATE']['content'] = '<p><strong>Information ' .
            'in your record has been updated.</strong></p>';
    }
    // there was invalid data
    else
    {
        $GLOBALS['TEMPLATE']['content'] .= '<p><strong>You provided some ' .
            'invalid data.</strong></p>';
        $GLOBALS['TEMPLATE']['content'] .= $form;
    }
}
// display the page
include '../templates/template-page.php';
?>
