function ViewMember(username) {
var w = 260;
var h = 350;
posleft = (screen.width-w)/2;
postop = (screen.height-h)/2;
window.open("<?php echo $site_url ?>/member.php?id="+username+"", "viewmember", "statusbar=no,menubar=no,toolbar=no,scrollbars=no,resizable=no,width="+w+",height="+h+",left="+posleft+",top="+postop+"");
}

function EditProfile() {
var w = 270;
var h = 490;
posleft = (screen.width-w)/2;
postop = (screen.height-h)/2;
window.open("<?php echo $site_url ?>/profile.php", "editprofile", "statusbar=no,menubar=no,toolbar=no,scrollbars=no,resizable=no,width="+w+",height="+h+",left="+posleft+",top="+postop+"");
}