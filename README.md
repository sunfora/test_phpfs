## motivation

<img width="375" height="867" alt="image" src="https://github.com/user-attachments/assets/c30a09af-5dfd-4db5-9841-4b334d14433d" />


The idea to use php as a preprocessor for C comes from the simple fact that php is a widely used and known language, yet by default it is a template language.
Basically php provides quite an abundance of ways you can represent a string and print it to the console / web page / file.

Yet, mostly this power is hidden from us mortals and it is used only for the web.

### An example of php as a quick no build solution
In one project of mine I have used php to pack dynamically javascript web components.
If you don't know, typically you would do something like this: http template, css and .js class definition.

By default W3C wants it from us in a single .js file. Html here is nothing but a string.
Which might be slightly annoying if your html is pretty large and ugly.

So instead of embeding these parts in javascript file or having a complex build pipeline with build scripts. 
I have... used php to assemble the parts on the fly and spit them out.
Achieving this ideal workflow where I just do ctrl + f5 in the browser and it just works.

### The same can be applied here in C
Just think about it. We can embed weird stuff when compiler reads a file and it will never be stale again.
No build scripts, no file watchers or other nonsense. Just a simple unity build + generation on demand when we need to embed some weird stuff.
Instead of having scripts which you must invoke, this thing is invoked by your system automatically.

Also, you can manage dependecies here, for example nothing really stops you from writing github.php. Which will declare repositories you are aware of.
And then you would just fetch individual files right from the github.

By having adequate scripting language at preprocessor stage we might just construct our very own simple build system in place of make / cmkake whatever nonsense. And package managers. 
If you know php you don't need to know about all of that stuff until you hit the necessety to integrate with a project which uses these build tools.
That's the idea.

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

This looks perfectly fine.
Just a normal c program.

Except that nowhere vendor/tinyexpr.h actually lives.
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

Code/hello.c is not even a real c file.

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

Same with icon.png.h after I added it.

## ?!

It kinda works, but it is a little broken.
Sometimes you need to restart or wait for it to recover.

So a proper fuse driver x http server implementation is needed instead of rclone to support this madness.
The aim of the project might be to create a separate php runtime, which also happens to be mainly a FUSE driver or Windows' Filesystem Proxy (or whatever they use there).

It should read open files and list directories via some routers just like `php -S localhost`. 
And it should serve http as well, because you might want to spit the UI out to the browser or have an admin panel or reuse the same thing during development in web projects.
