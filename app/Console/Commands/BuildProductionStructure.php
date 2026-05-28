<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BuildProductionStructure extends Command
{
    protected $signature = 'build:production-structure';
    protected $description = 'Create production build structure with Laravel and public_html folders';

    public function handle(): int
    {
        $this->info('Building production structure...');

        $distPath = base_path('dist');
        $laravelPath = $distPath . '/laravel';
        $publicHtmlPath = $distPath . '/public_html';

        File::makeDirectory($laravelPath, 0755, true, true);
        File::makeDirectory($publicHtmlPath, 0755, true, true);

        $this->info('Created dist directories');

        $this->copyLaravelFiles($laravelPath);
        $this->info('Copied Laravel files');

        $this->copyPublicFiles($publicHtmlPath);
        $this->info('Copied public files to public_html');

        $this->createLaravelPublicBuild($laravelPath, $publicHtmlPath);
        $this->info('Created Laravel public/build directory');

        $this->createBasicPublicStructure($laravelPath);
        $this->info('Created basic public structure in Laravel');

        $this->modifyIndexPhpForProduction($publicHtmlPath);
        $this->info('Modified index.php for production structure');

        $this->createZipArchive($distPath);
        $this->info('Created production.zip');

        $this->info('✅ Production build completed successfully!');
        $this->info('📁 Files created in: ' . $distPath);
        $this->info('📦 Production archive: ' . $distPath . '/production.zip');

        return self::SUCCESS;
    }

    private function copyLaravelFiles(string $destination): void
    {
        $topLevelExcludes = [
            'node_modules',
            '.git',
            'tests',
            'public',
            'dist',
        ];

        $storageSubExcludes = [
            'logs',
            'framework' . DIRECTORY_SEPARATOR . 'cache',
            'framework' . DIRECTORY_SEPARATOR . 'sessions',
            'framework' . DIRECTORY_SEPARATOR . 'views',
        ];

        $items = scandir(base_path());
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $srcPath = base_path($item);

            if (in_array($item, $topLevelExcludes)) {
                continue;
            }

            $destPath = $destination . DIRECTORY_SEPARATOR . $item;

            if ($item === 'storage') {
                File::makeDirectory($destPath, 0755, true, true);
                $this->copyDirectoryWithExclusions($srcPath, $destPath, $storageSubExcludes);
            } elseif (is_dir($srcPath)) {
                File::copyDirectory($srcPath, $destPath);
            } elseif (is_file($srcPath)) {
                $fileBasename = basename($item);
                if (str_ends_with($fileBasename, '.md') || str_ends_with($fileBasename, '.log')) {
                    continue;
                }
                if (in_array($fileBasename, ['.env', '.gitignore', '.gitattributes', '.editorconfig', 'composer.lock.backup'])) {
                    continue;
                }
                File::copy($srcPath, $destPath);
            }
        }
    }

    private function copyDirectoryWithExclusions(string $source, string $destination, array $excludeRelativePaths): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $relativePath = $iterator->getSubPathname();

            $shouldSkip = false;
            foreach ($excludeRelativePaths as $excludePath) {
                if (str_starts_with($relativePath, $excludePath)) {
                    $shouldSkip = true;
                    break;
                }
            }

            if ($shouldSkip) {
                if ($item->isDir()) {
                    $iterator->setMaxDepth(0);
                }
                continue;
            }

            $destPath = $destination . DIRECTORY_SEPARATOR . $relativePath;

            if ($item->isDir()) {
                File::makeDirectory($destPath, 0755, true, true);
            } else {
                File::copy($item->getRealPath(), $destPath);
            }
        }
    }

    private function copyPublicFiles(string $destination): void
    {
        $publicPath = base_path('public');
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($publicPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $relativePath = $iterator->getSubPathname();

            if (str_starts_with($relativePath, 'build' . DIRECTORY_SEPARATOR)) {
                continue;
            }

            $destPath = $destination . DIRECTORY_SEPARATOR . $relativePath;

            if ($item->isDir()) {
                File::makeDirectory($destPath, 0755, true, true);
            } else {
                File::copy($item->getRealPath(), $destPath);
            }
        }
    }

    private function createLaravelPublicBuild(string $laravelPath, string $publicHtmlPath): void
    {
        $buildSource = base_path('public' . DIRECTORY_SEPARATOR . 'build');
        $buildDest = $laravelPath . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'build';

        if (File::exists($buildSource)) {
            File::makeDirectory(dirname($buildDest), 0755, true, true);
            File::copyDirectory($buildSource, $buildDest);
        }

        $buildPublicDest = $publicHtmlPath . DIRECTORY_SEPARATOR . 'build';
        if (File::exists($buildSource)) {
            File::makeDirectory(dirname($buildPublicDest), 0755, true, true);
            File::copyDirectory($buildSource, $buildPublicDest);
        }
    }

    private function createBasicPublicStructure(string $laravelPath): void
    {
        $publicDir = $laravelPath . DIRECTORY_SEPARATOR . 'public';

        if (!File::exists($publicDir)) {
            File::makeDirectory($publicDir, 0755, true, true);
        }

        $htaccessContent = '<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>';

        File::put($laravelPath . DIRECTORY_SEPARATOR . '.htaccess', $htaccessContent);
    }

    private function modifyIndexPhpForProduction(string $publicHtmlPath): void
    {
        $indexPath = $publicHtmlPath . DIRECTORY_SEPARATOR . 'index.php';

        if (!File::exists($indexPath)) {
            $sourceIndex = base_path('public' . DIRECTORY_SEPARATOR . 'index.php');
            if (File::exists($sourceIndex)) {
                File::copy($sourceIndex, $indexPath);
            } else {
                return;
            }
        }

        $content = File::get($indexPath);
        $content = str_replace(
            "require __DIR__.'/../vendor/autoload.php';",
            "require __DIR__.'/../laravel/vendor/autoload.php';",
            $content
        );
        $content = str_replace(
            "\$app = require_once __DIR__.'/../bootstrap/app.php';",
            "\$app = require_once __DIR__.'/../laravel/bootstrap/app.php';",
            $content
        );

        File::put($indexPath, $content);
    }

    private function createZipArchive(string $distPath): void
    {
        $zipPath = $distPath . DIRECTORY_SEPARATOR . 'production.zip';

        if (class_exists(\ZipArchive::class)) {
            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($distPath, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::SELF_FIRST
                );

                foreach ($iterator as $item) {
                    $relativePath = $iterator->getSubPathname();

                    if ($relativePath === 'production.zip') {
                        continue;
                    }

                    if ($item->isDir()) {
                        $zip->addEmptyDir($relativePath);
                    } else {
                        $zip->addFile($item->getRealPath(), $relativePath);
                    }
                }

                $zip->close();
            } else {
                $this->warn('Could not create zip archive. ZipArchive not available or permission denied.');
            }
        } else {
            $this->warn('ZipArchive extension not available. Skipping zip creation.');
        }
    }
}
