ZeTwig
====

ZeTwig is a Twig / Zend Framework 2 module compatible the beta4 version of the framework.
It allows to render view templates using Twig instead of the default PHP templates.
It also supports aliases for your template names, rendering a particular action from 
within the template files (follows the save naming conventions as Symfony) and 
triggering events on an object with different parameters.

Installation / Usage
--------------------

ZeTwig can be installed using Composer by simply adding the following lines to your composer.json file:

    "require": {
        "ZendExperts/ZeTwig": "1.0.*"
    }
    
Then run `php composer.phar update`.

The module also defines a set of options that you can change from within the configuration files:

    'zendexperts_zetwig' => array(
        //you can change the extension of the loaded templates here
        'template_suffix'       => 'twig',
        'extensions'            => array(
                //add any extensions you want to register with twig
            ),
        //set twig environment options
        'environment_options'   => array(
                'cache' => '/tmp',
                'auto_reload' => true,
                'debug' => true
            ),
    ),
	
ZeTwig integrates with the View Manager service and uses the same resolvers defined within that service.
This allows you to define the template path stacks and maps within the view manager without having to set them again when installing the module:

	'view_manager' => array(
        'template_path_stack'   => array(
            'application'              => __DIR__ . '/../views',
        ),
        'template_map' => array(
            'layouts/layout'    => __DIR__ . '/../views/layouts/layout.twig',
            'index/index'       => __DIR__ . '/../views/application/index/index.twig',
        ),
    ), 
    
Documentation
-------------

2012.05.25: Upgraded to the beta4 version of ZF2 View Models.

Any command from the original Twig library should work and also added support for
Zend View helpers as functions and PHP functions as a fallback.

You can define an array for aliases within the configuration file for your modules and
use those aliases throughout your code, instead of a specific file name. This way you
can easily change the main layout of your pages from the configuration file and allow
other modules to change them as well (this allows your code to be extensible and allows
templates to have their own structure).

Apart from the functionality listed above the module adds two extension tags:

1. A tag for rendering a controller action, which follows the Symfony naming conventions
   	or the controller alias:

    ```
   	{% render "Core:Index:index" %}
   	```

    The twig tag will call the action "index" from the "IndexController" located within the "Core"
    module and based on the returned value it will either render a specific template or output the
    returned value, following the same principles as with any other zf2 action.

    Optionally you can also specify different parameters to send to the processed action which can
    later be retrieved from the matched route:

    ```
	{% render "Core:Index:index" with {'param1':1} %}
	```

2. A tag for triggering an event on the renderer that is similar to the above syntax:

	```
	{% trigger "myRendererEvent" on myObject with {'param1':1} %}
	```
	
	Both the target object and parameters are optional. The result of each listener is 
converted to string and rendered intead of the definition.