<?
# Redirect to new account/server page if set
if ($_COOKIE['use-new-account-page'] && !$_GET['view_old'] && !$_POST['view_old']) {
    $app_location = "/py/core";
    
    if (!empty($computer_number)) {
        header("Location: $app_location/#/device/$computer_number");
    }
    
    if (!empty($account_number)) {
        header("Location: $app_location/#/account/$account_number");
    }
    
    if (!empty($contract_number)) {
        header("Location: $app_locationt/#/contract/$contract_number");
    }
    
    if (!empty($credit_memo_number)) {
        header("Location: $app_location/#/creditmemo/$credit_memo_number");
    }
}
?>
<HTML>
<HEAD>
    <TITLE>
    <?
        if (!empty($account_number)) {
            print $account_number;
        } else {
            print "CORE";
        }
    ?>
    </TITLE>
</HEAD>
<?
if (!empty($args)) {
        $args = "?" . $args;
} else {
        $args = "?";
}

if (empty($content_page)) {
    if(!empty($cert_id)) {
        $content_page="/py/sslcert/sslcert.pt";
        $args .= "cert_id=$cert_id";
    } elseif (!empty($computer_number)) {
        $content_page="/tools/DAT_display_computer.php3";
        $args .= "computer_number=$computer_number";
        //If there is a computer number, then the account_number will
        //be calculated to ensure that the account number is consistent
        //with the computer number
        if(!empty($account_number)) {
            unset($account_number);
        }
    } elseif (!empty($account_number)) {
        $content_page="/py/account/view.pt";
        $args .= "account_number=$account_number";
    }
}
//now add on any post/get data
require_once('standard_lib.php3');
//BuildGetPost($args);
?>
<FRAMESET COLS="25%,75%" FRAMEBORDER="1" FRAMESPACING="2" name="content_wrapper">
    <FRAME id="left" NAME="left" SCROLLING="auto" MARGINWIDTH="0" MARGINHEIGHT="0" FRAMEBORDER="1"
        <?
        if (!empty($cert_id) && !empty($account_number)) {
            print "src='/py/account/tree.pt?account_number=$account_number'";
        } else {
            print 'src="/null.html"';
        }
        ?>
    ></FRAME>
    <FRAME id="content_page" src="<?=$content_page?><?=$args?>" name="content" FRAMEBORDER="1" SCROLLING="auto" MARGINWIDTH="0" MARGINHEIGHT="0"></FRAME>
</FRAMESET>
<NOFRAMES>
    Frames Not Supported.
</NOFRAMES>
</HTML>
