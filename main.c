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
