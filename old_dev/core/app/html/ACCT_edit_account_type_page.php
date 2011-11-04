<?php
require_once("CORE_app.php");
require_once("ACCT_edit_account_type_logic.php");
?>
<HTML>

<HEAD>
    <TITLE>CORE: Edit Account</TITLE>
    <LINK HREF="/css/core2_basic.css"
          REL="stylesheet">
    <LINK HREF="/css/core_ui.css"
          REL="stylesheet">
<SCRIPT LANGUAGE="javascript1.2" 
        TYPE="text/javascript">
</SCRIPT>
</HEAD>

<BODY MARGINWIDTH="0"
      MARGINHEIGHT="0"
      LEFTMARGIN="0"
      TOPMARGIN="0"
      BGCOLOR="#FFFFFF">

<!--Begin Account Info Form ------------------------------------------------->
<FORM ACTION="ACCT_edit_account_type_handler.php">
<?=$hidden_tags ?>
<TABLE class="blueman" style="width: 100%">
<TR>
 <TH class="blueman" colspan="3"> Edit Account Info </TH>
</TR>
<TR>
   <TD CLASS="label"
       ALIGN="right">Other Type:</td><td>
        <SELECT NAME="sub_type_parm_id"
               CLASS="data" onChange="atChange()" id="account_type">
       <?= $companySubTypesOptions ?>
        </SELECT>

</TD>
</TR>
<TR>
<TD colspan="3" ALIGN="center">
<INPUT TYPE="submit"
VALUE="CANCEL"
ONCLICK="window.close();"
CLASS="form_button">
<INPUT TYPE="submit"
VALUE="CONTINUE"
CLASS="form_button">
</TD>
</TR>						                                  
</TABLE>
</FORM>
<!--End Account Info Form ---------------------------------------------------->
<SCRIPT LANGUAGE="javascript1.2" 
        TYPE="text/javascript">
atChange();
</SCRIPT>
</BODY>
</HTML>
