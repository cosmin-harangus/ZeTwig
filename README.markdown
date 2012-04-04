ZeTwig
====

ZeTwig is a Twig / Zend Framework 2 module compatible with ViewModels from beta3.
It allows to render view templates using Twig instead of the default PHP templates.
It also supports aliases for your template names, rendering a particular action from 
within the template files (follows the save naming conventions as Symfony) and 
triggering events on an object with different parameters.

Documentation
-------------

Upgraded to the new beta3 version of ZF2 View Models.

Any command from the original Twig library should work and also added support for
Zend View helpers as functions and PHP functions as a fallback.

With this new update you should be able to use template names as aliases that can be 
mapped to any twig file. 

You can define an array for aliases within the configuration file for your modules and
use those aliases throughout your code, instead of a specific file name. This way you
can easily change the main layout of your pages from the configuration file and allow
other modules to change them as well (this allows your code to be extensible and allows
templates to have their own structure).

This latest version also contains two new constructs:

1. A tag for rendering a controller action, which follows the Symfony naming conventions 
	or the controller alias and can be used as :
	
	{% render "Core:Index:index" with {'param1':1} %}

2. A tag for triggering an event on the renderer that is similar to the above syntax:
	
	{% trigger "myRendererEvent" on myObject with {'param1':1} %}
	
	Both the target object and parameters are optional. The result of each listener is 
converted to string and rendered intead of the definition.

Also a new functionality allows the use of aliases within your template code or when
rendering a template.