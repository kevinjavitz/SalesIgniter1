// FIX EOLAS version 2
//
// (c) David Grudl
//
// more info: http://knowhow.davidgrudl.com/HTML/Eolas-workaround/


var objects = document.getElementsByTagName("object");

function eolas(i)
{
	if(objects[i]){
        objects[i].outerHTML = objects[i].outerHTML;
	}
}

for (var i=0; i<objects.length; i++)
    window.setTimeout("eolas(" + i + ")", 1);
