Contao-PHPSass
==============

Extension for the Contao CMS that allows usage of the PHPSass library.


Usage
=====

With this extension you can use [SASS](http://sass-lang.com/) right from your contao without the need of [Ruby](http://www.ruby-lang.org/) or [compass.app](http://compass.handlino.com/) using the PHP library [PHPSass](http://www.phpsass.com/).

1. Within the contao backend under "Layout" goto "PHPSass".
2. Then create a new SASS folder
3. Give it a title and at least select the directory where your SASS files are located and where your CSS files should be created.
4. Be sure to not check the "disabled" option to activate the compilation.
5. Save and go to your frontend. The CSS files will get compiled on each page request.
