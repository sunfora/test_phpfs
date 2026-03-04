## motivation

<img width="375" height="867" alt="image" src="https://github.com/user-attachments/assets/c30a09af-5dfd-4db5-9841-4b334d14433d" />


The idea to use php as a turing complete preprocessor for C
comes from the fact that php is a normal language, yet it is template language by default.

So it is kinda perfect for embeding some stuff into a template and going with it.
Yet, mostly it is used only as a language for the web.

I really like php as a glue. Yes it is pretty ugly and sometimes unintuitive.
But it is a duct tape. In one project of mine I have used php to pack dynamically javascript web components.
They consist of http template, css part and of course the class definition in a form of .js file.

By default you must put everything into .js file, as strings basically.
Which might be not so fun if your html is pretty large and ugly.

So instead of embeding these parts in javascript I have used php.

The same can be applied here in C: just think about it. We can embed weird stuff before compiler even reads a file and it never goes stale.
Instead of freaking scripts which you must invoke, this thing is invoked by your system automatically.

Also, you can manage dependecies, prepare them after you download and basically construct your own simple build system in place make / cmkake whatever nonsense.
And package managers. If you know php you don't need to know about all of that stuff until necessary and you can write whatever you like here.

Because it is a normal turing complete language which is also... imperative. And... object oriented.

## start

1. install php and rclone
2. run server and compile
```bash
bash run &
bash compile
```
3. ?? PROFIT

## main

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
(in some system's cache for speedup, but it is refetched from the internet every 60 seconds)
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

same with icon.png.h after I added it

## ?!

it kinda works, but it is a little broken
sometimes you need to restart or wait for it to recover

so a proper fuse driver x http server implementation is needed 
instead of rclone to support this madness

basically the aim of the project might be to create a separate php runtime
which also happens to be mainly a FUSE driver or Windows' Filesystem Proxy or whatever they use there

it should read open files and list directories via some routers just like 
php -S localhost... something we have now

and it should serve http as well, because you might want to spit the UI out to the browser 
and do things from out there
