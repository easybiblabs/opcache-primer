opcache-primer
==============

Scripts and tooling to prime the OPCache during deploys.


## Usage

### Via HTTP & OpsWorks' deploy hook

Do something like this in your frontcontroller (`index.php`):

```php
if ($_SERVER['SCRIPT_NAME'] == '/opcache/prime' && $_SERVER['SERVER_ADDR'] == '127.0.0.1') {
    require dirname(__DIR__) . '/vendor/autoload.php';
    $prime = new \EasyBib\OPcache\Juggler('/srv/www/app/releases', $maybeALogger);
    exit($prime->recycle($_GET['new'], $_GET['old']));
}
```

If you want to clear the opcache and varcache before, use `$prime->doResetAll()` instead of
`$prime->doPopulate()`.

Add the following to your deploy hook (AWS OpsWorks):

```ruby

# ...

prime_command = "curl -vvv -X GET http://127.0.0.1/opcache/prime?new=#{release_path}&old=#{path}"
prime_command << "; exit 0" # force success in case prime-opcache is not deployed

run "cd #{release_path} && #{composer_command} && #{prime_command}"
```

**Bonus points for a proper httpd configuration which blocks access to the script.**

### Via Cli (WIP)

See `examples/` directory for a cli implementation.

Also, check the console tool:

```
$ ./bin/opcache-primer.php
```

Available tasks:

 * `opcache:status` (quick and dirty status dump)
 * `opcache:juggle` (to prime a new release and remove an old one)
 * `apcu:clear` (to clear the variable cache)

## License

Apache-2.0