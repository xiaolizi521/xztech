<?require_once('step2_logic.php') ?>
<HTML>
<HEAD>
<TITLE>Add Contact Wizard - 2</TITLE>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
</HEAD>
<BODY>
<FORM ACTION="step2_handler.php">
    <DIV ALIGN="center"> 
        <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
            <TR> 
                <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;Add Contact</FONT></B></TD>
            </TR>
            <TR> 
                <TD BGCOLOR="#CCCCCC"> 
                    <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5">
                        <TR> 
                            <TD><B>Step <?=$step ?>: Check Search Results</B></TD>
                        </TR>
                        <TR> 
                            <TD> 
                                <P>The database was searched to find the person 
                                    you want to add as a contact. At this point 
                                    you can:</P>
                                <UL>
                                    <LI>Select one of names from the list below 
                                        and click &quot;Next&quot;</LI>
                                    <LI><I>OR</I> click &quot;New Person&quot; if 
                                        the name wasn't found</LI>
                                    <LI><I>OR</I> go &quot;Back&quot; to repeat 
                                        the search with a different Last Name</LI>
                                </UL>
                            </TD>
                        </TR>
                        <TR> 
                            <TD>
                                <DIV style="background: white; width: 600px; height: 200px; overflow: auto">
                                <table>
                        <? 
                        $prev_name = '';
                        for ($i = 0; $i < sizeof($person_list); $i++) {
                            $person =& $person_list[$i];
                            ?>
                            <? if ($person['customerName'] != $prev_name) { ?>
                                <tr><td colspan="2"><b>
                                    <?= $person['customerName'] ?>
                                </b></td></tr>
                                <? $prev_name = $person['customerName'] ?>
                            <? } ?>
                <? $radio_id = "radio-" . $person['onyxId']; ?>
                <tr onclick="getElementById('<?= $radio_id ?>').checked = true">
                            <td>&nbsp;&nbsp;&nbsp;</td>
                            <td style="border-bottom: 1px solid black">
                            <input type="radio" name="contact_id" 
                                   id="<?= $radio_id ?>"
                                   value="<?= $person['onyxId'] ?>">
                            <?= $person["city"] ?>, <?= $person["state"] ?>,
                            <?= $person['country'] ?> 
                            </td>
                            <td 
                                style="border-bottom: 1px solid black">
                              <?= $person['email'] ?>
                            </td>
                </tr>
                        <? } ?>
                          </table>
                                </DIV>
                            </TD>
                        </TR>
                        <TR> 
                            <TD ALIGN="RIGHT"> 
                                <INPUT TYPE="submit" NAME="back" VALUE=" <- Back " CLASS="data">
                                <INPUT TYPE="submit" NAME="new" VALUE=" New Person " CLASS="data">
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
