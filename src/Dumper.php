<?php declare(strict_types=1);

namespace Glider88;

class Dumper {
    public static function scalar(mixed $arg): string
    {
        return match (true) {
            $arg === ''     => '\'\'',
            $arg === null   => 'null',
            $arg === true   => 'true',
            $arg === false  => 'false',
            is_string($arg) => "'" . $arg . "'",
            is_scalar($arg) => (string) $arg,
            default => throw new \InvalidArgumentException(
                "printlnScalar with not scalar: " . var_export($arg, true)
            ),
        };
    }

    public static function is_flat($args): bool
    {
        if ($args === null || is_scalar($args)) {
            return true;
        }

        if (is_array($args)) {
            if (! array_is_list($args)) {
                return false;
            }

            foreach ($args as $arg) {
                if (! self::is_flat($arg)) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    public static function php_flat_value($args): string
    {
        if (is_array($args)) {
            return '[' . implode(', ', array_map(static fn($arg) => self::php_flat_value($arg), $args)) . ']';
        }

        return self::scalar($args);
    }

    public static function intend(int $depth): string
    {
        return implode(array_fill(0, $depth, '  '));
    }

    public static function php_value(mixed $arg, int $depth = 0, string $postfix = ''): string
    {
        if (is_array($arg)) {
            if (self::is_flat($arg)) {
                return self::php_flat_value($arg) . $postfix . PHP_EOL;
            }

            $result = '[' . PHP_EOL;
            $next = '  ';
            foreach ($arg as $key => $value) {
                $result .= self::intend($depth) . $next . self::scalar($key) . ' => ' . self::php_value($value,  $depth + 1, ',');
            }
            $result .= self::intend($depth) . '],' . PHP_EOL;

            return $result;
        }

        if (is_object($arg)) {
            throw new \InvalidArgumentException('Not implemented yet');
        }

        return self::scalar($arg) . $postfix . PHP_EOL;
    }

    public static function d(mixed $data): void
    {
        $result = self::php_value($data);
        if (str_ends_with($result, ',' . PHP_EOL)) {
            $result[-2] = ';';
        } else {
            $result[-1] = ';';
            $result .= PHP_EOL;
        }

        echo $result;
    }

    public static function dd(mixed $data)
    {
        self::d($data);die;
    }
}
