opcache-primer
==============

Scripts and tooling to prime the OPCache during deploys.


## Usage

Do something like this in your frontcontroller (`index.php`):

```php
if ($_SERVER['SCRIPT_NAME'] == '/opcache/prime' && $_SERVER['SERVER_ADDR'] == '127.0.0.1') {
    require dirname(__DIR__) . '/vendor/autoload.php';
    $prime = new \EasyBib\OPcache\Prime($bibEnv);

    if (0 !== ($status = $prime->setPath($_GET['p']))) {
        exit($status);
    }

    exit($prime->doPopulate());
}
```

If you want to clear the opcache and varcache before, use `$prime->doResetAll()` instead of
`$prime->doPopulate()`.

Add the following to your deploy hook (AWS OpsWorks):

```ruby

# ...

prime_command = "curl -vvv -X GET http://127.0.0.1/opcache/prime?p=#{release_path}"
prime_command << "; exit 0" # force success in case prime-opcache is not deployed

run "cd #{release_path} && #{composer_command} && #{prime_command}"
```

Bonus points for a proper httpd configuration which blocks access to the script.

## License

BSD-2-Clause
