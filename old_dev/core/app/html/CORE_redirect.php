<HTML>
<HEAD>
<TITLE></TITLE>
<?
    if( empty($args) ) {
        $args = "";
    } else {
        $args = "?" . $args;
    }
?>
<?  if( !empty($target) ) :?>
<SCRIPT LANGUAGE="JavaScript">
    top.document.location='<?=$url?>';
</SCRIPT>
<?else:?>
<SCRIPT LANGUAGE="JavaScript">
    if('<?=$target ?>' == '_blank') {
        window.open('<?=$url?><?=$args?>');
    } else {
        if( top && top.frames['<?=$target?>']) {
            top.frames['<?=$target?>'].location='<?=$url?><?=$args?>';
        } else {
            top.document.location='<?=$url?><?=$args?>';
        }
    }
</SCRIPT>
<?endif;?>
</HEAD>
