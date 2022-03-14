# Quiz Ramadan El-Hayat School

This is a project is made with **Laravel** and **React**.

## DB

![DB](DB.png)

----
----

## WEAK AUTH feature

----

### :warning: Disclaimer

**:warning: Don't consider this under any circumstances as a sirious security measurement**

For example the hashed password will remain in the users navigator...

----

### The usage

Set your plaintext authentication/authorization password in `config\pass.php` as:

```php
return 'pass';
```

Register the `WeakAuth` middlware in `app\Http\Kernel.php` `$routeMiddleware` as:

```php
'auth.weak' => \App\Http\Middleware\WeakAuth::class,
```

Append `->middleware('auth.weak')` to the target routes.

Call `{{ _p_field() }}` in your forms (similar to `@csrf`).

Redirect like this:

```php
return to_route('quiz.index', ['_p' => get_weak_auth_hashed_password()]);
```

### The setup

Create `app\Http\Middleware\WeakAuth.php` as:

```php
class WeakAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!isset($request->_p) || !password_verify_weak_auth($request->_p)) {
            return response('unauthorized', 401);
        }

        return $next($request);
    }
}
```

Create `app\Http\helpers.php` as:

```php
function get_weak_auth_password(): string
{
    return config('pass');
}

function get_weak_auth_hashed_password(): string
{
    return password_hash(config('pass'), PASSWORD_DEFAULT);
}

function password_verify_weak_auth($hash): bool
{
    return password_verify(config('pass'), $hash);
}

function _p_field(): void
{
    $hash = get_weak_auth_hashed_password();
    echo "<input type='hidden' name='_p' value='$hash'>";
}
```

Add to `composer.json`:

```json
    "autoload": {
        "files": [
            "app/Http/helpers.php"
        ],
```
