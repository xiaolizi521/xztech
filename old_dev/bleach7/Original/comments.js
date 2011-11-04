function InsertSmile( expression ) {
	document.comment_form.comment_post.value += ' :'+expression+' ';
}

function InsertBold() {
	document.comment_form.comment_post.value += ' [b] [/b] ';
}

function InsertItalic() {
	document.comment_form.comment_post.value += ' [i] [/i] ';
}

function InsertUnderline() {
	document.comment_form.comment_post.value += ' [u] [/u] ';
}

function InsertSpoiler() {
	document.comment_form.comment_post.value += ' [spoiler] [/spoiler] ';
}

function InsertURL() {
	urllink = prompt ("Enter the url you want to insert.");
	urltext = prompt ("Enter the text you want to have in place of the url");
	document.comment_form.comment_post.value += ' [url='+urllink+']'+urltext+'[/url] ';
}

function InsertColor() {
	colorlink = prompt ("Enter the color you want to insert.");
	colortext = prompt ("Enter the text you want to have in place of the color");
	document.comment_form.comment_post.value += ' [color='+colorlink+']'+colortext+'[/color] ';
}

function InsertHL() {
	hllink = prompt ("Enter the highlight you want to insert.");
	hltext = prompt ("Enter the text you want to have in place of the highlight");
	document.comment_form.comment_post.value += ' [hl='+hllink+']'+hltext+'[/hl] ';
}
