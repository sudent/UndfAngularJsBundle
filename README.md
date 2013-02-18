AngularJsBundle
===============

This bundle provides you with an easy way to integrate AngularJs into your Symfony2 projects.

It comes with the version 1.0.4 of the AngularJs framework and with a catalogue
of angular modules which is very easy to extend with your own modules. Such
modules are at this moment split into two main groups:
	
	- vendors: Includes a set of widely used modules (ngResource, angular-ui, ...)
	- undf: Includes a set of modules developed by our developers team.

The bundle also comes with an assetic filter (called 'angularjs'), which provides
you with an easy way to include all the modules you want to use in your
application (and only those) in one single output file.




Installation
============

Edit your composer.json and add this line in the "require" object
```
	"undf/angularjsbundle" :"dev-master"
```

Enable the bundle in your AppKernel.php
```
	new Udf\AngularJSBundle\UdfAngularJSBundle()
```




Usage
=====

#### - [1]. Take a look at the list of available modules:

```
	php app/console undf:angularjs:catalogue
```



####- [2]. Make your module selection

The bundle comes with 3 predefined sets of modules:

- novendors: It includes just the framework with no modules.
- default: It includes all vendor modules from the catalogue.
- all: It includes all available modules from the catalogue.

You can add your own module set by editing your config.yml file:
```
undf_angular_js:
  module_sets:
    mycustomset:
      #Use the star character to include all modules from a root module
      vendors: '*'
      undf: [uFileUpload, taggable]

```
You can also extend one of the predefined sets:
```
undf_angular_js:
  module_sets:
    novendors:
      vendors: [ngResource, ui]

```



####- [3]. Generate the master document for every configured module set:
```
php app/console undf:angularjs:create-master-files @MyBundle/Resources/public/js

```



####- [4]. Include the master document corresponding to the module set you want to use in your templates:
```jinja
{% javascripts
                //...your other javascript files...

                '@MyBundle/Resources/public/js/default.undfangular.js'

                //...your angularjs application files...

                filter="angularjs"
                output="yourOutputFile.js" %}
    <script src="{{ asset_url }}" type="text/javascript" ></script>
{% endjavascripts %}

```

The "angularjs" is compatible with any other chained filter you want to apply.
Just keep in mind that it should run in the first place, otherwise, the generated
content will not go throw the chained filter.



####- [5]. Configure your angular application to use the generated module set:
Once the angularjs assets are dumped, a new angular module will be created at
the end of the dumped file; this module will include all modules within your
module set as module dependencies. However, this module will be useless untill
you add it as a dependency for you angular application.
    The name of such a module is always build based on the name of the module set:


| Module set | Module name            |
| :--------: | :--------------------: |
| default    | undfDefaultModule      |
| novendors  | undfNovendorsModule    |
| ...        | ...                    |



```javascript
var app = angular.module('myangularapp', ['undfDefaultModule']);

```




Using your own modules
======================

####- [1]. Extend the catalogue:
```yaml
undf_angular_js:
  catalogue:
    mypackagename:
      myfirstmodule:
        description: 'Enter the module description here'
        files:
            # Absolute paths to the required files to use this module
            - @MyBundle/Resources/public/js/mymodule.js
      mysecondmodule:
        description: 'Enter the module description here'
        files:
            - @MyOtherBundle/Resources/public/othermodule.js

```
Tip: Run the undf:angularjs:catalogue command to make sure your new modules
have been added.



####- [2]. Add the new modules into a module set:
```
undf_angular_js:
  module_sets:
    mycustomset:
      vendors: [ngResource, ui]
      mypackagename: [myfirstmodule, mysecondmodule]

```
Repeat steps 3, 4 and 5 described in the "Usage" section.





Contributing
============
This is our first approach to the Symfony2-AngularJs integration.
The main goal is to get a flexible and easy to extend repository of AngularJs
modules, so those modules can be easily reused from one project to another.

Any ideas or suggestions to improve this bundle will be very welcome.

Also, don´t hesitate sending a pull request to include your Angular module
within the bundle catalogue, which can be done in only 2 steps:

    1. Add the JS files within the Resources/public/js folder
    2. Edit the catalogue file (Resources/config/catalogue.yml). Make sure that
The name you use for the module in the catalogue in exactly the same as in the
module declaration in the JS file.


TODO
====
* Create a css filter to handle all related css files.

* Allow twig template files as related files.

* Split vendor modules in separate files.

* Add unit testing



