<img src='img2/logo.png'><br>
<?

if (!$_SESSION['name']) {
echo "
<div class='navbar'><a href='index.php'>News</a> | <a href='lostpassword.php'>Lost your password?</a> | <a href='login.php'>Login</a> | <a href='register.php'>Register</a> | <a href='http://pulse.offbeat-zero.net/forum'>Support Forum</a></div>";
}
else {
echo "<div class='navbar'><a href='index.php'>News</a> | <a href='custom.php'>Customise</a> | <a href='fonts.php'>Upload a Font</a> | <a href='viewfonts.php'>View all Fonts</a> | <a href='viewbg.php'>View Your Backgrounds</a> | <a href='viewbg2.php'>View All Backgrounds</a>
| <a href='backgrounds.php'>Upload a Background</a> |  <a href='colour.php'>Colors</a> | <a href='http://pulse.offbeat-zero.net/forum'>Support Forum</a> | <a href='logout.php'>Log Out</a></div>
";
}


?>