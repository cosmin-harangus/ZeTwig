ZeTwig
====

ZeTwig is a Twig / Zend Framework 2 module compatible with `Zend\View\PhpRenderer`.
It allows to render view templates using Twig instead of the default PHP templates.
It also supports aliases for your template names.

Documentation
-------------

Any command from the original Twig library should work and also added support for
Zend View helpers as functions and PHP functions as a fallback.

The use of the .twig extension within your code is not required. This allows you to
change this extension from twig to anything you want later on and adds a bit more
extensibility.

Also a new functionality allows the use of aliases within your template code or when
rendering a template.

You can define an array for aliases within the configuration file for your modules and
use those aliases throughout your code, instead of a specific file name. This way you
can easily change the main layout of your pages from the configuration file and allow
other modules to change them as well (this allows your code to be extensible and allows
templates to have their own structure).