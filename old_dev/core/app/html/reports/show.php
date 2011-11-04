<?php
require_once("CORE_app.php");
require_once("menus.php");
require_once("./show_logic.php");
?>
<?    // Enable Progress Bar
    include("TimeRegister.php");
    $page_timer = new PageTimeRegister();
    $page_timer->start();
?>
<?set_title($title,'#003399');?>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML//EN">
<html id="mainbody">
<HEAD>
    <TITLE>
        CORE: <?=$title ?>
    </TITLE>
    <LINK HREF="/css/core_ui.css" REL="stylesheet">
    <LINK HREF="/css/core2_basic.css" REL="stylesheet">
    <SCRIPT LANGUAGE="JavaScript"
            SRC="/script/date-picker.js"></SCRIPT>
    <?= menu_headers() ?>
</HEAD>
<?= page_start() ?>
<!-- Begin Show Report -->

<br>
<table border="0"
       cellspacing="0"
       cellpadding="2"
       class="titlebaroutline">
<tr>
   <td>
	<table width="100%"
	       border="0"
	       cellspacing="0"
	       cellpadding="0"
          bgcolor="#FFFFFF">
    <tr>       
        <td> 
         		<table border="0"
         		       cellspacing="2"
         		       cellpadding="2">
         		<tr>
         			<td bgcolor="#003399" 
                        class="hd3rev"
                        colspan=2> <?=$title ?> </td>
         		</tr>
               <tr>
                  <td>
                    <?php if( $page_total > 1 ) { ?>
	    		    Page: <?=$page_index ?>/<?=$page_total ?> &nbsp; &nbsp; &nbsp;
	    	    	<?=$first_link ?> 
		    	    <?=$prev_link ?> 
    		    	<?=$next_link ?> 
	    		    <?=$last_link ?>
                    </td>
                    <td>
        		    <?php }
                    if( !empty($jump_link) ) {
                    ?><form>Jump to page: <?=$jump_link ?> <?php } ?> </form>
			        </TD>
                </TR>
                <TR>
                    <TD COLSPAN="2">
			        <?php printReport(); ?>
                    </TD>
                </TR>
               <tr>
                  <td>
                    <?php if( $page_total > 1 ) { ?>
	    		    Page: <?=$page_index ?>/<?=$page_total ?> &nbsp; &nbsp; &nbsp;
	    	    	<?=$first_link ?> 
		    	    <?=$prev_link ?> 
    		    	<?=$next_link ?> 
	    		    <?=$last_link ?>
                    </td>
                    <td>
        		    <?php }
                    if( !empty($jump_link) ) {
                    ?><form>Jump to page: <?=$jump_link ?> <?php } ?> </form>
			        </TD>
                </TR>
         	</table>
        </td>
    </tr>
    </table></td>
</tr>
</table>            
<!-- End Show Report ------------------------------------------------------- -->
<BR CLEAR="all">
<?php
	if( empty($NO_DOWNLOAD) ) {
?>
<p style="padding-left: 1ex">
<a href="<?=$xls_link ?>"><IMG SRC="/images/button_arrow_off.jpg" 
                               WIDTH="20" 
							   HEIGHT="20" 
							   BORDER="0" 
							   ALT="View"></A> Excel
<br>        
<a href="<?=$csv_link ?>"><IMG SRC="/images/button_arrow_off.jpg" 
                               WIDTH="20" 
							   HEIGHT="20" 
							   BORDER="0" 
							   ALT="View"></A> CSV
<br>
<a href="<?=$gnumeric_link ?>"><IMG SRC="/images/button_arrow_off.jpg" 
                               WIDTH="20" 
							   HEIGHT="20" 
							   BORDER="0" 
							   ALT="View"></A> Gnumeric
</p>
<?
    }
?>

<?= page_stop() ?>
</html>

<? $page_timer->stop();?>
