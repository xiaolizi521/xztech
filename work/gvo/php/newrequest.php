<?php echo '<?xml version="1.0" encoding="utf-8"?>' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
        <title>Global Virtual Opportunities - Office Portal</title>
        
        <link rel="stylesheet" href="includes/css/blueprint/screen.css" type="text/css" media="screen, projection" />
        <link rel="stylesheet" href="includes/css/blueprint/print.css" type="text/css" media="print" />
        <link type="text/css" href="includes/css/jquery.css" rel="Stylesheet" />
        <style type="text/css">
            textarea#textarae {width:auto;height:auto}
        </style>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.0/jquery-ui.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                $("#datepicker").datepicker();
              });
    	</script>
        <!--[if lt IE 8]>
          <link rel="stylesheet" href="includes/css/blueprint/ie.css" type="text/css" media="screen, projection">
        <![endif]-->
    </head>
    <body>
        <div class="container">
            <div class="span-24 last">
                <h1>Global Virtual Opportunities - <?php echo $_SERVER['REMOTE_USER']; ?></h1>
                <h2>Change Request Form</h2>
            </div>
            <div class="span-4">
                <ul>
                    <li><a href="http://gvoutil.ghshosting.com/newrequest.php">Add Request</a></li>
                    <li><a href="http://gvoutil.ghshosting.com/listrequests.php">View Requests</a></li>
                </ul>
            </div>
            <div class="span-20 last">
                <form id="chgrequest" class="form" method="post" action="addrequest.php">
                    <fieldset>
                        <legend>Change Request Information</legend>
                        <table>
                            <tr>
                                    <td><label><span>Change Request Name:</span></label></td>
                                    <td><input type="text" value="" name="name" class=".text" /></td>
                            </tr>
                            <tr>
                                    <td>
                                        <label><span>Date of Change:</span></label>
                                    </td>
                                    <td>
                                        <input type="text" id="datepicker" name="dateofchg"/>
                                    </td>
                            </tr>
                            <tr>
                                    <td>
                                        <label><span>Time of Change (Please enter this in CST, HH:MM):</span></label>
                                    </td>
                                    <td>
                                        <input type="text" id="timeofchg" name="timeofchg" />
                                    </td>
                            </tr>
                            <tr>
                                    <td>
                                        <label><span>Description:</span></label>
                                    </td>
                                    <td>
                                        <textarea rows="4" cols="40" name="description">Please describe the change as detailed as possible.</textarea>
                                    </td>
                            </tr>
                        </table>
                    </fieldset>
                    <fieldset>
                        <legend>Additional Information</legend>
                        <table>
                            <tr>  
                                    <td>
                                        <label><span>Affected Systems:</span></label>
                                    </td>
                                    <td>
                                        <textarea rows="4" cols="40" name="affected">Please list all affected systems separated by commas. These can be hostnames or IP addresses.</textarea>
                                    </td>
                            </tr>
                            <tr>
                                    <td>
                                        <label><span>Affected Customers:</span></label>
                                    </td>
                                    <td>
                                        <textarea rows="4" cols="40" name="affected">Please list any affected customers, if necessary.</textarea>
                                    </td>
                            </tr>
                            <tr>
                                    <td>
                                        <label><span>Classification:</span></label>
                                    </td>
                                    <td>
                                        <select name="classif">
                                            <option value="normal">Normal</option>
                                            <option value="urgent">Urgent</option>
                                            <option value="emerg">Emergency</option>
                                        </select>
                                    </td>
                            </tr>
                            <tr>
                                    <td>
                                        <label><span>Downtime Required (If applicable, HH:MM):</span></label>
                                    </td>
                                    <td>
                                        <input type="text" name="downtime" />
                                    </td>
                            </tr>
                            <tr>
                                    <td>
                                        <label><span>Has this been tested?</span></label>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="tested" value="Tested" />
                                    </td>
                            </tr>
                        </table>
                        <span style="margin-left: 700px;">
                            <input type="submit" name="submit" value="Submit" />
                        </span>
                    </fieldset>
                    <input type='hidden' name='user' value='<?php echo $_SERVER['REMOTE_USER'] ?>' />
                </form>
            </div>
            <div class="span-24 last">
                <pre>AUTHORIZED ACCESS ONLY - RESTRICTED USE - CONFIDENTIAL INFORMATION - ALL RIGHTS RESERVED GLOBAL VIRTUAL OPPORTUNITIES 2010</pre>
            </div>
        </div>
    </body>
</html>
