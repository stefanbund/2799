<?php
// include shared code
include '../lib/common.php';
include '../lib/db.php';
include '../lib/functions.php';
include '../lib/User.php';
include '../lib/JpegThumbnail.php';
include '401.php';

$user = User::getById($_SESSION['userId']);
?>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"
 method="post" enctype="multipart/form-data">
 <div>
  <input type="file" name="avatar"/> 
  <input type="submit" value="upload"/>
 </div>
</form>
<?php
if (!$_FILES['avatar']['error'])
{
    // create a thumbnail copy of the image
    $img = new JpegThumbnail();
    $img->generate($_FILES['avatar']['tmp_name'],
        'avatars/' . $user->username . '.jpg');
}
?>
