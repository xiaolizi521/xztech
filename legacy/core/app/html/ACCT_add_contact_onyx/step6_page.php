<?require_once('step6_logic.php') ?>
<HTML>
<HEAD>
<TITLE>Add Contact Wizard - 6</TITLE>
<LINK HREF="/css/core_ui.css" REL="stylesheet">
<script src="/script/loaddiv.js"></script>
<script language="javascript">
var req;

function loadXMLDoc() {
    var country_select = document.getElementById("country_select");
    code = country_select.options[country_select.selectedIndex].value;
    req = false;
    if ( code ) {
        url = "<?=$div_url?>";
        url += "?country_code=" + code
        loaddiv( url, "state_div", "Loading Regions/States...","loading");
    }
}

function processReqChange() {
    // only if req shows "loaded"
    if (req.readyState == 4) {
        // only if "OK"
        if (req.status == 200) {
            // ...processing statements go here...
            clearStateSelect();
            buildStateSelect();
        } else {
            alert("There was a problem retrieving the XML data:\n" +
                req.statusText);
        }
    }
}

function clearStateSelect() {
    var select = document.getElementById("state_div");
    while (select.length > 0) {
        select.remove(0);
    }
}

function buildStateSelect() {
    state_div = document.getElementById("state_div");
    state_div.innerHTML = req.responseText;
}
</script>
</HEAD>
<BODY>
<FORM ACTION="step6_handler.php">
    <DIV ALIGN="center"> 
        <TABLE BORDER="1" CELLSPACING="0" CELLPADDING="1" WIDTH="80%">
            <TR> 
                <TD BGCOLOR="#003399"><B><FONT COLOR="#FFFFFF">&nbsp;Add Contact </FONT></B>
                </TD> 
            </TR>
            <TR> 
                <TD BGCOLOR="#CCCCCC"> 
                    <TABLE BORDER="0" CELLSPACING="5" CELLPADDING="5">
                       <TR>  <TD id="loading"> </td></tr>
                        <TR> 
                            <TD><B>Step <?=$step ?>: Enter the Job Title and Address</B></TD>
                        </TR>
                        <TR> 
                            <TD>Please enter the contact's job title below. This 
                                doesn't have to be the person's &quot;official&quot; 
                                job title within the organization; it could simply 
                                be a short description of this person's relationship 
                                with the organization. Then, enter the mailing address 
                                for the contact. Notice that if the account has a
                                primary contact, then that contact's address is pre-filled.</TD>
                        </TR>
                        <TR> 
                            <TD>
                                <DIV ALIGN="CENTER"> 
                                    <TABLE>
                                        <TR> 
                                            <TD ALIGN="RIGHT">Job Title:</TD>
                                            <TD>
                                                <INPUT TYPE="text" NAME="job_title" VALUE="<?=$job_title ?>" SIZE="32" CLASS="data">
                                            </TD>
                                        </TR>
                                        <TR> 
                                            <TD ALIGN="RIGHT" VALIGN="TOP">
                                          <? showIsRequired("Street Address: Lines 1-3",$street1); ?> </td>
                                            <TD>
                                                <input type="text" NAME="street1" size="60" CLASS="data" value="<?=$street1 ?>"></input>
                                                <input type="text" NAME="street2" size="60" CLASS="data" value="<?=$street2 ?>"></input>
                                                <input type="text" NAME="street3" size="60" CLASS="data" value="<?=$street3 ?>"></input>
                                            </TD>
                                        </TR>
                                        <TR> 
                                            <TD ALIGN="RIGHT"><? showIsRequired("City:",$city); ?></TD>
                                            <TD NOWRAP> 
                                                <INPUT TYPE="text" NAME="city" VALUE="<?=$city ?>" MAXLENGTH="32" SIZE="32" CLASS="data">
                                                Region/State:
                                                    <SELECT NAME="state" CLASS="data" id="state_div">
                                        <OPTION VALUE=""> --select-- </OPTION>
                                                    <? foreach ($regions as $region ) {
                                                        if ( $region->parameter_id == $state ) {
                                                            $selected = 'selected';
                                                        }
                                                        else {
                                                            $selected = '';
                                                        } ?>
                                                        <option <?=$selected ?> value="<?=$region->parameter_id?>"><?=$region->desc?></option>
                                                    <? } ?>
                                                    </SELECT>
                                                Zip:
                                                <INPUT TYPE="text" NAME="zip" VALUE="<?=$zip ?>" SIZE="10" MAXLENGTH="10" CLASS="data">
                                            </TD>
                                        </TR>
                                    <TR>
                                    <TD ALIGN="RIGHT">
                                    Country:
                                    </TD>
                                    <TD>
                                        <SELECT NAME="country_id" CLASS="data" onchange="loadXMLDoc();" id="country_select">
                                        <option value="US"> United States</option>
                                        <option value="GB"> United Kingdom </option>
                                        <OPTION VALUE=""> --select-- </OPTION>
                                        <? foreach ($countries as $country) { 
                                            if ( $country['countryCode'] == $country_id ) {
                                                $selected = 'selected';
                                            }
                                            else {
                                                $selected = '';
                                            } ?>
                                            <option <?=$selected ?> value="<?=$country['countryCode']?>"><?=$country['name']?></option>
                                        <? } ?>
                                        </SELECT>
                                    </TD>
                                    </TR>
                                    </TABLE>
                                    
                                </DIV>
                            </TD>
                        </TR>
                        <TR> 
                            <TD ALIGN="RIGHT"> 
                                <INPUT TYPE="submit" NAME="next" VALUE=" Next -> " CLASS="data" style="display: none"><!-- default submit button for enter key -->
                                <INPUT TYPE="submit" NAME="back" VALUE=" <- Back " CLASS="data">
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
