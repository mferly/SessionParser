# SessionParser
Use this to iterate through active session files within the defined PHP sessions folder found in `php.ini`

**Hint**: your sessions directory is probably located at `/var/lib/php/sessions/`

## Getting Started
Due to permission issues you'll likely run into if you attempt to execute this from the browser, use CLI only.

You'll want to have a look at `private static $needle = 'auth|b:1';` in [SessionParser.php](https://github.com/mferly/SessionParser/blob/master/SessionParser.php#L35) and make the necessary change to reflect your `$_SESSION` key. The Docblock has more info.

You'll likely have to `sudo` the following command for it to work.

`$ sudo php index.php`

Should return number of active sessions `¯\_(ツ)_/¯`

This is where a check is being made to determine length of time between current time - session file modified time, and if it's within bounds set by `static::$sessionGcMaxlifetime` then `static::$counter` is iterated.
```
if (time() - $file->getCTime() <= static::$sessionGcMaxlifetime) {
    static::$counter++;
}
```

## Prerequisites
* PHP v7.x (tested on v7.3)
