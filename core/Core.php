<?php

class Core
{
    /**
     * @var core\base\Application
     */
    public static $app;
    /**
     * @var array
     */
    public static $aliases = ['@core' => __DIR__];

    /**
     * Registers a path alias.
     *
     * @param string $alias
     * @param string $path
     */
    public static function setAlias($alias, $path)
    {
        if (strncmp($alias, '@', 1)) {
            $alias = '@' . $alias;
        }
        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);
        if ($path !== null) {
            $path = strncmp($path, '@', 1) ?
                rtrim($path, '\\/') :
                static::getAlias($path);
            if (!isset(static::$aliases[$root])) {
                if ($pos === false) {
                    static::$aliases[$root] = $path;
                } else {
                    static::$aliases[$root] = [$alias => $path];
                }
            } elseif (is_string(static::$aliases[$root])) {
                if ($pos === false) {
                    static::$aliases[$root] = $path;
                } else {
                    static::$aliases[$root] = [
                        $alias => $path,
                        $root => static::$aliases[$root],
                    ];
                }
            } else {
                static::$aliases[$root][$alias] = $path;
                krsort(static::$aliases[$root]);
            }
        } elseif (isset(static::$aliases[$root])) {
            if (is_array(static::$aliases[$root])) {
                unset(static::$aliases[$root][$alias]);
            } elseif ($pos === false) {
                unset(static::$aliases[$root]);
            }
        }
    }

    /**
     * Translates a path alias into an actual path.
     *
     * @param string $alias
     *
     * @return bool|string
     */
    public static function getAlias($alias)
    {
        if (strncmp($alias, '@', 1)) {
            return $alias;
        }

        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);

        if (isset(static::$aliases[$root])) {
            if (is_string(static::$aliases[$root])) {
                return $pos === false ?
                    static::$aliases[$root] :
                    static::$aliases[$root] . substr($alias, $pos);
            }

            foreach (static::$aliases[$root] as $name => $path) {
                if (strpos($alias . '/', $name . '/') === 0) {
                    return $path . substr($alias, strlen($name));
                }
            }
        }

        return false;
    }

    /**
     * Class autoloader.
     *
     * @param string $className
     */
    public static function autoload($className)
    {
        if (strpos($className, '\\') !== false) {
            $alias = '@' . str_replace('\\', '/', $className) . '.php';
            $classFile = static::getAlias($alias);
            if ($classFile === false || !is_file($classFile)) {
                return;
            }
        } else {
            return;
        }

        include $classFile;
    }
}

spl_autoload_register(['Core', 'autoload'], true, true);