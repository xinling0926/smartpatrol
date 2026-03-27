<?php

/**
 * Assets Helper - CI4 Version
 * 資源文件輔助函數
 */

if (!function_exists('assets_js')) {
    /**
     * 輸出 JavaScript 標籤
     */
    function assets_js(string $filename, string $folder = 'js'): void
    {
        if (strpos($filename, '.js') === false && strpos($filename, ',') === false) {
            $filename .= '.js';
        }

        $path = "assets/{$folder}/{$filename}";
        $url = base_url($path);

        // 添加版本參數防止快取
        $version = '20260327';
        $url .= '?v=' . $version;

        echo '<script src="' . $url . '"></script>' . PHP_EOL;
    }
}

if (!function_exists('assets_css')) {
    /**
     * 輸出 CSS 樣式表標籤
     */
    function assets_css(string $filename, string $folder = 'css'): void
    {
        if (strpos($filename, '.css') === false) {
            $filename .= '.css';
        }

        $path = "assets/{$folder}/{$filename}";
        $url = base_url($path);

        // 添加版本參數防止快取
        $version = '20260327';
        $url .= '?v=' . $version;

        echo '<link rel="stylesheet" href="' . $url . '">' . PHP_EOL;
    }
}

if (!function_exists('assets_img')) {
    /**
     * 輸出圖片標籤
     */
    function assets_img(string $filename, array $attributes = [], string $folder = 'img'): string
    {
        $path = "assets/{$folder}/{$filename}";
        $url = base_url($path);

        $attrs = '';
        foreach ($attributes as $key => $value) {
            $attrs .= " {$key}=\"{$value}\"";
        }

        return '<img src="' . $url . '"' . $attrs . '>';
    }
}

if (!function_exists('assets_url')) {
    /**
     * 取得資源 URL
     */
    function assets_url(string $filename, string $folder = ''): string
    {
        $path = 'assets/';
        if ($folder) {
            $path .= $folder . '/';
        }
        $path .= $filename;

        return base_url($path);
    }
}
