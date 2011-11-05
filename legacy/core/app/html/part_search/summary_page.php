<?php
require_once("summary_logic.php");
$title = "Summary";
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
require_once("header.php");
?>
<?=$warn_datacenter ?>
<?php PrintSummary(); ?>

<hr noshade>
<?php PrintTotals(); ?>

<p>
<?=$back_build_link ?>

<?php require_once("footer.php"); ?>
