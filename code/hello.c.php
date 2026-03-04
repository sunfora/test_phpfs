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
