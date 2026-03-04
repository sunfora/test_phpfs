## some history

My weird idea to use php as a turing complete preprocessor for C
actually if implemented properly the idea might be promising

I really like php, because it is kinda wacky but in the end it is everything you need 
packed into one box: template engine + system utilities + http

and it is dynamic, you start a server and it basically hot reloads
you never wait for anything

In one project of mine I have used php to pack dynamically javascript web components.
They consist of http template, css part and ofcourse the class definition in a form of .js

But... having your html and css in a string is quite an unfortunate thing to do.
So instead of embeding these parts in javascript, instead I have used php.

The same can be said about C, even though it has some preprocessor quite a lot of boilerplate 
comes from the C language, especially when you need to write preprocessor boilerplate.

What if instead we would have used normal turing complete language?!
But without reinvention of the compiler and the language.

Embed an image, some simple file.

## start

1. install php and rclone
2. run server and compile
```bash
bash run &
bash compile
```
3. ?? PROFIT

## source code of main

```c
#include "code/hello.c"
#include "vendor/tinyexpr.h"

int main() {
  greet();
  printf("\n");
  const char *expr = "sqrt(25) + 10";
  double result = te_interp(expr, 0);
  printf("Result of '%s' is: %f\n", expr, result);
  return 0;
}
```

this looks perfectly fine
just a normal c program

except that nowhere vendor/tinyexpr.h actually lives
(in some system's cache for speedup, but it is refetched from the internet from time to time)
```php
<?php
$cacheFile = '/tmp/tinyexpr_c_cache';
$url = "https://raw.githubusercontent.com/codeplea/tinyexpr/refs/heads/master/tinyexpr.c";

// Refresh cache only if it's older than 60 seconds
if (!file_exists($cacheFile) || (time() - filemtime($cacheFile) > 60)) {
    $data = file_get_contents($url);
    if ($data) file_put_contents($cacheFile, $data);
}

echo "/* Cached Proxy */\n";
echo file_get_contents($cacheFile);
```

code/hello.c is not even a real c file

```php
<?php
$processUser = posix_getpwuid(posix_geteuid())['name'];
$date = date('Y-m-d H:i:s');
?>
#include <stdio.h>

long greet() {
  printf(
    "Hello from a virtual C file!" "\n"
    "<?=$processUser?> <?=$date?>" "\n"
  );
  return 0;
}
```
## ?!

though it kinda breaks if you really spam the compilation
so a proper fuse driver instead of rclone is probably needed
to support this kind of madness
