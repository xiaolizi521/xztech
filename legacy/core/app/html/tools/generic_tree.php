<?php
require_once("CORE_app.php");
new CORE;
$db =& $GLOBAL_db;

require_once('tree_class.php');
?>
<HTML>

<HEAD>
<TITLE>Side Menu Tree</TITLE>
<LINK href="/css/core_ui.css" rel="stylesheet">
</SCRIPT>
</HEAD>
    
<BODY bgcolor="#FFFFFF" 
      MARGINWIDTH="4" >
<?
print "<!-- REQUEST_URI length:" . strlen($REQUEST_URI) . " -->\n";

# Collapsing and expanding tree branches could work but it ends up
# creating a huge page because of all the variables that have to be included
# in each +/- symbol's link.
if (empty($title_url)) {
    print "
<SCRIPT LANGUAGE=\"JavaScript\">
history.back();
</SCRIPT>
    ";
}

$tree = new TreeNode("<A HREF=\"$title_url\" TARGET=content>"
    . "<B>$title</B></A>"); 

for ($i = 0; $i < sizeof($item_names); $i++) {
    $node = new TreeNode(
        $item_names[$i],
        '',
        $item_base . $item_urls[$i]);
    if(isset($item_branch1[$i]) && $item_branch1[$i] != '') {
        $node->addChild(new TreeNode(
            $item_branch1[$i],
            '',
            $item_branch1_base . $item_branch1_urls[$i]));
    }
    if(isset($item_branch2[$i]) && $item_branch2[$i] != '') {
        $node->addChild(new TreeNode(
            $item_branch2[$i],
            '',
            $item_branch2_base . $item_branch2_urls[$i]));
    }
    $tree->addChild($node);
}

$TARGET_FRAME='content';
$EXTRA_VARIABLES = "";

if (isset($tree_state)) {
    $tree->update();
}
else {
    $tree->expand();
}
$tree->display();
?>
</BODY>
</HTML>
