<?
	session_start();
		
	include( "header_functions.php" );
	if( $p_level != "super-manager" )
	{
		header( "Location: home.php" );
		exit();
	}
			
	// Post
	$post = $_POST;
	
	$id = $_REQUEST['id'];
	
	// FreshBooks API
	$fb = new FreshBooksAPI();
	
	$data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
	<request method=\"client.get\">
	  <client_id>$id</client_id>
	</request>";
	$fb_result = $fb->post( $data );
	
	//Creating Instance of the Class
	$xml = new xmlarray($fb_result);
	//Creating Array
	$arrayData = $xml->createArray();

	$data = $arrayData['response']['client'][0];
	
	if( sizeof( $post ) > 0 )
	{	
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
		<request method=\"client.update\">
		  <client>
			 <client_id>".$post['client_id']."</client_id>
		    <first_name>".$post['fname']."</first_name>
		    <last_name>".$post['lname']."</last_name>
		    <organization>".$post['organization']."</organization>
		    <email>".$post['email']."</email> 
		    <username>".$post['username']."</username>
		    <password>".$post['client_password']."</password>
		    <work_phone>".$post['bus_phone']."</work_phone>
		    <home_phone>".$post['home_phone']."</home_phone>
		    <mobile>".$post['mob_phone']."</mobile>
		    <fax>".$post['fax']."</fax>
		    <notes>".$post['note']."</notes>
		    <p_street1>".$post['p_street']."</p_street1>
		    <p_street2>".$post['p_street2']."</p_street2>
		    <p_city>".$post['p_city']."</p_city>
		    <p_state>".$post['p_state']."</p_state>
		    <p_country>".$post['p_country']."</p_country>
		    <p_code>".$post['p_code']."</p_code>
	 		 <s_street1>".$post['s_street']."</s_street1>
	 		 <s_street2>".$post['s_street2']."</s_street2>
	 		 <s_city>".$post['s_city']."</s_city>
	 		 <s_state>".$post['s_state']."</s_state>
	 		 <s_country>".$post['s_country']."</s_country>
	 		 <s_code>".$post['s_code']."</s_code>
		  </client>
		</request>";
	
		$fb->post( $xml );
		
		header( "Location: clients.php" );	
	}
	
	// Include header TEMPLATE
	include( "header_template.php" );
?>

<!-- Start of Client -->
<div id="newClient">
<h1>Update Client</h1>
<form action="client_edit.php" method="post">
<input type="hidden" name="client_id" value="<?= $id ?>">
<fieldset>
<legend>Your Client Contact Information</legend>
<table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="newTable pad">
	<tr>
		<td class="formTitle"><div style="cursor:pointer;display:inline;" onClick="document.getElementById('organization').focus()">Organization:&nbsp;</div></td>
	    <td><input class="formTextMed" tabindex='1' type="text" name="organization" id="organization" value="<?= $data['organization'] ?>" maxlength="100"></td>
	    <td class="formTitle"><div style="cursor:pointer;display:inline;" onClick="document.getElementById('username').focus()">Username:&nbsp;</div></td>
	    <td><input class="formTextMedReq" tabindex='5' type='text' name='username' id="username" value="<?= $data['username'] ?>" maxlength="100"></td>
	</tr>
	<tr>

    	<td class="formTitle"><div style="cursor:pointer;display:inline;" onClick="document.getElementById('fname').focus()">Contact First Name:&nbsp;</div></td>
	    <td><input class="formTextMedReq" tabindex='2' type="text" name="fname" id="fname" value="<?= $data['first_name'] ?>" maxlength="100">
	    <td class="formTitle"><div style="cursor:pointer;display:inline;" onClick="document.getElementById('client_password').focus()"><span class="example">*</span>New Password:&nbsp;</div></td>
	    <td><input class="formTextMed" tabindex='6' type='password' name='client_password' id="client_password" value='' maxlength=20></td>
	</tr>
	<tr>
		<td class="formTitle"><div style="cursor:pointer;display:inline;" onClick="document.getElementById('lname').focus()">Contact Last Name:&nbsp;</div></td>
	    <td><input class="formTextMedReq" tabindex='3' type="text" name="lname" id="lname" value="<?= $data['last_name'] ?>" maxlength=20></td>
	    <td class="formTitle"><div style="cursor:pointer;display:inline;" onClick="document.getElementById('password2').focus()"><span class="example">*</span>Confirm Password:&nbsp;</div></td>
		<td><input class="formTextMed" tabindex='7' type='password' name='password2' id="password2" value='' maxlength=20></td>
	</tr>
	<tr>
		<td class="formTitle"><div style="cursor:pointer;display:inline;" onClick="document.getElementById('email').focus()">Email:&nbsp;</div></td>
		<td><input class="formTextMedReq" tabindex='4' type="text" name="email" id="email" value="<?= $data['email'] ?>" maxlength="100" onChange='updateUsername(email, username2);' ></td>
		<td colspan="2" class="example" valign="bottom"><strong>*</strong>If no password is entered, one will be created randomly.</td>
	 </tr>
