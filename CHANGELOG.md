# html_ajax Changelog
---

> https://pear.php.net/package/HTML_AJAX/download/All

## 0.1.3

> Release Time: 2005-08-11
>
> Download Link: http://download.pear.php.net/package/HTML_AJAX-0.1.3.tgz

```
Update comments to PEAR coding standards
```

## 0.1.0

> Release Time: 2005-08-11
>
> Download Link: http://download.pear.php.net/package/HTML_AJAX-0.1.0.tgz

```
initial release
```

## 0.1.4

> Release Time: 2005-08-19
>
> Download Link: http://download.pear.php.net/package/HTML_AJAX-0.1.4.tgz

```
PEAR CS fixes
Support for generating multiple stubs in a single request stub=test,test2
304 Http Cache support for Client and Stub generation, this is on by default, caching rules are configurable see docblocks for more info
```

## 0.2.0

> Release Time: 2005-09-27
>
> Download Link: http://download.pear.php.net/package/HTML_AJAX-0.2.0.tgz

```
Reliense under the LGPL fixing concerns about GPL compability

Full rewrite of all JavaScript code pulled in from JPSpan, this allows for relicence, as well as shrinking the code size while adding new features
   Big new Features are: 
   Request object non contains all information needed to make a request, HTML_AJAX.makeRequest added service a request object
   HTML_AJAX_HttpClient instances now created as needed by a factory HTML_AJAX.httpClient(), this functionality will be replaced at some future point

   These changes will allow for various queue and pool structures to be created in the future, but for now client in progress errors should not be possible
   	when using proxy objects

Serializer that mimics post added, filling _POST on an ajax request, helper code for AJAX forms still in progress

Bugs Fixed:
5087, 5284 	- jsClient Location fixes, allows it to be set manually
5908 		- PHP 5 bug fix, auto loading of classes not working in php5 for an unknown reason, just load serializer as a normal include
5029 		- init bug in auto_server
```

## 0.2.1

> Release Time: 2005-09-30
>
> Download Link: http://download.pear.php.net/package/HTML_AJAX-0.2.1.tgz

```
This release is mainly bug fixes.
	Remove debug message when throwing an exception
	Fix problems with async calls
	Fix broken content-type detection
	Stop trying to run an init method when the init flag isn't set
	PHP required version moved to 4.1.0 which is what it should need

There is also a couple small features added
	A basic debug class has been added, allowing you to write PHP errors to a file
	HTML_AJAX.replace now works async
```

## 0.2.3

> Release Time: 2005-10-24
>
> Download Link: http://download.pear.php.net/package/HTML_AJAX-0.2.3.tgz

```
Initial Helper API see examples/usage_helper.php for details
	HTML_AJAX.append
	BC Change, default loading implementation moved to its own file
	client param takes a comma seperated list of args now ex: server.php?client=main,httpclient
	ajax server has support for delivering custom libraries HTML_AJAX_Server::registerJSLibrary
Bug #5675 Reference bug in php 4.4 5.1 etc
```

## 0.2.4

> Release Time: 2005-10-26
>
> Download Link: http://download.pear.php.net/package/HTML_AJAX-0.2.4.tgz

```
#5788  	New bug in Ajax Server , cant create JS stub
	New login example
```

## 0.2.5

> Release Time: 2005-11-01
>
> Download Link: http://download.pear.php.net/package/HTML_AJAX-0.2.5.tgz

```
Small optimizations and cleanup of HTML_AJAX and HTML_AJAX_Server
	Case fixes for specifing case while exporting a class
	Fix url creation in javascript allowing server urls to contain parameters
	Added a flag to turn off sending a Content-Length header
```

## 0.3.0

> Release Time: 2005-11-17
>
> Download Link: http://download.pear.php.net/package/HTML_AJAX-0.3.0.tgz

