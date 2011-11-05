<?
require_once('finish_logic.php');
?>
<html>
<head>
<title>Add Contact Wizard - Finish</title>
<link href="/css/core_ui.css" rel="stylesheet">
</head>
<body>
<form action="finish_handler.php">
    <div align="center"> 
        <table border="1" cellspacing="0" cellpadding="1" width="80%">
            <tr> 
                <td bgcolor="#003399"><b><font color="#FFFFFF">&nbsp;Add Contact</font></b></td>
            </tr>
            <tr> 
                <td bgcolor="#CCCCCC"> 
                    <table border="0" cellspacing="5" cellpadding="5">
                        <tr> 
                            <td><b>Step <?=$step ?>: Select Account Role</b></td>
                        </tr>
                        <tr> 
                            <td> 
                                <p>Please select a role for this contact.

                                You can also assign 
                                    other roles by selecting one or more roles from 
                                    the list below. Click &quot;Finish&quot; to 
                                    add the contact to the account.</p>
                                </td>
                        </tr>
                        <tr> 
                            <td>
                                <div align="CENTER">
                                    <select MULTIPLE name="role_id[]" size="7" class="data">
                                        <option value=<? print("\"" . ONYX_ACCOUNT_ROLE_ABUSE . "\"");?> selected="selected">Abuse</option>
                                        <option value=<? print("\"" . ONYX_ACCOUNT_ROLE_ADMINISTRATIVE . "\"");?>>Administrative</option>
                                        <option value=<? print("\"" . ONYX_ACCOUNT_ROLE_BILLING . "\"");?>>Billing</option>
                                        <? if (!$has_primary_contact ) { ?>
                                           <option value=<? print("\"" . ONYX_ACCOUNT_ROLE_PRIMARY . "\"");?>>Primary Contact</option>
                                        <? }; ?>
                                        <option value=<? print("\"" . ONYX_ACCOUNT_ROLE_PURCHASER . "\"");?>>Purchaser</option>
                                        <option value=<? print("\"" . ONYX_ACCOUNT_ROLE_REVIEWER . "\"");?>>Reviewer</option>
                                        <option value=<? print("\"" . ONYX_ACCOUNT_ROLE_TECHNICAL . "\"");?>>Technical</option>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr> 
                            <td align="RIGHT"> 
                                <input type="submit" name="finish" style="display: none"><!-- default submit button for enter key -->
                                <input type="submit" name="back" value=" <- Back " class="data">
                                <input type="submit" name="finish" value=" Finish " class="data">
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</form>
</body>
</html>
