<?php

namespace Valet\Drivers\Specific;

use Valet\Drivers\BasicValetDriver;

class ViteValetDriver extends BasicValetDriver
{
    /**
     * Determine if the driver serves the request.
     */
    public function serves(string $sitePath, string $siteName, string $uri): bool
    {
        return is_dir($sitePath.'/dist');
    }

    /**
     * Determine if the incoming request is for a static file.
     */
    public function isStaticFile(string $sitePath, string $siteName, string $uri)/* : string|false */
    {
        $staticFilePath = $sitePath.'/dist'.rtrim($uri, '/');

        if ($this->isActualFile($staticFilePath)) {
            return $staticFilePath;
        }

        // Check if it's a directory with an index.html file
        if (is_dir($staticFilePath) && file_exists($staticFilePath.'/index.html')) {
            return $staticFilePath.'/index.html';
        }

        return false;
    }

    /**
     * Get the fully resolved path to the application's front controller.
     */
    public function frontControllerPath(string $sitePath, string $siteName, string $uri): ?string
    {
        $_SERVER['PHP_SELF'] = $uri;
        $_SERVER['SERVER_ADDR'] = $_SERVER['SERVER_ADDR'] ?? '127.0.0.1';
        $_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'];

        $distPath = $sitePath.'/dist';
        $uri = rtrim($uri, '/');

        $candidates = [
            $distPath.$uri,
            $distPath.$uri.'/index.html',
            $distPath.'/index.html',
        ];

        foreach ($candidates as $candidate) {
            if ($this->isActualFile($candidate)) {
                $_SERVER['SCRIPT_FILENAME'] = $candidate;
                $_SERVER['SCRIPT_NAME'] = str_replace($distPath, '', $candidate);
                $_SERVER['DOCUMENT_ROOT'] = $distPath;

                return $candidate;
            }
        }

        // For SPA applications, fallback to index.html for client-side routing
        if (file_exists($distPath.'/index.html')) {
            $_SERVER['SCRIPT_FILENAME'] = $distPath.'/index.html';
            $_SERVER['SCRIPT_NAME'] = '/index.html';
            $_SERVER['DOCUMENT_ROOT'] = $distPath;

            return $distPath.'/index.html';
        }

        return null;
    }
}
