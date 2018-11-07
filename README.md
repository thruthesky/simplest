# PHP Simplest

PHP Simplest is the most simple PHP framework But has complete, powerful functionality.

This is good for those who have a little programming knowledge.

No OOP, No Chaining, No PSR. Only simple code but Best framework.


## TODO

* Wrap db functions

Make below
````
db()->table(...)->fields(...)->where(...)->update()
````

as an alias of
````
_db_update( ...table, ...fields, ...where )
_db_insert()
_db_delete()
````


* Hook system.
    * Each hook must handle get input using _in().
    * Hooks can return a value or a value of array.

````
    add_hook('abc');
    hook('abc');
````

* Join with wordpress.
    * Use hook system to handle user auth.

* Member Management
    * default accounts
        * admin for the super user. can be changed.
        * guest for all the guest visitors.
            When a user visits the site and has not logged in, then the user is a guest and using guest account.



* Forum Management



## Backend & Frontend

The Simplest PHP is a headless framework.
This means it gets request and response in restful format.


Frontend can be a variable HTML, CSS, Javascript combination framework like Angular, Vue.

We have a simple builtin frontend with jQuery + Bootstrap.




## Function names

* all function name of Simplest begins with underbar.

````
function _in() {}
````


* all the variable names of Simple begins with underbar.



## Folder and File Structure

* All the functionality is in `extensions` folder.


### Run the functionality

HTTP Variable `run` will have a string to execute the script and function.

````
[extention-folder-name].[file-name][.function-name]
````

* function name is optional. If there is no function name, then the script itself will do all the work.

* For instance, `index.php?run=test.simplest.version` will run `version()` function in `simplest.php` under `extenstions/test` folder.



## Unit Test

````
$ cd tests
$ php all.php
````


## File upload

### Upload file name format

File names are composed by three parts.

````
[file.idx]-[file name].[extionsion]
````