</table>
</fieldset>
<br>
<fieldset>
		<legend>Your Client Address</legend>
		<table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="newTable pad">
	<tr>
    	<td class="centerTitle" colspan="2"><div style="cursor:pointer;display:inline;" onClick="document.getElementById('p_country').focus()">Primary Mailing Address</div></td>

    	<td class="centerTitle" colspan="2"><div style="cursor:pointer;display:inline;" onClick="document.getElementById('s_country').focus()">Secondary Address</div></td>
	</tr>
	<tr>
	    <td class="formTitle"><div style="cursor:pointer;display:inline;" onClick="document.getElementById('p_country').focus()">Country:&nbsp;</div></td>
	    <td><select class="formSelect" tabindex="110" name="p_country" id="p_country" onChange="changeState(this.form.p_country,'')"><option value='' selected>[Choose One]     <OPTION VALUE='United States'>United States<OPTION VALUE='Canada'>Canada<OPTION VALUE='United Kingdom'>United Kingdom  <OPTION value="">[none]
  <OPTION VALUE="Afghanistan">Afghanistan
  <OPTION VALUE="Albania">Albania
  <OPTION VALUE="Algeria">Algeria
  <OPTION VALUE="American Samoa">American Samoa
  <OPTION VALUE="Andorra">Andorra
  <OPTION VALUE="Angola">Angola
  <OPTION VALUE="Anguilla">Anguilla
  <OPTION VALUE="Antarctica">Antarctica
  <OPTION VALUE="Antigua and Barbuda">Antigua and Barbuda
  <OPTION VALUE="Argentina">Argentina
  <OPTION VALUE="Armenia">Armenia
  <OPTION VALUE="Aruba">Aruba
  <OPTION VALUE="Australia">Australia
  <OPTION VALUE="Austria">Austria
  <OPTION VALUE="Azerbaijan">Azerbaijan
  <OPTION VALUE="Bahamas">Bahamas
  <OPTION VALUE="Bahrain">Bahrain
  <OPTION VALUE="Bangladesh">Bangladesh
  <OPTION VALUE="Barbados">Barbados
  <OPTION VALUE="Belarus">Belarus
  <OPTION VALUE="Belgium">Belgium
  <OPTION VALUE="Belize">Belize
  <OPTION VALUE="Benin">Benin
  <OPTION VALUE="Bermuda">Bermuda
  <OPTION VALUE="Bhutan">Bhutan
  <OPTION VALUE="Bolivia">Bolivia
  <OPTION VALUE="Bosnia and Herzegovina">Bosnia and 
              Herzegovina
  <OPTION VALUE="Botswana">Botswana
  <OPTION VALUE="Bouvet Island">Bouvet Island
  <OPTION VALUE="Brazil">Brazil
  <OPTION VALUE="British Indian Ocean Territory">

              British Indian Ocean Territory
  <OPTION VALUE="Brunei Darussalam">Brunei Darussalam
  <OPTION VALUE="Bulgaria">Bulgaria
  <OPTION VALUE="Burkina Faso">Burkina Faso
  <OPTION VALUE="Burundi">Burundi
  <OPTION VALUE="Cambodia">Cambodia
  <OPTION VALUE="Cameroon">Cameroon
  <OPTION VALUE="Canada">Canada
  <OPTION VALUE="Cape Verde">Cape Verde
  <OPTION VALUE="Cayman Islands">Cayman Islands
  <OPTION VALUE="Central African Republic">
             Central African Republic
  <OPTION VALUE="Chad">Chad
  <OPTION VALUE="Chile">Chile
  <OPTION VALUE="China">China
  <OPTION VALUE="Christmas Island">Christmas Island
  <OPTION VALUE="Cocos (Keeling Islands)">

             Cocos (Keeling Islands)
  <OPTION VALUE="Colombia">Colombia
  <OPTION VALUE="Comoros">Comoros
  <OPTION VALUE="Congo">Congo
  <OPTION VALUE="Cook Islands">Cook Islands
  <OPTION VALUE="Costa Rica">Costa Rica
  <OPTION VALUE="Cote D'Ivoire (Ivory Coast)">
               Cote D'Ivoire (Ivory Coast)
  <OPTION VALUE="Croatia (Hrvatska)">Croatia (Hrvatska)
  <OPTION VALUE="Cuba">Cuba
  <OPTION VALUE="Cyprus">Cyprus
  <OPTION VALUE="Czech Republic">Czech Republic
  <OPTION VALUE="Denmark">Denmark
  <OPTION VALUE="Djibouti">Djibouti
  <OPTION VALUE="Dominican Republic">Dominican Republic
  <OPTION VALUE="Dominica">Dominica
  <OPTION VALUE="East Timor">East Timor
  <OPTION VALUE="Ecuador">Ecuador
  <OPTION VALUE="Egypt">Egypt
  <OPTION VALUE="El Salvador">El Salvador
  <OPTION VALUE="Equatorial Guinea">Equatorial Guinea
  <OPTION VALUE="Eritrea">Eritrea
  <OPTION VALUE="Estonia">Estonia
  <OPTION VALUE="Ethiopia">Ethiopia
  <OPTION VALUE="Falkland Islands (Malvinas)">

                  Falkland Islands (Malvinas)
  <OPTION VALUE="Faroe Islands">Faroe Islands
  <OPTION VALUE="Fiji">Fiji
  <OPTION VALUE="Finland">Finland
  <OPTION VALUE="France, Metropolitan">France, Metropolitan
  <OPTION VALUE="France">France
  <OPTION VALUE="French Guiana">French Guiana
  <OPTION VALUE="French Polynesia">French Polynesia
  <OPTION VALUE="French Southern Territories">
              French Southern Territories
  <OPTION VALUE="French West Indies"> French West Indies
  <OPTION VALUE="Gabon">Gabon
  <OPTION VALUE="Gambia">Gambia
  <OPTION VALUE="Georgia">Georgia
  <OPTION VALUE="Germany">Germany
  <OPTION VALUE="Ghana">Ghana
  <OPTION VALUE="Gibraltar">Gibraltar
  <OPTION VALUE="Greece">Greece
  <OPTION VALUE="Greenland">Greenland
  <OPTION VALUE="Grenada">Grenada
  <OPTION VALUE="Guadeloupe">Guadeloupe
  <OPTION VALUE="Guam">Guam
  <OPTION VALUE="Guatemala">Guatemala
  <OPTION VALUE="Guinea-Bissau">Guinea-Bissau
  <OPTION VALUE="Guinea">Guinea
  <OPTION VALUE="Guyana">Guyana
  <OPTION VALUE="Haiti">Haiti
  <OPTION VALUE="Heard and McDonald Islands">

            Heard and McDonald Islands
  <OPTION VALUE="Honduras">Honduras
  <OPTION VALUE="Hong Kong">Hong Kong
  <OPTION VALUE="Hungary">Hungary
  <OPTION VALUE="Iceland">Iceland
  <OPTION VALUE="India">India
  <OPTION VALUE="Indonesia">Indonesia
  <OPTION VALUE="Iran">Iran
  <OPTION VALUE="Iraq">Iraq
  <OPTION VALUE="Ireland">Ireland
  <OPTION VALUE="Israel">Israel
  <OPTION VALUE="Italy">Italy
  <OPTION VALUE="Jamaica">Jamaica
  <OPTION VALUE="Japan">Japan
  <OPTION VALUE="Jordan">Jordan
  <OPTION VALUE="Kazakhstan">Kazakhstan
  <OPTION VALUE="Kenya">Kenya
  <OPTION VALUE="Kiribati">Kiribati
  <OPTION VALUE="Korea (North)">Korea (North)
  <OPTION VALUE="Korea (South)">Korea (South)
  <OPTION VALUE="Kuwait">Kuwait
  <OPTION VALUE="Kyrgyzstan">Kyrgyzstan
  <OPTION VALUE="Laos">Laos
  <OPTION VALUE="Latvia">Latvia
  <OPTION VALUE="Lebanon">Lebanon
  <OPTION VALUE="Lesotho">Lesotho
  <OPTION VALUE="Liberia">Liberia
  <OPTION VALUE="Libya">Libya
  <OPTION VALUE="Liechtenstein">Liechtenstein
  <OPTION VALUE="Lithuania">Lithuania
  <OPTION VALUE="Luxembourg">Luxembourg
  <OPTION VALUE="Macau">Macau
  <OPTION VALUE="Macedonia">Macedonia
  <OPTION VALUE="Madagascar">Madagascar
  <OPTION VALUE="Malawi">Malawi
  <OPTION VALUE="Malaysia">Malaysia
  <OPTION VALUE="Maldives">Maldives
  <OPTION VALUE="Mali">Mali
  <OPTION VALUE="Malta">Malta
  <OPTION VALUE="Marshall Islands">Marshall Islands
  <OPTION VALUE="Martinique">Martinique
  <OPTION VALUE="Mauritania">Mauritania
  <OPTION VALUE="Mauritius">Mauritius
  <OPTION VALUE="Mayotte">Mayotte
  <OPTION VALUE="Mexico">Mexico
  <OPTION VALUE="Micronesia">Micronesia
  <OPTION VALUE="Moldova">Moldova
  <OPTION VALUE="Monaco">Monaco
  <OPTION VALUE="Mongolia">Mongolia
  <OPTION VALUE="Montenegro">Montenegro  
  <OPTION VALUE="Montserrat">Montserrat
  <OPTION VALUE="Morocco">Morocco
  <OPTION VALUE="Mozambique">Mozambique
  <OPTION VALUE="Myanmar">Myanmar
  <OPTION VALUE="Namibia">Namibia
  <OPTION VALUE="Nauru">Nauru
  <OPTION VALUE="Nepal">Nepal
  <OPTION VALUE="Netherlands Antilles">Netherlands Antilles
  <OPTION VALUE="Netherlands">Netherlands
  <OPTION VALUE="New Caledonia">New Caledonia
  <OPTION VALUE="New Zealand">New Zealand
  <OPTION VALUE="Nicaragua">Nicaragua
  <OPTION VALUE="Nigeria">Nigeria
  <OPTION VALUE="Niger">Niger
  <OPTION VALUE="Niue">Niue
  <OPTION VALUE="Norfolk Island">Norfolk Island
  <OPTION VALUE="Northern Mariana Islands">

             Northern Mariana Islands
  <OPTION VALUE="Norway">Norway
  <OPTION VALUE="Oman">Oman
  <OPTION VALUE="Pakistan">Pakistan
  <OPTION VALUE="Palau">Palau
  <OPTION VALUE="Panama">Panama
  <OPTION VALUE="Papua New Guinea">Papua New Guinea
  <OPTION VALUE="Paraguay">Paraguay
  <OPTION VALUE="Peru">Peru
  <OPTION VALUE="Philippines">Philippines
  <OPTION VALUE="Pitcairn">Pitcairn
  <OPTION VALUE="Poland">Poland
  <OPTION VALUE="Portugal">Portugal
  <OPTION VALUE="Puerto Rico">Puerto Rico
  <OPTION VALUE="Qatar">Qatar
  <OPTION VALUE="Reunion">Reunion
  <OPTION VALUE="Romania">Romania
  <OPTION VALUE="Russian Federation">Russian Federation
  <OPTION VALUE="Rwanda">Rwanda
 <!-- <OPTION VALUE="S. Georgia and S. Sandwich Isls.">
         S. Georgia and S. Sandwich Isls. -->

  <OPTION VALUE="Saint Kitts and Nevis">Saint Kitts and Nevis
  <OPTION VALUE="Saint Lucia">Saint Lucia
  <!-- <OPTION VALUE="Saint Vincent and The Grenadines">
         Saint Vincent and The Grenadines -->
  <OPTION VALUE="Samoa">Samoa
  <OPTION VALUE="San Marino">San Marino
  <OPTION VALUE="Sao Tome and Principe">Sao Tome and Principe
  <OPTION VALUE="Saudi Arabia">Saudi Arabia
  <OPTION VALUE="Senegal">Senegal
  <OPTION VALUE="Serbia">Serbia  
  <OPTION VALUE="Seychelles">Seychelles
  <OPTION VALUE="Sierra Leone">Sierra Leone
  <OPTION VALUE="Singapore">Singapore
  <OPTION VALUE="Slovak Republic">Slovak Republic
  <OPTION VALUE="Slovenia">Slovenia
  <OPTION VALUE="Solomon Islands">Solomon Islands
  <OPTION VALUE="Somalia">Somalia
  <OPTION VALUE="South Africa">South Africa
  <OPTION VALUE="Spain">Spain
  <OPTION VALUE="Sri Lanka">Sri Lanka
  <OPTION VALUE="St. Helena">St. Helena
  <OPTION VALUE="St. Pierre and Miquelon">

              St. Pierre and Miquelon
  <OPTION VALUE="Sudan">Sudan
  <OPTION VALUE="Suriname">Suriname
  <!-- <OPTION VALUE="Svalbard and Jan Mayen Islands">
             Svalbard and Jan Mayen Islands -->
  <OPTION VALUE="Svalbard">
             Svalbard
  <OPTION VALUE="Swaziland">Swaziland
  <OPTION VALUE="Sweden">Sweden
  <OPTION VALUE="Switzerland">Switzerland
  <OPTION VALUE="Syria">Syria
  <OPTION VALUE="Taiwan">Taiwan
  <OPTION VALUE="Tajikistan">Tajikistan
  <OPTION VALUE="Tanzania">Tanzania
  <OPTION VALUE="Thailand">Thailand
  <OPTION VALUE="Togo">Togo
  <OPTION VALUE="Tokelau">Tokelau
  <OPTION VALUE="Tonga">Tonga
  <OPTION VALUE="Trinidad and Tobago">Trinidad and Tobago
  <OPTION VALUE="Tunisia">Tunisia
  <OPTION VALUE="Turkey">Turkey
  <OPTION VALUE="Turkmenistan">Turkmenistan
  <OPTION VALUE="Turks and Caicos Islands">

       Turks and Caicos Islands
  <OPTION VALUE="Tuvalu">Tuvalu
  <OPTION VALUE="US Minor Outlying Islands">
     US Minor Outlying Islands
  <OPTION VALUE="Uganda">Uganda
  <OPTION VALUE="Ukraine">Ukraine
  <OPTION VALUE="United Arab Emirates">
     United Arab Emirates
  <OPTION VALUE="United Kingdom">United Kingdom
  <OPTION VALUE="United States">United States
  <OPTION VALUE="Uruguay">Uruguay
  <OPTION VALUE="Uzbekistan">Uzbekistan
  <OPTION VALUE="Vanuatu">Vanuatu
  <OPTION VALUE="Vatican City State">Vatican City State
  <OPTION VALUE="Venezuela">Venezuela
  <OPTION VALUE="Viet Nam">Viet Nam
  <OPTION VALUE="Virgin Islands (British)">

     Virgin Islands (British)
  <OPTION VALUE="Virgin Islands (US)">
     Virgin Islands (US)
  <OPTION VALUE="Wallis and Futuna Islands">
     Wallis and Futuna Islands
  <OPTION VALUE="Western Sahara">Western Sahara
  <OPTION VALUE="Yemen">Yemen
  <OPTION VALUE="Yugoslavia">Yugoslavia
  <OPTION VALUE="Zaire">Zaire
  <OPTION VALUE="Zambia">Zambia
  <OPTION VALUE="Zimbabwe">Zimbabwe

 </select></td>
	    <td class="formTitle"><div style="cursor:pointer;display:inline;" onClick="document.getElementById('s_country').focus()">Country:&nbsp;</div></td>

	    <td><select class="formSelect" tabindex="114"  name="s_country" id="s_country" onChange="changeState(this.form.s_country,'2')"><option value='' selected>[Choose One]     <OPTION VALUE='United States'>United States<OPTION VALUE='Canada'>Canada<OPTION VALUE='United Kingdom'>United Kingdom  <OPTION value="">[none]
  <OPTION VALUE="Afghanistan">Afghanistan
  <OPTION VALUE="Albania">Albania
  <OPTION VALUE="Algeria">Algeria
  <OPTION VALUE="American Samoa">American Samoa
  <OPTION VALUE="Andorra">Andorra
  <OPTION VALUE="Angola">Angola
  <OPTION VALUE="Anguilla">Anguilla
  <OPTION VALUE="Antarctica">Antarctica
  <OPTION VALUE="Antigua and Barbuda">Antigua and Barbuda
  <OPTION VALUE="Argentina">Argentina
  <OPTION VALUE="Armenia">Armenia
  <OPTION VALUE="Aruba">Aruba
  <OPTION VALUE="Australia">Australia
  <OPTION VALUE="Austria">Austria
  <OPTION VALUE="Azerbaijan">Azerbaijan
  <OPTION VALUE="Bahamas">Bahamas
  <OPTION VALUE="Bahrain">Bahrain
  <OPTION VALUE="Bangladesh">Bangladesh
  <OPTION VALUE="Barbados">Barbados
  <OPTION VALUE="Belarus">Belarus
  <OPTION VALUE="Belgium">Belgium
  <OPTION VALUE="Belize">Belize
  <OPTION VALUE="Benin">Benin
  <OPTION VALUE="Bermuda">Bermuda
  <OPTION VALUE="Bhutan">Bhutan
  <OPTION VALUE="Bolivia">Bolivia
  <OPTION VALUE="Bosnia and Herzegovina">Bosnia and 
              Herzegovina
  <OPTION VALUE="Botswana">Botswana
  <OPTION VALUE="Bouvet Island">Bouvet Island
  <OPTION VALUE="Brazil">Brazil
  <OPTION VALUE="British Indian Ocean Territory">

              British Indian Ocean Territory
  <OPTION VALUE="Brunei Darussalam">Brunei Darussalam
  <OPTION VALUE="Bulgaria">Bulgaria
  <OPTION VALUE="Burkina Faso">Burkina Faso
  <OPTION VALUE="Burundi">Burundi
  <OPTION VALUE="Cambodia">Cambodia
  <OPTION VALUE="Cameroon">Cameroon
  <OPTION VALUE="Canada">Canada
  <OPTION VALUE="Cape Verde">Cape Verde
  <OPTION VALUE="Cayman Islands">Cayman Islands
  <OPTION VALUE="Central African Republic">
             Central African Republic
  <OPTION VALUE="Chad">Chad
  <OPTION VALUE="Chile">Chile
  <OPTION VALUE="China">China
  <OPTION VALUE="Christmas Island">Christmas Island
  <OPTION VALUE="Cocos (Keeling Islands)">

             Cocos (Keeling Islands)
  <OPTION VALUE="Colombia">Colombia
  <OPTION VALUE="Comoros">Comoros
  <OPTION VALUE="Congo">Congo
  <OPTION VALUE="Cook Islands">Cook Islands
  <OPTION VALUE="Costa Rica">Costa Rica
  <OPTION VALUE="Cote D'Ivoire (Ivory Coast)">
               Cote D'Ivoire (Ivory Coast)
  <OPTION VALUE="Croatia (Hrvatska)">Croatia (Hrvatska)
  <OPTION VALUE="Cuba">Cuba
  <OPTION VALUE="Cyprus">Cyprus
  <OPTION VALUE="Czech Republic">Czech Republic
  <OPTION VALUE="Denmark">Denmark
  <OPTION VALUE="Djibouti">Djibouti
  <OPTION VALUE="Dominican Republic">Dominican Republic
  <OPTION VALUE="Dominica">Dominica
  <OPTION VALUE="East Timor">East Timor
  <OPTION VALUE="Ecuador">Ecuador
  <OPTION VALUE="Egypt">Egypt
  <OPTION VALUE="El Salvador">El Salvador
  <OPTION VALUE="Equatorial Guinea">Equatorial Guinea
  <OPTION VALUE="Eritrea">Eritrea
  <OPTION VALUE="Estonia">Estonia
  <OPTION VALUE="Ethiopia">Ethiopia
  <OPTION VALUE="Falkland Islands (Malvinas)">

                  Falkland Islands (Malvinas)
  <OPTION VALUE="Faroe Islands">Faroe Islands
  <OPTION VALUE="Fiji">Fiji
  <OPTION VALUE="Finland">Finland
  <OPTION VALUE="France, Metropolitan">France, Metropolitan
  <OPTION VALUE="France">France
  <OPTION VALUE="French Guiana">French Guiana
  <OPTION VALUE="French Polynesia">French Polynesia
  <OPTION VALUE="French Southern Territories">
              French Southern Territories
  <OPTION VALUE="French West Indies"> French West Indies
  <OPTION VALUE="Gabon">Gabon
  <OPTION VALUE="Gambia">Gambia
  <OPTION VALUE="Georgia">Georgia
  <OPTION VALUE="Germany">Germany
  <OPTION VALUE="Ghana">Ghana
  <OPTION VALUE="Gibraltar">Gibraltar
  <OPTION VALUE="Greece">Greece
  <OPTION VALUE="Greenland">Greenland
  <OPTION VALUE="Grenada">Grenada
  <OPTION VALUE="Guadeloupe">Guadeloupe
  <OPTION VALUE="Guam">Guam
  <OPTION VALUE="Guatemala">Guatemala
  <OPTION VALUE="Guinea-Bissau">Guinea-Bissau
  <OPTION VALUE="Guinea">Guinea
  <OPTION VALUE="Guyana">Guyana
  <OPTION VALUE="Haiti">Haiti
  <OPTION VALUE="Heard and McDonald Islands">

            Heard and McDonald Islands
  <OPTION VALUE="Honduras">Honduras
  <OPTION VALUE="Hong Kong">Hong Kong
  <OPTION VALUE="Hungary">Hungary
  <OPTION VALUE="Iceland">Iceland
  <OPTION VALUE="India">India
  <OPTION VALUE="Indonesia">Indonesia
  <OPTION VALUE="Iran">Iran
  <OPTION VALUE="Iraq">Iraq
  <OPTION VALUE="Ireland">Ireland
  <OPTION VALUE="Israel">Israel
  <OPTION VALUE="Italy">Italy
  <OPTION VALUE="Jamaica">Jamaica
  <OPTION VALUE="Japan">Japan
  <OPTION VALUE="Jordan">Jordan
  <OPTION VALUE="Kazakhstan">Kazakhstan
  <OPTION VALUE="Kenya">Kenya
  <OPTION VALUE="Kiribati">Kiribati
  <OPTION VALUE="Korea (North)">Korea (North)
  <OPTION VALUE="Korea (South)">Korea (South)
  <OPTION VALUE="Kuwait">Kuwait
  <OPTION VALUE="Kyrgyzstan">Kyrgyzstan
  <OPTION VALUE="Laos">Laos
  <OPTION VALUE="Latvia">Latvia
  <OPTION VALUE="Lebanon">Lebanon
  <OPTION VALUE="Lesotho">Lesotho
  <OPTION VALUE="Liberia">Liberia
  <OPTION VALUE="Libya">Libya
  <OPTION VALUE="Liechtenstein">Liechtenstein
  <OPTION VALUE="Lithuania">Lithuania
  <OPTION VALUE="Luxembourg">Luxembourg
  <OPTION VALUE="Macau">Macau
  <OPTION VALUE="Macedonia">Macedonia
  <OPTION VALUE="Madagascar">Madagascar
  <OPTION VALUE="Malawi">Malawi
  <OPTION VALUE="Malaysia">Malaysia
  <OPTION VALUE="Maldives">Maldives
  <OPTION VALUE="Mali">Mali
  <OPTION VALUE="Malta">Malta
  <OPTION VALUE="Marshall Islands">Marshall Islands
  <OPTION VALUE="Martinique">Martinique
  <OPTION VALUE="Mauritania">Mauritania
  <OPTION VALUE="Mauritius">Mauritius
  <OPTION VALUE="Mayotte">Mayotte
  <OPTION VALUE="Mexico">Mexico
  <OPTION VALUE="Micronesia">Micronesia
  <OPTION VALUE="Moldova">Moldova
  <OPTION VALUE="Monaco">Monaco
  <OPTION VALUE="Mongolia">Mongolia
  <OPTION VALUE="Montenegro">Montenegro  
  <OPTION VALUE="Montserrat">Montserrat
  <OPTION VALUE="Morocco">Morocco
  <OPTION VALUE="Mozambique">Mozambique
  <OPTION VALUE="Myanmar">Myanmar
  <OPTION VALUE="Namibia">Namibia
  <OPTION VALUE="Nauru">Nauru
  <OPTION VALUE="Nepal">Nepal
  <OPTION VALUE="Netherlands Antilles">Netherlands Antilles
  <OPTION VALUE="Netherlands">Netherlands
  <OPTION VALUE="New Caledonia">New Caledonia
  <OPTION VALUE="New Zealand">New Zealand
  <OPTION VALUE="Nicaragua">Nicaragua
  <OPTION VALUE="Nigeria">Nigeria
  <OPTION VALUE="Niger">Niger
  <OPTION VALUE="Niue">Niue
  <OPTION VALUE="Norfolk Island">Norfolk Island
  <OPTION VALUE="Northern Mariana Islands">

             Northern Mariana Islands
  <OPTION VALUE="Norway">Norway
  <OPTION VALUE="Oman">Oman
  <OPTION VALUE="Pakistan">Pakistan
  <OPTION VALUE="Palau">Palau
  <OPTION VALUE="Panama">Panama
  <OPTION VALUE="Papua New Guinea">Papua New Guinea
  <OPTION VALUE="Paraguay">Paraguay
  <OPTION VALUE="Peru">Peru
  <OPTION VALUE="Philippines">Philippines
  <OPTION VALUE="Pitcairn">Pitcairn
  <OPTION VALUE="Poland">Poland
  <OPTION VALUE="Portugal">Portugal
  <OPTION VALUE="Puerto Rico">Puerto Rico
  <OPTION VALUE="Qatar">Qatar
  <OPTION VALUE="Reunion">Reunion
  <OPTION VALUE="Romania">Romania
  <OPTION VALUE="Russian Federation">Russian Federation
  <OPTION VALUE="Rwanda">Rwanda
 <!-- <OPTION VALUE="S. Georgia and S. Sandwich Isls.">
         S. Georgia and S. Sandwich Isls. -->

  <OPTION VALUE="Saint Kitts and Nevis">Saint Kitts and Nevis
  <OPTION VALUE="Saint Lucia">Saint Lucia
  <!-- <OPTION VALUE="Saint Vincent and The Grenadines">
         Saint Vincent and The Grenadines -->
  <OPTION VALUE="Samoa">Samoa
  <OPTION VALUE="San Marino">San Marino
  <OPTION VALUE="Sao Tome and Principe">Sao Tome and Principe
  <OPTION VALUE="Saudi Arabia">Saudi Arabia
  <OPTION VALUE="Senegal">Senegal
  <OPTION VALUE="Serbia">Serbia  
  <OPTION VALUE="Seychelles">Seychelles
  <OPTION VALUE="Sierra Leone">Sierra Leone
  <OPTION VALUE="Singapore">Singapore
  <OPTION VALUE="Slovak Republic">Slovak Republic
  <OPTION VALUE="Slovenia">Slovenia
  <OPTION VALUE="Solomon Islands">Solomon Islands
  <OPTION VALUE="Somalia">Somalia
  <OPTION VALUE="South Africa">South Africa
  <OPTION VALUE="Spain">Spain
  <OPTION VALUE="Sri Lanka">Sri Lanka
  <OPTION VALUE="St. Helena">St. Helena
  <OPTION VALUE="St. Pierre and Miquelon">

              St. Pierre and Miquelon
  <OPTION VALUE="Sudan">Sudan
  <OPTION VALUE="Suriname">Suriname
  <!-- <OPTION VALUE="Svalbard and Jan Mayen Islands">
             Svalbard and Jan Mayen Islands -->
  <OPTION VALUE="Svalbard">
             Svalbard
  <OPTION VALUE="Swaziland">Swaziland
  <OPTION VALUE="Sweden">Sweden
  <OPTION VALUE="Switzerland">Switzerland
  <OPTION VALUE="Syria">Syria
  <OPTION VALUE="Taiwan">Taiwan
  <OPTION VALUE="Tajikistan">Tajikistan
  <OPTION VALUE="Tanzania">Tanzania
  <OPTION VALUE="Thailand">Thailand
  <OPTION VALUE="Togo">Togo
  <OPTION VALUE="Tokelau">Tokelau
  <OPTION VALUE="Tonga">Tonga
  <OPTION VALUE="Trinidad and Tobago">Trinidad and Tobago
  <OPTION VALUE="Tunisia">Tunisia
  <OPTION VALUE="Turkey">Turkey
  <OPTION VALUE="Turkmenistan">Turkmenistan
  <OPTION VALUE="Turks and Caicos Islands">

       Turks and Caicos Islands
  <OPTION VALUE="Tuvalu">Tuvalu
  <OPTION VALUE="US Minor Outlying Islands">
     US Minor Outlying Islands
  <OPTION VALUE="Uganda">Uganda
  <OPTION VALUE="Ukraine">Ukraine
  <OPTION VALUE="United Arab Emirates">
     United Arab Emirates
  <OPTION VALUE="United Kingdom">United Kingdom
  <OPTION VALUE="United States">United States
  <OPTION VALUE="Uruguay">Uruguay
  <OPTION VALUE="Uzbekistan">Uzbekistan
  <OPTION VALUE="Vanuatu">Vanuatu
  <OPTION VALUE="Vatican City State">Vatican City State
  <OPTION VALUE="Venezuela">Venezuela
  <OPTION VALUE="Viet Nam">Viet Nam
  <OPTION VALUE="Virgin Islands (British)">

     Virgin Islands (British)
  <OPTION VALUE="Virgin Islands (US)">
     Virgin Islands (US)
  <OPTION VALUE="Wallis and Futuna Islands">
     Wallis and Futuna Islands
  <OPTION VALUE="Western Sahara">Western Sahara
  <OPTION VALUE="Yemen">Yemen
  <OPTION VALUE="Yugoslavia">Yugoslavia
  <OPTION VALUE="Zaire">Zaire
  <OPTION VALUE="Zambia">Zambia
  <OPTION VALUE="Zimbabwe">Zimbabwe

 </select></td>
	</tr>

	<tr>
    	<td class="formTitle"><div style="cursor:pointer;display:inline;" onClick="document.getElementById('p_street').focus()">Street Address:&nbsp;</div></td>
	    <td><input class="formTextMed" tabindex='110' type="text" name="p_street" id="p_street" value="<?= $data['p_street1'] ?>" maxlength="100"></td>
		<td class="formTitle"><div style="cursor:pointer;display:inline;" onClick="document.getElementById('s_street').focus()">Street Address:&nbsp;</div></td>
	    <td><input class="formTextMed" tabindex='114' type="text" name="s_street" id="s_street" value="<?= $data['s_street1'] ?>" maxlength="100"></td>
	</tr>
	<tr>
		<td align="right"><div style="cursor:pointer;display:inline;" onClick="document.getElementById('p_street2').focus()"></div></td>

		<td><input class="formTextMed" tabindex='110' type="text" name="p_street2" id="p_street2" value="<?= $data['p_street2'] ?>" maxlength="100"></td>
	    <td align="right"><div style="cursor:pointer;display:inline;" onClick="document.getElementById('s_street2').focus()"></div></td>
    	<td><input class="formTextMed" tabindex='114' type="text" name="s_street2" id="s_street2" value="<?= $data['s_street2'] ?>" maxlength="100"></td>
	</tr>
	<tr>
		<td class="formTitle"><div style="cursor:pointer;display:inline;" onClick="document.getElementById('p_city').focus()">City:&nbsp;</div></td>
		<td><input class="formTextMed" tabindex='110' type="text" name="p_city" id="p_city" value="<?= $data['p_city'] ?>" maxlength="100"></td>
		<td  class="formTitle"><div style="cursor:pointer;display:inline;" onClick="document.getElementById('s_city').focus()">City:&nbsp;</div></td>

		<td><input class="formTextMed" tabindex='114' type="text" name="s_city" id="s_city" value="<?= $data['s_city'] ?>" maxlength="100"></td>
	</tr>
	<tr>
    	<td class="formTitle">Province/State:&nbsp;</td>
	    <td><input  class="formTextMed" ID="others" tabindex='110' TYPE="TEXT" NAME="p_state" SIZE="25" value="" maxlength="50" ></td>
	    <td class="formTitle">Province/State:&nbsp;</td>
	    <td><input  class="formTextMed" ID="others" tabindex='110' TYPE="TEXT" NAME="s_state" SIZE="25" value="" maxlength="50" ></td>
	</tr>
	<tr>
    	<td class="formTitle"><div style="cursor:pointer;display:inline;" onClick="document.getElementById('p_code').focus()">Postal/Zip Code:&nbsp;</div></td>
		<td><input class="formTextMed" tabindex='110' type="text" name="p_code" id="p_code" value="<?= $data['p_code'] ?>" maxlength="100"></td>
	    <td class="formTitle"><div style="cursor:pointer;display:inline;" onClick="document.getElementById('s_code').focus()">Postal/Zip Code:&nbsp;</div></td>

	    <td><input class="formTextMed" tabindex='116' type="text" name="s_code" id="s_code" value="<?= $data['s_code'] ?>" maxlength="100"></td>
	</tr>
	<tr>
	    <td class="formTitle"><div style="cursor:pointer;display:inline;" onClick="document.getElementById('bus_phone').focus()">Phone - <em>Business</em>:&nbsp;</div></td>
	    <td><input class="formTextMed" tabindex='111' type="text" name="bus_phone" id="bus_phone" value="<?= $data['work_phone'] ?>" maxlength="100"></td>
	    <td class="formTitle"><div style="cursor:pointer;display:inline;" onClick="document.getElementById('note').focus()">Notes:&nbsp;</div></td><td>(not visible to client)</td>
	</tr>
	<tr>
    	<td class="formTitle"><div style="cursor:pointer;display:inline;" onClick="document.getElementById('mob_phone').focus()">Phone -<em> Mobile</em>:&nbsp;</div></td>
		<td><input class="formTextMed" tabindex='112' type="text" name="mob_phone" id="mob_phone" value="<?= $data['mobile'] ?>" maxlength="100"></td>
    	<td colspan="2" rowspan='4' align="center" valign="top"><textarea class="formAreaThin" tabindex='116' name='note' id="note"></textarea></td>
	</tr>

	<tr>
    	<td class="formTitle"><div style="cursor:pointer;display:inline;" onClick="document.getElementById('home_phone').focus()">Phone -<em> Home</em>:&nbsp;</div></td>
	    <td><input class="formTextMed" tabindex='112' type="text" name="home_phone" id="home_phone" value="<?= $data['home_phone'] ?>" maxlength="100"></td>
	</tr>
	<tr>
		<td class="formTitle"><div style="cursor:pointer;display:inline;" onClick="document.getElementById('fax').focus()">Phone - <em>Fax</em>:&nbsp;</div></td>

		<td><input class="formTextMed" tabindex='113' type="text" name="fax" id="fax" value="<?= $data['fax'] ?>" maxlength="100"></td>
	</tr>
 </table>
</fieldset>

<p align="center" class="large_button"><input type="submit" value="Save"></p>
</form>

</div>
<!-- End of Client -->

<? include( "footer.php" ); ?>