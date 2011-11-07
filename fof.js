var oldInp=0;
function clickage(evt)
{
    elements = document.forms[0].elements;
    evt=(evt)?evt:event;
    var target=(evt.target)?evt.target:evt.srcElement;
    if(!evt.shiftKey)
    {
        oldInp=target.name.substr(target.name.indexOf(".")+1);
    //alert(oldInp);
        return false;
    }
    target.checked=1;
    var low=Math.min(target.name.substr(target.name.indexOf(".")+1),oldInp);
    var high=Math.max(target.name.substr(target.name.indexOf(".")+1),oldInp)
    for(i=0;i<elements.length;i++)
    {
        if (elements[i].name.match(/^c/))
        {
             if ((parseInt(elements[i].name.substr(target.name.indexOf(".")+1)) >= low)&&(parseInt(elements[i].name.substr(target.name.indexOf(".")+1)) <= high))
             {
                 elements[i].checked = true;
             }
        }
    }
    return true;
}

function flag_upto(id)
{
	elements = document.forms[0].elements;
	nam = '';
	
	for(i = 0; i<elements.length; i++)
	{
        if (elements[i].name.match(/^c/))
		{
			elements[i].checked = true;
			nam = nam + " " + elements[i].name;	
		}
		if(elements[i].name == id)
			break;
	}
	//alert(nam);
}

function toggle_arrowimage(img)
{
	//'exp$item_id'
	var s = document.getElementById(img);
	//print s.src;
	var image = s.src.split("/").pop();
	if (image == "ipodarrowright.jpg")
	{
		s.src = "ipodarrowdown.jpg";
	}
	else
	{
		s.src = "ipodarrowright.jpg";
	}
}


function toggle_expand_item(id)
{
        elements = document.getElementsBySelector('.item div#' + id);

        for(i=0; i<elements.length; i++)
        {
            elements[i].style.display = (elements[i].style.display != 'none') ? 'none' : '';
        }
}


function toggle_expand_all()
{
        elements = document.getElementsBySelector('.item .body');
	for(i=0; i<elements.length; i++)
	{
            elements[i].style.display = (elements[i].style.display != 'none') ? 'none' : '';
        }

	ctrlelem = document.getElementsBySelector('.item .control');
	for(i=0; i<ctrlelem.length; i++)
	{
            ctrlelem[i].style.display = (ctrlelem[i].style.display != 'none') ? 'none' : '';
        }

}

function toggle_flags_all()
{
        elements = document.forms[0].elements;

        for(i=0; i<elements.length; i++)
        {
                if (elements[i].name.match(/^c/))
                {
                    elements[i].checked = (elements[i].checked == false) ? true : false;
                }
        }
}

function flag_all()
{
	elements = document.forms[0].elements;
	
	for(i=0; i<elements.length; i++)
	{
                if (elements[i].name.match(/^c/))
		    elements[i].checked = true;
	}
}

function unflag_all()
{
	elements = document.forms[0].elements;
	
	for(i=0; i<elements.length; i++)
	{
                if (elements[i].name.match(/^c/))
		    elements[i].checked = false;
	}
}

function mark_read()
{
	//document.items['action'].value = 'read';
	//document.items['return'].value = escape(location);
	document.getElementById('action').value = 'read';
    document.getElementById('return').value = escape(location);
	//alert(document.items['action'].value);
	document.items.submit();
}

function mark_unread()
{
	document.items['action'].value = 'unread';
	document.items['return'].value = escape(location);
	document.items.submit();
}
function togglePublish(item)
{
       url = "view-action.php?c"+item.value+"=checked&action=" +
		(item.checked ? "publish" : "unpublish");
       SendRequest(url);                       
}
function togStar(item)
{
       var s = document.getElementById(item);
       var image = s.src.split("/").pop();
       if (image == "star_off.gif") 
       {
           s.src = "star_on.gif";
           url = "view-action.php?"+s.id+"=starred&action=star";
       }
       else
       {
           s.src = "star_off.gif";
           url = "view-action.php?"+s.id+"=starred&action=unstar";
       }
       SendRequest(url);                       
}

/*
function twitterit(title,url)
{
	var urlz = SendTINYURLRequest(title,url);
	SendTwitterRequest(title,urlz); 
	//return urlz;
	//perform POST action to Twitter
}
*/

/* document.getElementsBySelector(selector)
   - returns an array of element objects from the current document
     matching the CSS selector. Selectors can contain element names, 
     class names and ids and can be nested. For example:
     
       elements = document.getElementsBySelect('div#main p a.external')
     
     Will return an array of all 'a' elements with 'external' in their 
     class attribute that are contained inside 'p' elements that are 
     contained inside the 'div' element which has id="main"

   New in version 0.4: Support for CSS2 and CSS3 attribute selectors:
   See http://www.w3.org/TR/css3-selectors/#attribute-selectors

   Version 0.4 - Simon Willison, March 25th 2003
   -- Works in Phoenix 0.5, Mozilla 1.3, Opera 7, Internet Explorer 6, Internet Explorer 5 on Windows
   -- Opera 7 fails 
*/

