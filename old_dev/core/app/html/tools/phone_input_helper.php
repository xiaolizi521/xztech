<?PHP

define("NUMBER_CHARACTER", "#");

function getJumpingJavascript() {
    return "<SCRIPT LANGUAGE=\"JavaScript\">
        <!-- Original:  Cyanide_7 (leo7278@hotmail.com) -->
        <!-- Web Site:  http://members.xoom.com/cyanide_7 -->
        
        <!-- This script and many more are available free online at -->
        <!-- The JavaScript Source!! http://javascript.internet.com -->
        
        <!-- Begin
        var isNN = (navigator.appName.indexOf(\"Netscape\")!=-1);
        function autoTab(input,len, e) {
        var keyCode = (isNN) ? e.which : e.keyCode; 
        var filter = (isNN) ? [0,8,9] : [0,8,9,16,17,18,37,38,39,40,46];
        if(input.value.length >= len && !containsElement(filter,keyCode)) {
        input.value = input.value.slice(0, len);
        input.form[(getIndex(input)+1) % input.form.length].focus();
        }
        function containsElement(arr, ele) {
        var found = false, index = 0;
        while(!found && index < arr.length)
        if(arr[index] == ele)
        found = true;
        else
        index++;
        return found;
        }
        function getIndex(input) {
        var index = -1, i = 0, found = false;
        while (i < input.form.length && index == -1)
        if (input.form[i] == input)index = i;
        else i++;
        return index;
        }
        return true;
        }
        //  End -->
        </script>";
}

function getPhoneInputMarkup($inputNamePrefix, $class, $mask, $phone_number="", $printDebug=false) {
    $returnString = "";
    
    if($printDebug) {
        $returnString .= "Mask Used: " . $mask . "<br>\n";
        $returnString .= "Phone Used: " . $phone_number . "<br>\n";
    }
    
    $numberIndex = 0;
    $maxNumberIndex = strlen($phone_number);
    for($i=0; $i<strlen($mask); $i++) {
        if($mask[$i] != NUMBER_CHARACTER) {
            $returnString .= $mask[$i];
        }
        else {
            $charactersInInput = 0;
            $valueOfInput = "";
            while($i < strlen($mask) and 
                  $mask[$i] == NUMBER_CHARACTER) {
                $charactersInInput++;
                $i++;
                if($numberIndex < $maxNumberIndex) {
                    $valueOfInput .= $phone_number[$numberIndex];
                    $numberIndex++;
                }
            }
            $i--;
            $inputName = $inputNamePrefix . "[]";
            $returnString .= "<input type=\"text\" name=\"" . $inputName . "\" class=\"" . $class . "\" maxlength=\"" . $charactersInInput . "\" size=\"" . $charactersInInput . "\" value=\"" . $valueOfInput . "\" onkeyup=\"return autoTab(this, " . $charactersInInput . ", event);\">\n";
        }
    }
    
    return $returnString;   
}

?>
