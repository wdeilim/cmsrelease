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

