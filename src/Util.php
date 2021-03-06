<?php

namespace Gogilo\Lpdm;

use Exception;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;

use function PHPUnit\Framework\throwException;

class Util
{

    /**
     * Replace the given string in the given file.
     *
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $file
     * @return void
     */
    static function replaceInFile(string $search, string $replace, string $file)
    {
        file_put_contents(
            $file,
            str_replace($search, $replace, file_get_contents($file))
        );
    }

    /**
     * Get stubs directory
     * @param String $stub
     * @return String
     */
    static function getStubFile($stub = null)
    {
        $dir = self::getBasePath("stubs");
        $path = null;

        if ($stub) {
            $path = $dir . '/' . $stub . '.stub';
            if (file_exists($path)) {
                if (PHP_OS_FAMILY == 'Windows') {
                    $path = str_replace("/", "\\", $path);
                }
                return $path;
            } else {
                throw new RuntimeException("Stab file $stub does not exists");
            }
        }

        if (PHP_OS_FAMILY == 'Windows') {
            $dir = str_replace("/", "\\", $dir);
        }

        return $dir;
    }

    /**
     * Get Vendor Namespace Name
     * @param String $vendor
     * @return String
     */
    static function getVendorNamespaceName($vendor)
    {
        return ucfirst($vendor);
    }

    /**
     * Get Package Namespace Name
     * @param String $package
     * @return String
     */
    static function getPackageNamespaceName($package)
    {
        return ucfirst($package);
    }

    /**
     * Get Vendor Name
     * @param String $vendor
     * @return String
     */
    static function getVendorName($vendor)
    {
        return strtolower($vendor);
    }

    /**
     * Get Package Name
     * @param String $package
     * @return String
     */
    static function getPackageName($package)
    {
        return strtolower($package);
    }

    /**
     * Get Namespace
     * @param String $vendor
     * @param String $package
     * @return String
     */
    static function getNamespace($vendor, $package, $suffix = null)
    {
        if ($suffix) {
            $arr = explode("\\", $suffix);
            foreach ($arr as $key => $value) {
                $arr[$key] = ucfirst($value);
            }
            $suffix = '\\' . implode('\\', $arr);
        }
        return self::getVendorNamespaceName($vendor) .  '\\' . self::getPackageNamespaceName($package) . $suffix;
    }

    /**
     * Get controllers namespace
     * @param Symfony\Component\Console\Input\InputInterface $input
     * @return String
     */
    static function getControllersNamespace(InputInterface $input)
    {
        return Util::getNamespace($input->getArgument('vendor'), $input->getArgument('name'), ($input->getOption('api') ? "\\Http\\Controllers\\Api\\V1" : "\\Http\\Controllers\\V1"));
    }

    /**
     * Get Base Path
     * @return String
     */
    static function getBasePath($path = null)
    {


        $cDir = getcwd();
        chdir(__DIR__ . '/../');

        if ($path) {
            chdir(getcwd() . (substr($path, 0, 1) != '/' ? '/' : '') . $path);
        }

        $dir = getcwd();

        chdir($cDir);

        return $dir;
    }

    /**
     * Get Skeleton Path
     * @return String
     */
    static function getSkeletonPath()
    {
        $filePath = self::getBasePath('/skeleton');
        if (PHP_OS_FAMILY == 'Windows') {
            $filePath = str_replace("/", "\\", $filePath);
        }
        return $filePath;
    }

    /**
     * Get Composer Path
     * @param String $package_path
     * @return String
     */
    static function getComposerPath(String $package_path)
    {
        return $package_path . '/composer.json';
    }

    /**
     * Get Vendor Directory
     * @param \Symfony\Component\Console\Input\InputInterface  $input
     * @return String
     */
    static function getVendorDirectory(InputInterface $input, $suffix = null)
    {
        $cDir = getcwd();
        $path = getcwd();

        $vendor = strtolower($input->getArgument('vendor'));

        if ($input->getOption('path')) {
            if (substr($input->getOption('path'), 0, 2) == './' || substr($input->getOption('path'), 0, 1) != '/') {
                $path = getcwd() . '/' . ltrim(ltrim($input->getOption('path'), '.'), '/');
            } else {
                $path = $input->getOption('path');
            }
            if (!file_exists($path)) {
                throw new RuntimeException("Path $path not found");
            }
        }

        $path = rtrim($path, "/");

        $vendorDir = ($path ? "$path/" : "./") . (file_exists("$path/artisan") ? 'packages/' : '') . ($vendor !== '.' ?   $vendor : '');

        if (!is_null($suffix) && !empty($suffix)) {
            $suffix = ltrim(ltrim($suffix, "\\"), '/');
            $vendorDir .= "/" . $suffix;
        }

        if (PHP_OS_FAMILY == 'Windows') {
            $vendorDir = str_replace("/", "\\", $vendorDir);
        }

        // if (!file_exists($vendorDir)) {
        //     mkdir($vendorDir);
        // }

        chdir($cDir);

        return $vendorDir;
    }

    /**
     * Get Package Directory
     * @param \Symfony\Component\Console\Input\InputInterface  $input
     * @return String
     */
    static function getPackageDirectory(InputInterface $input, $suffix = null)
    {
        $packageDir = self::getVendorDirectory($input);

        $packageDir = rtrim(rtrim($packageDir, "/"), "\\");

        $packageDir  .= "/" . strtolower($input->getArgument('name'));

        if ($suffix) {
            $suffix = ltrim(ltrim($suffix, "/"), "\\");
            $packageDir .= "/" . $suffix;
        }

        if (PHP_OS_FAMILY == 'Windows') {
            $packageDir = str_replace("/", "\\", $packageDir);
        }

        // if (!file_exists($packageDir)) {
        //     mkdir($packageDir);
        // }

        return $packageDir;
    }

    /**
     * Get package path
     * @param String $suffix
     * @return String
     */
    static function getControllerPath(String $path = null)
    {
        return self::getPackagePath('src/Http/Controllers' . ($path ? '/' . ltrim($path, '/') : ''));
    }

    /**
     * Get package path
     * @param String $suffix
     * @return String
     */
    static function getPackagePath(String $suffix = null)
    {
        $cDir = getcwd();

        if ($suffix) {
            if (substr($suffix, 0, 1) == '/') {
                chdir($suffix);
            } else {
                $P = getcwd() . '/' . $suffix;
                chdir($P);
            }
        }

        $path = getcwd();
        chdir($cDir);

        return $path;
    }

    /**
     * Get Base namespace
     * @param \Symfony\Component\Console\Input\InputInterface  $input
     * @param String $suffix
     * @return String
     */
    static function getBaseNamespace(InputInterface $input, String $suffix = null)
    {
        $path = self::getPackagePath($input->getOption('path'));
        $composerPath = $path . '/composer.json';
        $namespace = '';

        if (file_exists($composerPath)) {
            $composerJson = json_decode(file_get_contents($composerPath));
            foreach ($composerJson->autoload->{'psr-4'} as $key => $value) {
                $namespace = rtrim($key, '\\');
                if ($suffix) {
                    $namespace .= '\\' . ltrim($suffix, '\\');
                }
            }
        } else {
            throw new Exception("This does not seem to be a valid laravel package");
        }
        return $namespace;
    }
}
