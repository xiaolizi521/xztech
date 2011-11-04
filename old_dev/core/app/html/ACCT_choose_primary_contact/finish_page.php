<?
require_once('finish_logic.php');
?>
<html>
<head>
<title>Choose Primary Contact Wizard - Finish</title>
<link href="/css/core_ui.css" rel="stylesheet">
</head>
<body>
<form action="finish_handler.php">
    <div align="center"> 
        <table border="1" cellspacing="0" cellpadding="1" width="80%">
            <tr> 
                <td bgcolor="#003399"><b><font color="#FFFFFF">&nbsp;Choose Primary Contact</font></b></td>
            </tr>
            <tr> 
                <td bgcolor="#CCCCCC"> 
                    <table border="0" cellspacing="5" cellpadding="5">
                        <tr> 
                            <td><b>Step <?=$step ?>: Confirm</b></td>
                        </tr>
                        <tr> 
                            <td> 
                                <p>Please confirm that you wish to change the primary contact for account <b>
                                <? 
                                  print($accountName . " (#" . $accountNumber . ")");
                                ?>
                                </b> to <b>
                                <?
                                  print($contactName);
                                ?>
                                </b>. 
                                </p>
                            </td>
                        </tr>
                        <tr> 
                            <td align="RIGHT">
                                <input type="hidden" name="role_id" value="<? print(ONYX_ACCOUNT_ROLE_PRIMARY); ?>"/> 
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
