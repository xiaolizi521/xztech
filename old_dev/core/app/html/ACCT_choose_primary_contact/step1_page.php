<?require_once('step1_logic.php') ?>
<HTML>
<HEAD>
<TITLE>Choose Primary Contact Wizard - 1</TITLE>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
</HEAD>
<BODY>
<FORM ACTION="step2_page.php">
    <DIV ALIGN="center"> 
        <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
            <TR> 
                <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;Choose Primary Contact</FONT></B></TD>
            </TR>
            <TR> 
                <TD BGCOLOR="#CCCCCC"> 
                    <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5">
                        <TR> 
                            <TD><B>Step <?=$step ?>: Search for an Existing Contact</B></TD>
                        </TR>
                        <TR> 
                            <TD>In order to maintain database integrity, you should 
                                always check to see if the contact you are trying 
                                to add already exists in the system. Search on the 
                                <B>Last Name</B> of the person to see if it exists 
                                in the database.</TD>
                        </TR>
                        <TR> 
                            <TD>
                                <table ALIGN="CENTER">
                                    <tr><td>Search First Name:</td>
                                        <td><INPUT TYPE="text" NAME="first_name" VALUE="<?=$first_name ?>" MAXLENGTH="32" SIZE="32" CLASS="data"></td>
                                    </tr>
                                    <tr><td><? showIsRequired("Search Last Name:", $last_name); ?> </td><td>
                                    <INPUT TYPE="text" NAME="last_name" VALUE="<?=$last_name ?>" MAXLENGTH="32" SIZE="32" CLASS="data"></td>
                                    </tr>
                                </table>
                            </TD>
                        </TR>
                        <TR> 
                            <TD ALIGN="RIGHT"> 
                                <INPUT TYPE="submit" NAME="next" VALUE=" Next -> " CLASS="data">
                            </TD>
                        </TR>
                    </TABLE>
                </TD>
            </TR>
        </TABLE>
    </DIV>
</FORM>
</BODY>
</HTML>
