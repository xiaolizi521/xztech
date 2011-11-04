<?php
$title = "Product List";
if( !empty($startdate) or !empty($enddate) ) {
        $title .= " (";
        if( !empty($startdate) ) {
                $title .=  $startdate;
        } else {
                $title .= "Beginning of Time";
        }
        $title .= " - ";
        if( !empty($enddate) ) {
                $title .=  $enddate;
        } else {
                $title .= "Now";
        }
        $title .= ")";
}
require_once("product_list_logic.php");
require_once("header.php");
?>
<?=$warn_datacenter?>
<form>
<?php if( $page_total > 1 ) { ?>
Page: <?=$page_index ?>/<?=$page_total ?>
<br>              
<?=$first_link ?> 
<?=$prev_link ?> 
<?=$next_link ?> 
<?=$last_link ?>

<?php }
if( !empty($jump_link) ) {
        ?>Jump to page: <?=$jump_link ?> <?php } ?>
<?php printReport(); ?>
<?=$first_link ?> 
<?=$prev_link ?> 
<?=$next_link ?> 
<?=$last_link ?> 

<BR CLEAR="all">
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

<p>
<?=$back_summary_link ?>
<br>
<?=$back_build_link ?>

<?php require_once("footer.php"); ?>

