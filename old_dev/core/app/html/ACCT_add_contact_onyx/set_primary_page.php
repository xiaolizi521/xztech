<?
require_once("set_primary_logic.php");
?>
<html>
<head>
<title>Set Primary Contact</title>
<link href="/css/core_ui.css" rel="stylesheet">
</head>
<body>
<form action="add_contact_handler.php">
    <div align="center"> 
        <table border="1" cellspacing="0" cellpadding="1" width="80%">
            <tr> 
                <td bgcolor="#003399"><b><font color="#FFFFFF">&nbsp;Set Primary Contact</font></b></td>
            </tr>
            <tr> 
                <td bgcolor="#CCCCCC"> 
                    <table border="0" cellspacing="5" cellpadding="5">
                        <tr> 
                            <td> 
                        <p>This will set <b><?= $contact_name ?></b> as primary contact for this account, replacing the current primary contact. 

                                   Are you sure you wish to do this?</p>
                                </td>
                        </tr>
                        <tr> 
                            <td align="RIGHT"> 
                                <input type="submit" name="finish" value=" Ok " class="data">
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
