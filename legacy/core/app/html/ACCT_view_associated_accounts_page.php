<?PHP
// THIS IS ALL YOU NEED!
require_once("ACCT_view_associated_accounts_logic.php");
require_once("menus.php");
?>

<HTML>
<HEAD>
<TITLE>
CORE: View Accounts Associated with <?=$name ?>
</TITLE>
<LINK href="/css/core_ui.css" rel="stylesheet">
<LINK HREF="/css/core2_basic.css" REL="stylesheet">
<STYLE TYPE="text/css">
<!--

TABLE.reporter {border: double black}
TH.reporter {background: #CCCCCC;
color: #000000}

.reporter {border-spacing: 0}

TABLE.reporter TD {border-top: solid #CCCCCC;
border-right: solid #CCCCCC}
TR.reporter    {background: #F0F0F0}
TR.reporterodd {background: #FFFFFF}

SPAN.Active{ color: blue }
SPAN.New{ color: green }
SPAN.Closed{ color: gray }
SPAN.Delinquent{ color: red }

-->
</STYLE>
<?= menu_headers() ?>
<SCRIPT LANGUAGE="JavaScript" SRC="/script/popup.js" TYPE="text/javascript"></SCRIPT>
</HEAD>
<?= page_start() ?>
<br>

<?require('ACCT_view_all_accounts_include.php'); ?>

<?= page_stop() ?>
</HTML>
