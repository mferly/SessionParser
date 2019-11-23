# SessionParser
Use this to iterate through active session files within the defined PHP sessions folder found in `php.ini`

**Hint**: your sessions directory is probably located at `/var/lib/php/sessions/`

* [Getting Started](#getting-started)
* [Basic Usage](#basic-usage)
* [Prerequisites](#prerequisites)
* [After Thoughts](#after-thoughts)
* [Contributing](#contributing)
* [Licence](#licence)
* [Creator](#creator)

## Getting Started

**WARNING**: Don't keep these files in a `public` folder of any kind.

Due to permission issues you'll likely run into if you attempt to execute this from the browser, use CLI only.

You'll want to have a look at `private static $needle = 'auth|b:1';` in `SessionParser.php` and make the necessary change to reflect your `$_SESSION` key. The Docblock has more info.

## Basic Usage

You can pass your session directory to `(new \SessionParser\SessionParser)::init()` as an argument or just use the default as stated in `private static $sessionFolderPath = '/var/lib/php/sessions/';`

You'll likely have to `sudo` the following command for it to work. Or just run as `root`. Whatever works for your ENV.

`$ sudo php index.php`

Should return number of active sessions `¯\_(ツ)_/¯`

This is where a check is being made to determine length of time between current time - session file modified time, and if it's within bounds set by `static::$sessionGcMaxlifetime` then `static::$counter` is iterated.
```php
if (time() - $file->getCTime() <= static::$sessionGcMaxlifetime) {
    static::$counter++;
}
```

## Prerequisites
* PHP v7.x (tested on v7.3)

## After Thoughts
While this program will simply `echo` the result, there is no reason this cannot be modified to write the result to a file, database, emailed to a recipient, etc.

## Contributing
* [PSR](https://www.php-fig.org/) must be followed.
* All classes **MUST** implement an interface. Pull requests with classes that do not implement an interface will be rejected.

## Licence
[MIT](https://opensource.org/licenses/MIT)

## Creator
[mferly](https://www.reddit.com/user/mferly)
