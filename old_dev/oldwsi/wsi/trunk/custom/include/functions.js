var Base64 = {

    // private property
    _keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

    // public method for encoding
    encode : function (input) {
        var output = "";
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
        var i = 0;

        input = Base64._utf8_encode(input);

        while (i < input.length) {

            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);

            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;

            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }

            output = output +
            this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
            this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

        }

        return output;
    },

    // public method for decoding
    decode : function (input) {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;

        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

        while (i < input.length) {

            enc1 = this._keyStr.indexOf(input.charAt(i++));
            enc2 = this._keyStr.indexOf(input.charAt(i++));
            enc3 = this._keyStr.indexOf(input.charAt(i++));
            enc4 = this._keyStr.indexOf(input.charAt(i++));

            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;

            output = output + String.fromCharCode(chr1);

            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }

        }

        output = Base64._utf8_decode(output);

        return output;

    },

    // private method for UTF-8 encoding
    _utf8_encode : function (string) {
        string = string.replace(/\r\n/g,"\n");
        var utftext = "";

        for (var n = 0; n < string.length; n++) {

            var c = string.charCodeAt(n);

            if (c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }

        }

        return utftext;
    },

    // private method for UTF-8 decoding
    _utf8_decode : function (utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;

        while ( i < utftext.length ) {

            c = utftext.charCodeAt(i);

            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            }
            else if((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i+1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else {
                c2 = utftext.charCodeAt(i+1);
                c3 = utftext.charCodeAt(i+2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }

        }

        return string;
    }

}

function collapseMenus()
{
	var menus = new Array(3);
	
	menus[0] = "availableitems";
	menus[1] = "fontoptions";
	menus[2] = "backgroundoptions";
	
	for (i = 0; i < menus.length; i++)
	{
		c = $(menus[i]);
		
		if (c.style.display != "none")
		{
			Effect.BlindUp(c);
		}
	}
	
	$('expand').innerHTML = "Expand Menus";
	$('expand').onclick = expandMenus;
}

function expandMenus()
{
	var menus = new Array(3);
	
	menus[0] = "availableitems";
	menus[1] = "fontoptions";
	menus[2] = "backgroundoptions";
		
	for (i = 0; i < menus.length; i++)
	{
		c = $(menus[i]);
		
		if (c.style.display == "none")
		{
			Effect.BlindDown(c);
		}
	}
	
	$('expand').innerHTML = "Collapse Menus";
	$('expand').onclick = collapseMenus;
}

function enableBorder()
{
	if ($('border').checked)
	{
		$('container').style.border = "1px solid black";
	}
	else
	{
		$('container').style.border = "";
	}
}

function changeBackground()
{
	$('container').style.backgroundImage = "url('http://www.offbeat-zero.net/pulse/" + $('background').value + "')";
}

function changeFont()
{
    var container = $('container');

    if (container.hasChildNodes())
    {
        var children = container.childNodes;

        for (i = 0; i < children.length; i++)
        {
            var image = children[i].firstChild;
            var font  = Base64.encode(String($('font').value));

            image.src = image.src.replace(/(&b=)[^&]+/, "$1" + font);
        }
    }
}

function changeFontSize()
{
	var container = $('container');
	
	if (container.hasChildNodes())
	{
		var children = container.childNodes;
		
		for (i = 0; i < children.length; i++)
		{
            var image = children[i].firstChild;
            var fontsize    = $('fontsize').value;
            fontsize        = ((fontsize <= 25) ? fontsize : 25);
            fontsize        = Base64.encode(String(fontsize));

            image.src = image.src.replace(/(&r=)[^&]+/, "$1" + fontsize);
		}
	}
}

function changeFontColor()
{
    var container = $('container');

    if (container.hasChildNodes())
    {
        var children = container.childNodes;

        for (i = 0; i < children.length; i++)
        {
            var image = children[i].firstChild;
            var color = Base64.encode(String($('color').value));

            image.src = image.src.replace(/(&g=)[^&]+/, "$1" + color);
        }
    }
}

function loadLightBox(id)
{
	$(id).style.display='block';
	$('fade').style.display='block';
}

function closeLightBox(id)
{
	$(id).style.display='none';
	$('fade').style.display='none';
}

function Collapse(id)
{
	var i = $(id);
	var im = $(id + "Img");
	
	if (i.style.display == "none")
	{
		im.src = "http://pulse.offbeat-zero.net/images/icons/bullet_arrow_top.png";
		Effect.BlindDown(i);
	}
	else
	{
		im.src = "http://pulse.offbeat-zero.net/images/icons/bullet_arrow_bottom.png";
		Effect.BlindUp(i);
	}
}

function ShowCoordinates()
{
	var container = $("container");
	var c = $("coords");
	
	if (container.hasChildNodes())
	{
		var children = container.childNodes;
		var coords = "";
		
		for (var i = 0; i < children.length; i++)
		{
			if (children[i].id != null)
			{
				coords += children[i].id + ": " + children[i].offsetLeft + ", ";
				coords += children[i].offsetTop + "<br />";
			}
		}
		
		Element.update(c, coords);
	}
}

function hideItem(item)
{
	var i = $(item);
	Element.hide(item);
}

function DeleteItem(item)
{
	var i = $(item);
	Element.remove(item);
	Element.show(item + "Item");
}

function ClearItems()
{
	var container = $("container");
	if (container.hasChildNodes())
	{
		while (container.childNodes)
		{
			var child = container.firstChild;
			Element.show(child.id + "Item");
			container.removeChild(container.firstChild);
		}
	}
}

function AddItem(container, item, value, x, y)
{
    var coords;
    var text    = item + ": " + ((item == "country") ? "" : value);
	var divItem = document.createElement("div");
	var parent  = $(container);
	var dLink   = "<a class='delete' href='#' onClick=\"DeleteItem('"+ item +"')\">[x]</a>";
    var image   = "<img src=\"item/item.png?z=";
    image       += Base64.encode(String(text)); 
    image       += "&a=" + Base64.encode(String(x+y+666));
    image       += "&b=" + Base64.encode(String($('font').value));
    image       += "&r=" + Base64.encode(String($('fontsize').value));
    image       += "&g=" + Base64.encode(String($('color').value));
    image       += "\" />";

	divItem.className = "item";
	divItem.id = item;
	divItem.parent = parent;
	divItem.innerHTML = image + ((item == "country") ? (value + dLink) : dLink);
    parent.appendChild(divItem);
    
    coords = snapInContainer(x, y, null, item);
	
    new Draggable(divItem, {revert:false, 
		snap:function(x,y,draggable){return snapInContainer(x,y,draggable,null)}});

    new Effect.MoveBy(divItem, coords[1], coords[0], {duration: 0}); 
	
    new Effect.Highlight(divItem, {duration: 0.5});
	
	hideItem(item + "Item");	
}

function snapInContainer(x, y, draggable, item)
{
	var dimensions = Element.getDimensions((draggable) ? draggable.element : item);
	
    var xMin = 3;
	var xMax = 497 - dimensions.width;
	var yMin = 3;
	var yMax = 37 - dimensions.height;
	
    x = x < xMin ? xMin : x;
	x = x > xMax ? xMax : x;
	y = y < yMin ? yMin : y;
	y = y > yMax ? yMax : y;
	
	return [x, y];
}

Event.observe(window, 'load', function(){
	changeBackground();
	enableBorder();
	Event.observe('fontsize', 'change', changeFontSize);
	Event.observe('font', 'change', changeFont);
	Event.observe('background', 'change', changeBackground);
	Event.observe('border', 'change', enableBorder);
});