function getAllChildren(e) {
  // Returns all children of element. Workaround required for IE5/Windows. Ugh.
  return e.all ? e.all : e.getElementsByTagName('*');
}

document.getElementsBySelector = function(selector) {
  // Attempt to fail gracefully in lesser browsers
  if (!document.getElementsByTagName) {
    return new Array();
  }
  // Split selector in to tokens
  var tokens = selector.split(' ');
  var currentContext = new Array(document);
  for (var i = 0; i < tokens.length; i++) {
    token = tokens[i].replace(/^\s+/,'').replace(/\s+$/,'');;
    if (token.indexOf('#') > -1) {
      // Token is an ID selector
      var bits = token.split('#');
      var tagName = bits[0];
      var id = bits[1];
      var element = document.getElementById(id);
      if (tagName && element.nodeName.toLowerCase() != tagName) {
        // tag with that ID not found, return false
        return new Array();
      }
      // Set currentContext to contain just this element
      currentContext = new Array(element);
      continue; // Skip to next token
    }
    if (token.indexOf('.') > -1) {
      // Token contains a class selector
      var bits = token.split('.');
      var tagName = bits[0];
      var className = bits[1];
      if (!tagName) {
        tagName = '*';
      }
      // Get elements matching tag, filter them for class selector
      var found = new Array;
      var foundCount = 0;
      for (var h = 0; h < currentContext.length; h++) {
        var elements;
        if (tagName == '*') {
            elements = getAllChildren(currentContext[h]);
        } else {
            elements = currentContext[h].getElementsByTagName(tagName);
        }
        for (var j = 0; j < elements.length; j++) {
          found[foundCount++] = elements[j];
        }
      }
      currentContext = new Array;
      var currentContextIndex = 0;
      for (var k = 0; k < found.length; k++) {
        if (found[k].className && found[k].className.match(new RegExp('\\b'+className+'\\b'))) {
          currentContext[currentContextIndex++] = found[k];
        }
      }
      continue; // Skip to next token
    }
    // Code to deal with attribute selectors
    if (token.match(/^(\w*)\[(\w+)([=~\|\^\$\*]?)=?"?([^\]"]*)"?\]$/)) {
      var tagName = RegExp.$1;
      var attrName = RegExp.$2;
      var attrOperator = RegExp.$3;
      var attrValue = RegExp.$4;
      if (!tagName) {
        tagName = '*';
      }
      // Grab all of the tagName elements within current context
      var found = new Array;
      var foundCount = 0;
      for (var h = 0; h < currentContext.length; h++) {
        var elements;
        if (tagName == '*') {
            elements = getAllChildren(currentContext[h]);
        } else {
            elements = currentContext[h].getElementsByTagName(tagName);
        }
        for (var j = 0; j < elements.length; j++) {
          found[foundCount++] = elements[j];
        }
      }
      currentContext = new Array;
      var currentContextIndex = 0;
      var checkFunction; // This function will be used to filter the elements
      switch (attrOperator) {
        case '=': // Equality
          checkFunction = function(e) { return (e.getAttribute(attrName) == attrValue); };
          break;
        case '~': // Match one of space seperated words 
          checkFunction = function(e) { return (e.getAttribute(attrName).match(new RegExp('\\b'+attrValue+'\\b'))); };
          break;
        case '|': // Match start with value followed by optional hyphen
          checkFunction = function(e) { return (e.getAttribute(attrName).match(new RegExp('^'+attrValue+'-?'))); };
          break;
        case '^': // Match starts with value
          checkFunction = function(e) { return (e.getAttribute(attrName).indexOf(attrValue) == 0); };
          break;
        case '$': // Match ends with value - fails with "Warning" in Opera 7
          checkFunction = function(e) { return (e.getAttribute(attrName).lastIndexOf(attrValue) == e.getAttribute(attrName).length - attrValue.length); };
          break;
        case '*': // Match ends with value
          checkFunction = function(e) { return (e.getAttribute(attrName).indexOf(attrValue) > -1); };
          break;
        default :
          // Just test for existence of attribute
          checkFunction = function(e) { return e.getAttribute(attrName); };
      }
      currentContext = new Array;
      var currentContextIndex = 0;
      for (var k = 0; k < found.length; k++) {
        if (checkFunction(found[k])) {
          currentContext[currentContextIndex++] = found[k];
        }
      }
      // alert('Attribute Selector: '+tagName+' '+attrName+' '+attrOperator+' '+attrValue);
      continue; // Skip to next token
    }
    // If we get here, token is JUST an element (not a class or ID selector)
    tagName = token;
    var found = new Array;
    var foundCount = 0;
    for (var h = 0; h < currentContext.length; h++) {
      var elements = currentContext[h].getElementsByTagName(tagName);
      for (var j = 0; j < elements.length; j++) {
        found[foundCount++] = elements[j];
      }
    }
    currentContext = found;
  }
  return currentContext;
}

/* That revolting regular expression explained 
/^(\w+)\[(\w+)([=~\|\^\$\*]?)=?"?([^\]"]*)"?\]$/
  \---/  \---/\-------------/    \-------/
    |      |         |               |
    |      |         |           The value
    |      |    ~,|,^,$,* or =
    |   Attribute 
   Tag
*/

