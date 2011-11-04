<?
    require_once("CORE_app.php");
    require_once("switch_port_logic.php3");
    // Enable Progress Bar
    include("TimeRegister.php");
    $page_timer = new PageTimeRegister();
    $page_timer->start(); 
    $loadtime = number_format((0+$page_timer->average_duration),0);
    set_title('Switch/Port Assignments ------ (Avg Load: '.$loadtime.' secs)','#003399');
?>
<html>
  <head>
    <script>
      var avgLoadTime = <?print $loadtime;?>;
    </script>
    <TITLE>
      CORE: Switch/Port Assignments ---- (Avg Load: <?=$loadtime?> secs)</TITLE> 
      <LINK HREF="/css/core2_basic.css" REL="stylesheet">
  <? require_once("tools_body.php"); ?>
    <?=$buttons?>
    <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2" CLASS="blueman">
      <TH class="blueman">
        Switch/Port Assignments
        <?
            if(isset($datacenter_abbr)) {
                print " for " . $datacenter_abbr;
            }
        ?>
      </TH>
      <TR>
        <TD>
          <TABLE class=datatable width="100%">
            <?=$switch_form ?>
          </TABLE>
          <TABLE class=datatable>
            <?=$switch_table?>
          </TABLE>
        </TD>
      </TR>
    </TABLE>
<?= page_stop() ?>
</HTML>
<? $page_timer->stop();?>
