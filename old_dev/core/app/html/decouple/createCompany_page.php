<?php
require_once("CORE_app.php");
#require_once("menus.php");
require_once("createCompany_logic.php");
?>
<HTML id="mainbody">

<HEAD>
    <TITLE>CORE: Edit Account</TITLE>
    <LINK HREF="/css/core_ui.css"
          REL="stylesheet">
    <LINK HREF="/css/core2_basic.css" REL="stylesheet">
    <!-- ?= menu_headers() ?-->
</HEAD>
<!-- ?= page_start() ?-->

<!-- Begin Left Content Area ------------------------------------------------->
<TABLE BORDER="1"
       CELLSPACING="0"
       CELLPADDING="0"
       ALIGN="left">
    <TR>
        <TD>

<!--Begin Account Info Form ------------------------------------------------->
<?include("form_wrap_begin.php");?>

        <TABLE BORDER="0"
               CELLSPACING="2"
               CELLPADDING="2"
               ALIGN="left">
            <TR>
                <TD BGCOLOR="#003399"
                    CLASS="hd3rev">Account Info</TD>
            </TR>
            <TR>
                <TD><FORM ACTION="createCompany_handler.php">
                    <?=$hidden_tags ?>
                    <input type="hidden" 
                    name="crm_company_id"
                    value="<?=$onyx_account->crm_company_id ?>"/>


                    <TABLE border="0">
                        <TR>
                            <TD CLASS="label"
                                ALIGN="right">Name: </td><td>
								<INPUT TYPE="text"
                                       NAME="account_name"
                                       VALUE="<?=$onyx_account->account_name?>"
                                       SIZE="32"
                                       CLASS="data"></TD>
						</TR>
                        <TR>
                            <TD CLASS="label"
                                ALIGN="right">SLA Type:</td><td>
                             <SELECT NAME="sla_type_value" CLASS="data">
                             <?=$sla_options?>
                             </SELECT>
                             </TD>
						</TR>
                        <TR>
                            <TD CLASS="label"
                                ALIGN="right">Market Sector:</td><td>
                            <SELECT NAME="marketSector"
                                    CLASS="data">
                                <?=$marketSectorOptions ?>
	                        </SELECT></TD>
                        </TR>
                        <TR>
                            <TD CLASS="label"
                                ALIGN="right">Segment:</td><td>
                                <SELECT NAME="segment" CLASS="data">
                                <?=$segmentOptions?>
                                </SELECT>
                            </TD>
                        </TR>
                        <TR>
                            <TD CLASS="label"
                                ALIGN="right">Support Team:</td><td>
                                <SELECT NAME="support_team" CLASS="data">
                                <?=$companyTeamsOptions?>
                                </SELECT>
                            </TD>
                        </TR>
                        <TR>
                            <TD CLASS="label"
                                ALIGN="right">Company Type:</td><td>
                                <SELECT NAME="companyType"
                                    CLASS="data">
                              <?=$companyTypeOptions ?>
                              </SELECT>
                        </TR>
                        <TR>
                            <TD CLASS="label"
                                ALIGN="right">Company Status:</td><td>
                                <SELECT NAME="companyStatus"
                                    CLASS="data">
                              <?=$companyStatusOptions ?>
                              </SELECT>
                        </TR>
			            <TR>
			                <TD colspan="2" ALIGN="right"><INPUT TYPE="image"
			                           NAME="CONTINUE"
			                           SRC="/images/button_command_save_off.jpg"
			                           HSPACE="2"
			                           VSPACE="2"
									   BORDER="0"></TD>
			            </TR>						                                  
                    </TABLE>
					        </FORM>
                </TD>
            </TR>
        </TABLE>                        
        <BR CLEAR="all"><BR>    
<?include("form_wrap_end.php");?>
<!--End Account Info Form ---------------------------------------------------->
<!-- End Left Content Area ------------------------------------------------  -->
        </TD>
    </TR>
</TABLE>
<!--?= page_stop() ?-->

</HTML>
