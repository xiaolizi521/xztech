<?require_once('step2_logic.php') ?>
<HTML>
<HEAD>
<TITLE>Edit Address Wizard</TITLE>
<SCRIPT type="text/javascript" lang="javascript">
    // address issue where blank Street and City
    // fields cause the page to reload with previous
    // data. (CORE-6386).
    // added by Nathen Hinson 10-16-07.
    var goingNext = 0;
    function validate(thisForm){
        // make sure the form has the correct fields
        if(!thisForm.street1 || !thisForm.city){
            // reset the goingNext value
            goingNext = 0;
            return true;
        } 
        if(goingNext == 1){
            // only deal with Street1 and City as those are the fields that are causing the issue.
           if(!thisForm.street1.value){
                var msg = "Please enter a value into the Street Address 1 field.";
                alert(msg);
                thisForm.street1.focus();
                goingNext = 0;
                return false;
            }
            if(!thisForm.city.value){
                 var msg = "Please enter a value into the City field.";
                alert(msg);
                thisForm.street1.focus();
                goingNext = 0;
                return false;
            }
            goingNext = 0;
            return true;
        }
    }

</SCRIPT>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
</HEAD>
<BODY>
<FORM ACTION="step2_handler.php" ONSUBMIT="return validate(this)">
    <DIV ALIGN="center"> 
        <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
            <TR> 
                <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;Edit Address</FONT></B></TD>
            </TR>
            <TR> 
                <TD BGCOLOR="#CCCCCC"> 
                    <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5">
                        <TR> 
                          <TD><B>Step <?=$step ?>: Edit the address</B></TD>
                        </TR>
                        <TR>
                          <TD>Please fill all the fields.
                              <p>
                              Complete the address and press <b>Next</b>
                              </p>
                          </td>
                        </TR>
                        <TR>
                          <TD>
                              <table border='0' align='center'>
                              <tr>
                                <td align='right' valign='top'>
                                  Street Address 1:
                                </td>
                                <td>
                                    <input type="text" name="street1" class="data" value="<?=$street1 ?>" size="50"></input>
                                </td>
                              </tr>
                              <tr>
                                <td align='right' valign='top'>
                                  Street Address 2:
                                </td>
                                <td>
                                    <input type="text" name="street2" class="data" value="<?=$street2 ?>" size="50"></input>
                                </td>
                              </tr>
                              <tr>
                                <td align='right' valign='top'>
                                  Street Address 3:
                                </td>
                                <td>
                                    <input type="text" name="street3" class="data" value="<?=$street3 ?>" size="50"></input>
                                </td>
                              </tr>
                              <tr>
                                 <td align='right' valign='top'>
                                   City:
                                 </td>
                                 <td>
                                    <input type="text" name="city"
                                    value="<?=$city ?>" class="data">
                                 </td>
                              </tr>
                              <tr>
                                 <td align='right' valign='top'>
                                   State:
                                 </td>
                                 <td>
					<select name="state">
						<?= $state_options ?>
					</select>
                                </td>
                              </tr>
                              <tr>
                                <td align='right'>
                                    PostalCode:
                                </td>
                                <td>    
                                    <input type="text" name="zip"
                                    value="<?=$zip ?>" class="data">
                                </td>
                              </tr>
                              <tr>
                                <td align="right">
                                   Country:
                                </td>
                                <td alight="left">
                                    <?= $SESSION_country_name ?>
                                </td>
                              </tr>
                              <tr>
                                <td align="right">
                                   Address Type:
                                </td>
                                <td alight="left">
				<select name="address_type_id">
                                    <?= $address_type_options ?>
				</select>
                                </td>
                              </tr>
                              </table>
                          </TD> 
                        <TR> 
                            <TD ALIGN="RIGHT"> 
                                <INPUT TYPE="submit" NAME="back" VALUE=" <- Back " CLASS="data">
                                <INPUT TYPE="submit" NAME="next" VALUE=" Next -> " CLASS="data" ONCLICK="goingNext = 1">
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
