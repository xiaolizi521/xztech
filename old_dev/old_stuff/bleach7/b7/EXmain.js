var EXlogin='konb7' // Login
var EXvsrv='s9' // VServer
navigator.javaEnabled() == 1 ? EXjv = "y" : EXjv = "n";

EXd = document;
EXw ? "" : EXw = "na";
EXb ? "" : EXb = "na";
EXd.write("<img src='http://e0.extreme-dm.com",
"/" + EXvsrv + ".g?login=" + EXlogin + "&amp;",
"jv=" + EXjv + "&amp;j=y&amp;srw=" + EXw + "&amp;srb=" + EXb + "&amp;",
"l=" + escape(EXd.referrer) + "' height='1' width='1' />");

/*var EXlogin='konb7' // Login
var EXvsrv='s9' // VServer
navigator.javaEnabled()==1?EXjv="y":EXjv="n";
EXd=document;EXw?"":EXw="na";EXb?"":EXb="na";
EXd.write("<img src=\"http://e0.extreme-dm.com",
"/"+EXvsrv+".g?login="+EXlogin+"&",
"jv="+EXjv+"&j=y&srw="+EXw+"&srb="+EXb+"&",
"l="+escape(EXd.referrer)+"\" height=1 width=1>");*/