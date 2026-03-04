#include "code/hello.c"
#include "code/icon.png.h"
#include "vendor/tinyexpr.h"

#include <stdio.h>
#include <stdint.h>

void print_as_ascii_art() {
    for (int y = 0; y < icon_png.height; y++) {
        for (int x = 0; x < icon_png.width; x++) {
            uint32_t hex = icon_png.data[y * icon_png.width + x];

            uint8_t r = (hex >> 16) & 0xFF;
            uint8_t g = (hex >> 8) & 0xFF;
            uint8_t b = hex & 0xFF;

            printf("\033[48;2;%d;%d;%dm  ", r, g, b);
        }
        printf("\033[0m\n");
    }
}

int main() {
  greet();
  printf("\n");
  const char *expr = "sqrt(25) + 10";
  double result = te_interp(expr, 0);
  printf("Result of '%s' is: %f\n", expr, result);

  print_as_ascii_art();

  return 0;
}