```
BC BREAK - in js renamed event handlers so they don't collide with native ones and cause problems onOpen -> Open, onLoad -> Load, onProgress -> Progress, onSend -> Send
   Added stub support to helper class
   HTML_AJAX_Util (javascript):
   - setElementEvent() renamed to registerEvent(), set event should now be fixed
   - New methods: getType() and strRepeat()
   - Fixed methods: varDump() and getElementsByClassName()
   Behavior javascript files moved to own directory and HTML_AJAX_Server updated to reflect change
   Better example for behavior use in example directory
   Fixed URL encoding serializer, requires PHP 5 or the PHP_Compat implementation of http_build_query()
   Added serializer compatible with PHP's native serialization functions
   Small optimizations and cleanup of HTML_AJAX
   Added a request priority queue
   Added HTTP client pooling
   Update and cleaned up the examples
   Pass errors from sync calls to HTML_AJAX.onError
```

## 0.3.1

> Release Time: 2005-12-05
>
> Download Link: http://download.pear.php.net/package/HTML_AJAX-0.3.1.tgz

```
Rearranged the examples dir, moving support files and tests into there own directory
	Updated examples index.php page so that it makes sense when shown from the HTML_AJAX website
	Fix some various IE bugs, grabbing non-existant headers was throwing errors
	Fixed problem with async requests timing out in IE
	Added IFrame fallback, targeted at versions of Opera before XMLHttpRequest and IE with ActiveX turned off
	Fix js file detection when not installed through PEAR
	Fix numerous IE 5 javascript bugs and added js compat functions
	haSerializer and HTML_AJAX_Action bugs fixed
```

## 0.3.3

> Release Time: 2005-12-07
>
> Download Link: http://download.pear.php.net/package/HTML_AJAX-0.3.3.tgz

```
- Firefox bugs
- added arpad as maintainer.
```

## 0.3.2

> Release Time: 2005-12-07
>
> Download Link: http://download.pear.php.net/package/HTML_AJAX-0.3.2.tgz

```
Rearranged the examples dir, moving support files and tests into there own directory
	Updated examples index.php page so that it makes sense when shown from the HTML_AJAX website
	Fix some various IE bugs, grabbing non-existant headers was throwing errors
	Fixed problem with async requests timing out in IE
	Added IFrame fallback, targeted at versions of Opera before XMLHttpRequest and IE with ActiveX turned off
	Fix js file detection when not installed through PEAR
	Fix numerous IE 5 javascript bugs and added js compat functions
    haSerializer and HTML_AJAX_Action bugs fixed
    Fixed Numerous Firefox bugs
```

## 0.3.4

> Release Time: 2006-01-31
>
> Download Link: http://download.pear.php.net/package/HTML_AJAX-0.3.4.tgz

```
Fix for Bug #6478 problems with assignAttr in HTML_AJAX_Action
	Add a flag (HTML_AJAX->php4CompatCase) that allows for exporting all introspected method/class names in php5 in lower case for php4 compat
	Update to the newest Services_JSON code, still waiting for a Services_JSON release so we can stop embedding it (fixes bug: #6501)
	Fix bug #6424, getting extra Request timeouts errors
	Fix bug #6564, Don't send Content-Length header when output buffering is enabled
	Fix bug #6295, Use std input when HTTP_RAW_POST isn't set
	Fix for Main.js compatibility function tests
	Patch from Julien Wajsberg, Keep extra query params passed to HTML_AJAX_Server in the default serverUrl, this helps when integrating with frameworks
	Fix bug #6478, Not possible to set className in firefox using HTML_AJAX_Action, now were setting the attribute directly instead of calling setAttribute
```

## 0.4.0

> Release Time: 2006-04-07
>
> Download Link: http://download.pear.php.net/package/HTML_AJAX-0.4.0.tgz

```
Added ordered Queue Support and the slow_livesearch example to show it off
	Added setInnerHTML method to HTML_AJAX_Util (Bug #6672)
	Added getElementsByCssSelector, hasClass, addClass, removeClass, replaceClass, getElement methods to HTML_AJAX_Util
	Method parameter change to HTML_AJAX.fullCall the last option is now a hash for setting any option on the request object instead of just its customHeaders property
	Added support for setting options to HTML_AJAX.grab
	Added slow livesearch example
	Added jsEncode method to generate a JSON string from a single variable
	Added dhtmlHistory.js to provide access to DHTML history, history data, and bookmarking (not fully integrated yet)
	Added support to formEncode to return form data in an array
	Added support for exporting callbacks using the PHP callback pseudo-type
```

