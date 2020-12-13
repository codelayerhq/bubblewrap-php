# PHP Bubblewrap

![](https://github.com/codelayerhq/bubblewrap-php/workflows/PHP%20Composer/badge.svg) 
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
![Packagist Version](https://img.shields.io/packagist/v/codelayer/bubblewrap)

A PHP wrapper class for the [bubblewrap](https://github.com/containers/bubblewrap) cli tool.
Can be used to execute external commands like imagemagick or ghostscript in a separated environment
as a protection against possible security issues.

## Installation

The package can be installed using composer:

```
$ composer require codelayer/bubblewrap
```

## Usage

At the moment most commands that don't take a file descriptor as argument are implemented. 
This snippet shows how the example from the bubblewrap readme looks in PHP:

```php
$bwrap = new \Codelayer\Bubblewrap\Bubblewrap();

$bwrap
    ->readOnlyBind('/usr') // If src equals dest only one parameter is required for bind commands
    ->symlink('usr/lib64', '/lib')
    ->proc() // /proc is used as default location for procfs
    ->dev()  // /dev  is used as default location for devtmpfs
    ->unsharePid()
    ->exec(['bash']);
```

Exec returns a [Symfony Process](https://symfony.com/doc/current/components/process.html) object.

## Additional methods

Two additional methods are available to modify how bubblewrap is called:

 * `setBinary($binary)` sets the location of the bwrap binary
 * `clearEnv()` prepends `env -i` to the bwrap calls and unsets all environment variables
 * `getCommand($cmd)` show the full command that `exec($cmd)` would execute

## License

This package is licensed under the MIT License.
For more information see the [LICENSE file](./LICENSE).

## About us

codelayer is a software company from Karlsruhe, Germany which specializes in web application development.  
For more information about codelayer, visit our website at [codelayer.de](https://codelayer.de).
