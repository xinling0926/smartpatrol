<?php

/**
 * HTML Helper - CI4 Version
 * HTML 輔助函數擴展
 */

if (!function_exists('assets_css')) {
    /**
     * 產生連結 assets CSS 檔的 HTML
     */
    function assets_css(string $css = '', string $dir = ''): void
    {
        $version = defined('VERSION') ? VERSION : '1.0.0';
        $assetsUrl = $dir ? base_url('assets/' . $dir) : base_url('assets/css');
        echo "<link href=\"{$assetsUrl}/{$css}.css?{$version}\" rel=\"stylesheet\" type=\"text/css\">";
    }
}

if (!function_exists('assets_js')) {
    /**
     * 產生連結 assets js 檔的 HTML
     */
    function assets_js(string $js = '', string $dir = ''): void
    {
        $version = defined('VERSION') ? VERSION : '1.0.0';
        $assetsUrl = $dir ? base_url('assets/' . $dir) : base_url('assets/js');
        echo "<script src=\"{$assetsUrl}/{$js}.js?{$version}\" type=\"text/javascript\"></script>";
    }
}

if (!function_exists('assets_img')) {
    /**
     * 產生連結 assets images 檔的 HTML
     */
    function assets_img(string $src = '', string|array $attributes = '', string $alt = ''): void
    {
        $assetsUrl = base_url('assets/img');
        $image = "<img src='{$assetsUrl}/{$src}' alt='{$alt}' ";
        $image .= stringify_attributes($attributes);
        $image .= '>';
        echo $image;
    }
}
