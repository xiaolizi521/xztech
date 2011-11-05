<?

require_once("CORE_app.php");

$GLOBAL_db->BeginTransaction();

?>

<HTML><HEAD>

<LINK REL="stylesheet" TYPE="text/css" HREF="/css/core2_basic.css">

</HEAD>
<BODY onLoad="document.getElementById('mysql_net_form').submit();">

<TABLE class="blueman">
<TR><TH class="blueman">Submitting MySQL Network subscription for #<?=$computer_number ?>...</TH></TR>
<TR><TD class="blueman">
<P>

<?
if (isset($computer_number) and isset($account_number)) {
    $computer = new RackComputer;
    $computer->Init($account_number,$computer_number,&$GLOBAL_db);

    $key = md5('RKSP' . $email . '3^j3bK673s9!p');
    if($GLOBALS["rack_test_system"] ){
        $url = 'https://shop.mysql.com/partner/ordertest.php';
        $proto = 'http';
    } else {
        $url = 'https://shop.mysql.com/partner/order.php';
        $proto = 'https';
    }
    
    $contact = $computer->account->getPrimaryContact();
?>
    <form id="mysql_net_form" action="<? print $url ; ?>">
    <input type="hidden" name="partnercode" value="RKSP" />
    <input type="hidden" name="key" value="<? print $key; ?>" />
    <input type="hidden" name="item[1][product]" value="NETWORKBASIC" />
    <input type="hidden" name="item[1][quantity]" value="1" />
    <input type="hidden" name="item[1][pcode]" value="RKSP-NB-05-06-A" />
    <input type="hidden" name="contact[1][item]" value="1" />
    <input type="hidden" name="contact[1][firstname]" value="<? print $contact->individual->firstName; ?>" />
    <input type="hidden" name="contact[1][lastname]" value="<? print $contact->individual->lastName; ?>" />
    <input type="hidden" name="contact[1][email]" value="<? print $contact->individual->getPrimaryEmailAddress(); ?>" />
    <input type="hidden" name="contact[1][phone]" value="<? print $contact->individual->getPrimaryPhoneNumber(); ?>" />
    
    <input type="hidden" name="company" value="<? print $contact->primaryCompanyName; ?>" />
    <input type="hidden" name="firstname" value="<? print $contact->individual->firstName; ?>" />
    <input type="hidden" name="lastname" value="<? print $contact->individual->lastName; ?>" />
    <input type="hidden" name="title" value="<? print $contact->individual->titleDescription; ?>" />
    <input type="hidden" name="email" value="<? print $contact->individual->getPrimaryEmailAddress(); ?>" />
    <input type="hidden" name="phone" value="<? print $contact->individual->getPrimaryPhoneNumber(); ?>" />
    <input type="hidden" name="fax" value="<? print $contact->individual->getFaxPhoneNumber(); ?>" />
    <input type="hidden" name="address" value="<? print $contact->street; ?>" />
    <input type="hidden" name="city" value="<? print $contact->city; ?>" />
    <input type="hidden" name="state" value="<? print $contact->state; ?>" />
    <input type="hidden" name="zipcode" value="<? print $contact->postal_code; ?>" />
    <input type="hidden" name="country" value="<? print $contact->country_abbrev; ?>" />

<input type="hidden" name="ref_url" value="<? print $proto ; ?>://<? print $_SERVER['SERVER_NAME']; ?>/mysql_net_order.php?computer_number=<? 
print $computer->computer_number ; ?>&account_number=<? print $computer->account->account_number; ?>" />

    <span class="text_button">
    <a  href="javascript: document.getElementById('mysql_net_form').submit();" class="text_button">Order</a>
    </span>

    </form>




<?
} else {
    print "Error! Computer/Account # not passed in!";
}
?>

</P>
</TD></TR>
</TABLE>

</BODY>
</HTML>
