<?php

namespace App\Libraries;

/**
 * jQuery File Upload Plugin PHP Class
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
class UploadHandler
{
    protected array $options;
    protected array $response = [];

    // PHP File Upload error message codes:
    protected array $error_messages = [
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk',
        8 => 'A PHP extension stopped the file upload',
        'post_max_size' => 'The uploaded file exceeds the post_max_size directive in php.ini',
        'max_file_size' => 'File is too big',
        'min_file_size' => 'File is too small',
        'accept_file_types' => 'Filetype not allowed',
        'max_number_of_files' => 'Maximum number of files exceeded',
        'max_width' => 'Image exceeds maximum width',
        'min_width' => 'Image requires a minimum width',
        'max_height' => 'Image exceeds maximum height',
        'min_height' => 'Image requires a minimum height',
        'abort' => 'File upload aborted',
        'image_resize' => 'Failed to resize image'
    ];

    protected array $image_objects = [];

    public function __construct(?array $options = null, bool $initialize = true, ?array $error_messages = null)
    {
        $this->response = [];
        $this->options = [
            'script_url' => $this->get_full_url() . '/' . basename($this->get_server_var('SCRIPT_NAME')),
            'upload_dir' => dirname($this->get_server_var('SCRIPT_FILENAME')) . '/files/',
            'upload_url' => $this->get_full_url() . '/files/',
            'input_stream' => 'php://input',
            'user_dirs' => false,
            'mkdir_mode' => 0755,
            'param_name' => 'files',
            'delete_type' => 'DELETE',
            'access_control_allow_origin' => '*',
            'access_control_allow_credentials' => false,
            'access_control_allow_methods' => [
                'OPTIONS', 'HEAD', 'GET', 'POST', 'PUT', 'PATCH', 'DELETE'
            ],
            'access_control_allow_headers' => [
                'Content-Type', 'Content-Range', 'Content-Disposition'
            ],
            'redirect_allow_target' => '/^' . preg_quote(
                parse_url($this->get_server_var('HTTP_REFERER'), PHP_URL_SCHEME)
                . '://'
                . parse_url($this->get_server_var('HTTP_REFERER'), PHP_URL_HOST)
                . '/',
                '/'
            ) . '/',
            'download_via_php' => false,
            'readfile_chunk_size' => 10 * 1024 * 1024,
            'inline_file_types' => '/\.(gif|jpe?g|png)$/i',
            'accept_file_types' => '/.+$/i',
            'max_file_size' => null,
            'min_file_size' => 1,
            'max_number_of_files' => null,
            'image_file_types' => '/\.(gif|jpe?g|png)$/i',
            'correct_image_extensions' => false,
            'max_width' => null,
            'max_height' => null,
            'min_width' => 1,
            'min_height' => 1,
            'discard_aborted_uploads' => true,
            'image_library' => 1,
            'convert_bin' => 'convert',
            'identify_bin' => 'identify',
            'image_versions' => [
                '' => ['auto_orient' => true]
            ],
            'print_response' => true
        ];

        if ($options) {
            $this->options = $options + $this->options;
        }
        if ($error_messages) {
            $this->error_messages = $error_messages + $this->error_messages;
        }
        if ($initialize) {
            $this->initialize();
        }
    }

    protected function initialize(): void
    {
        switch ($this->get_server_var('REQUEST_METHOD')) {
            case 'OPTIONS':
            case 'HEAD':
                $this->head();
                break;
            case 'GET':
                $this->get($this->options['print_response']);
                break;
            case 'PATCH':
            case 'PUT':
            case 'POST':
                $this->post($this->options['print_response']);
                break;
            case 'DELETE':
                $this->delete($this->options['print_response']);
                break;
            default:
                $this->header('HTTP/1.1 405 Method Not Allowed');
        }
    }

    protected function get_full_url(): string
    {
        $https = !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0 ||
            !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
            strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0;
        return
            ($https ? 'https://' : 'http://') .
            (!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] . '@' : '') .
            (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'] .
                ($https && $_SERVER['SERVER_PORT'] === 443 ||
                    $_SERVER['SERVER_PORT'] === 80 ? '' : ':' . $_SERVER['SERVER_PORT']))) .
            substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
    }

    protected function get_user_id(): string
    {
        @session_start();
        return session_id();
    }

    protected function get_user_path(): string
    {
        if ($this->options['user_dirs']) {
            return $this->get_user_id() . '/';
        }
        return '';
    }

    protected function get_upload_path(?string $file_name = null, ?string $version = null): string
    {
        $file_name = $file_name ?: '';
        if (empty($version)) {
            $version_path = '';
        } else {
            $version_dir = @$this->options['image_versions'][$version]['upload_dir'];
            if ($version_dir) {
                return $version_dir . $this->get_user_path() . $file_name;
            }
            $version_path = $version . '/';
        }
        return $this->options['upload_dir'] . $this->get_user_path() . $version_path . $file_name;
    }

    protected function get_query_separator(string $url): string
    {
        return strpos($url, '?') === false ? '?' : '&';
    }

    protected function get_download_url(string $file_name, ?string $version = null, bool $direct = false): string
    {
        if (!$direct && $this->options['download_via_php']) {
            $url = $this->options['script_url']
                . $this->get_query_separator($this->options['script_url'])
                . $this->get_singular_param_name()
                . '=' . rawurlencode($file_name);
            if ($version) {
                $url .= '&version=' . rawurlencode($version);
            }
            return $url . '&download=1';
        }
        if (empty($version)) {
            $version_path = '';
        } else {
            $version_url = @$this->options['image_versions'][$version]['upload_url'];
            if ($version_url) {
                return $version_url . $this->get_user_path() . rawurlencode($file_name);
            }
            $version_path = rawurlencode($version) . '/';
        }
        return $this->options['upload_url'] . $this->get_user_path() . $version_path . rawurlencode($file_name);
    }

    protected function set_additional_file_properties(object $file): void
    {
        $file->deleteUrl = $this->options['script_url']
            . $this->get_query_separator($this->options['script_url'])
            . $this->get_singular_param_name()
            . '=' . rawurlencode($file->name);
        $file->deleteType = $this->options['delete_type'];
        if ($file->deleteType !== 'DELETE') {
            $file->deleteUrl .= '&_method=DELETE';
        }
        if ($this->options['access_control_allow_credentials']) {
            $file->deleteWithCredentials = true;
        }
    }

    protected function fix_integer_overflow(int|float $size): int|float
    {
        if ($size < 0) {
            $size += 2.0 * (PHP_INT_MAX + 1);
        }
        return $size;
    }

    protected function get_file_size(string $file_path, bool $clear_stat_cache = false): int|float
    {
        if ($clear_stat_cache) {
            clearstatcache(true, $file_path);
        }
        return $this->fix_integer_overflow(filesize($file_path));
    }

    protected function is_valid_file_object(string $file_name): bool
    {
        $file_path = $this->get_upload_path($file_name);
        if (is_file($file_path) && $file_name[0] !== '.') {
            return true;
        }
        return false;
    }

    protected function get_file_object(string $file_name): ?object
    {
        if ($this->is_valid_file_object($file_name)) {
            $file = new \stdClass();
            $file->name = $file_name;
            $file->size = $this->get_file_size($this->get_upload_path($file_name));
            $file->url = $this->get_download_url($file->name);
            foreach ($this->options['image_versions'] as $version => $options) {
                if (!empty($version)) {
                    if (is_file($this->get_upload_path($file_name, $version))) {
                        $file->{$version . 'Url'} = $this->get_download_url($file->name, $version);
                    }
                }
            }
            $this->set_additional_file_properties($file);
            return $file;
        }
        return null;
    }

    protected function get_file_objects(string $iteration_method = 'get_file_object'): array
    {
        $upload_dir = $this->get_upload_path();
        if (!is_dir($upload_dir)) {
            return [];
        }
        return array_values(array_filter(array_map(
            [$this, $iteration_method],
            scandir($upload_dir)
        )));
    }

    protected function count_file_objects(): int
    {
        return count($this->get_file_objects('is_valid_file_object'));
    }

    protected function get_error_message(int|string $error): string
    {
        return $this->error_messages[$error] ?? (string)$error;
    }

    public function get_config_bytes(string $val): int|float
    {
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        $val = (int)$val;
        switch ($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        return $this->fix_integer_overflow($val);
    }

    protected function validate(?string $uploaded_file, object $file, ?int $error, ?int $index): bool
    {
        if ($error) {
            $file->error = $this->get_error_message($error);
            return false;
        }
        $content_length = $this->fix_integer_overflow((int)$this->get_server_var('CONTENT_LENGTH'));
        $post_max_size = $this->get_config_bytes(ini_get('post_max_size'));
        if ($post_max_size && ($content_length > $post_max_size)) {
            $file->error = $this->get_error_message('post_max_size');
            return false;
        }
        if (!preg_match($this->options['accept_file_types'], $file->name)) {
            $file->error = $this->get_error_message('accept_file_types');
            return false;
        }
        if ($uploaded_file && is_uploaded_file($uploaded_file)) {
            $file_size = $this->get_file_size($uploaded_file);
        } else {
            $file_size = $content_length;
        }
        if ($this->options['max_file_size'] && (
                $file_size > $this->options['max_file_size'] ||
                $file->size > $this->options['max_file_size'])
        ) {
            $file->error = $this->get_error_message('max_file_size');
            return false;
        }
        if ($this->options['min_file_size'] && $file_size < $this->options['min_file_size']) {
            $file->error = $this->get_error_message('min_file_size');
            return false;
        }
        if (is_int($this->options['max_number_of_files']) &&
            ($this->count_file_objects() >= $this->options['max_number_of_files']) &&
            !is_file($this->get_upload_path($file->name))
        ) {
            $file->error = $this->get_error_message('max_number_of_files');
            return false;
        }
        return true;
    }

    protected function upcount_name_callback(array $matches): string
    {
        $index = isset($matches[1]) ? ((int)$matches[1]) + 1 : 1;
        $ext = $matches[2] ?? '';
        return ' (' . $index . ')' . $ext;
    }

    protected function upcount_name(string $name): string
    {
        return preg_replace_callback(
            '/(?:(?: \(([\d]+)\))?(\.[^.]+))?$/',
            [$this, 'upcount_name_callback'],
            $name,
            1
        );
    }

    protected function get_unique_filename(string $file_path, string $name, int|float $size, ?string $type, ?int $error, ?int $index, ?array $content_range): string
    {
        while (is_dir($this->get_upload_path($name))) {
            $name = $this->upcount_name($name);
        }
        $uploaded_bytes = $this->fix_integer_overflow((int)($content_range[1] ?? 0));
        while (is_file($this->get_upload_path($name))) {
            if ($uploaded_bytes === $this->get_file_size($this->get_upload_path($name))) {
                break;
            }
            $name = $this->upcount_name($name);
        }
        return $name;
    }

    protected function trim_file_name(string $file_path, string $name, int|float $size, ?string $type, ?int $error, ?int $index, ?array $content_range): string
    {
        $name = trim(basename(stripslashes($name)), ".\x00..\x20");
        if (!$name) {
            $name = str_replace('.', '-', (string)microtime(true));
        }
        return $name;
    }

    protected function get_file_name(string $file_path, string $name, int|float $size, ?string $type, ?int $error, ?int $index, ?array $content_range): string
    {
        $name = $this->trim_file_name($file_path, $name, $size, $type, $error, $index, $content_range);
        return $this->get_unique_filename($file_path, $name, $size, $type, $error, $index, $content_range);
    }

    protected function handle_file_upload(?string $uploaded_file, ?string $name, int|float $size, ?string $type, ?int $error, ?int $index = null, ?array $content_range = null): object
    {
        $file = new \stdClass();
        $file->name = $this->get_file_name($uploaded_file ?? '', $name ?? '', $size, $type, $error, $index, $content_range);
        $file->size = $this->fix_integer_overflow((int)$size);
        $file->type = $type;

        if ($this->validate($uploaded_file, $file, $error, $index)) {
            $this->handle_form_data($file, $index);
            $upload_dir = $this->get_upload_path();
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, $this->options['mkdir_mode'], true);
            }
            $file_path = $this->get_upload_path($file->name);
            $append_file = $content_range && is_file($file_path) && $file->size > $this->get_file_size($file_path);

            if ($uploaded_file && is_uploaded_file($uploaded_file)) {
                if ($append_file) {
                    file_put_contents($file_path, fopen($uploaded_file, 'r'), FILE_APPEND);
                } else {
                    move_uploaded_file($uploaded_file, $file_path);
                }
            } else {
                file_put_contents($file_path, fopen($this->options['input_stream'], 'r'), $append_file ? FILE_APPEND : 0);
            }

            $file_size = $this->get_file_size($file_path, (bool)$append_file);
            if ($file_size === $file->size) {
                $file->url = $this->get_download_url($file->name);
            } else {
                $file->size = $file_size;
                if (!$content_range && $this->options['discard_aborted_uploads']) {
                    unlink($file_path);
                    $file->error = $this->get_error_message('abort');
                }
            }
            $this->set_additional_file_properties($file);
        }
        return $file;
    }

    protected function readfile(string $file_path): int|false
    {
        $file_size = $this->get_file_size($file_path);
        $chunk_size = $this->options['readfile_chunk_size'];
        if ($chunk_size && $file_size > $chunk_size) {
            $handle = fopen($file_path, 'rb');
            while (!feof($handle)) {
                echo fread($handle, $chunk_size);
                @ob_flush();
                @flush();
            }
            fclose($handle);
            return (int)$file_size;
        }
        return \readfile($file_path);
    }

    protected function body(string $str): void
    {
        echo $str;
    }

    protected function header(string $str): void
    {
        header($str);
    }

    protected function get_upload_data(string $id): ?array
    {
        return $_FILES[$id] ?? null;
    }

    protected function get_post_param(string $id): mixed
    {
        return $_POST[$id] ?? null;
    }

    protected function get_query_param(string $id): mixed
    {
        return $_GET[$id] ?? null;
    }

    protected function get_server_var(string $id): ?string
    {
        return $_SERVER[$id] ?? null;
    }

    protected function handle_form_data(object $file, ?int $index): void
    {
        // Handle form data, e.g. $_POST['description'][$index]
    }

    protected function get_version_param(): string
    {
        return basename(stripslashes($this->get_query_param('version') ?? ''));
    }

    protected function get_singular_param_name(): string
    {
        return substr($this->options['param_name'], 0, -1);
    }

    protected function get_file_name_param(): string
    {
        $name = $this->get_singular_param_name();
        return basename(stripslashes($this->get_query_param($name) ?? ''));
    }

    protected function send_content_type_header(): void
    {
        $this->header('Vary: Accept');
        if (strpos($this->get_server_var('HTTP_ACCEPT') ?? '', 'application/json') !== false) {
            $this->header('Content-type: application/json');
        } else {
            $this->header('Content-type: text/plain');
        }
    }

    protected function send_access_control_headers(): void
    {
        $this->header('Access-Control-Allow-Origin: ' . $this->options['access_control_allow_origin']);
        $this->header('Access-Control-Allow-Credentials: ' . ($this->options['access_control_allow_credentials'] ? 'true' : 'false'));
        $this->header('Access-Control-Allow-Methods: ' . implode(', ', $this->options['access_control_allow_methods']));
        $this->header('Access-Control-Allow-Headers: ' . implode(', ', $this->options['access_control_allow_headers']));
    }

    public function generate_response(array $content, bool $print_response = true): array
    {
        $this->response = $content;
        if ($print_response) {
            $json = json_encode($content);
            $redirect = stripslashes($this->get_post_param('redirect') ?? '');
            if ($redirect && preg_match($this->options['redirect_allow_target'], $redirect)) {
                $this->header('Location: ' . sprintf($redirect, rawurlencode($json)));
                return $content;
            }
            $this->head();
            if ($this->get_server_var('HTTP_CONTENT_RANGE')) {
                $files = $content[$this->options['param_name']] ?? null;
                if ($files && is_array($files) && is_object($files[0]) && $files[0]->size) {
                    $this->header('Range: 0-' . ($this->fix_integer_overflow((int)$files[0]->size) - 1));
                }
            }
            $this->body($json);
        }
        return $content;
    }

    public function get_response(): array
    {
        return $this->response;
    }

    public function head(): void
    {
        $this->header('Pragma: no-cache');
        $this->header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->header('Content-Disposition: inline; filename="files.json"');
        $this->header('X-Content-Type-Options: nosniff');
        if ($this->options['access_control_allow_origin']) {
            $this->send_access_control_headers();
        }
        $this->send_content_type_header();
    }

    public function get(bool $print_response = true): array
    {
        if ($print_response && $this->get_query_param('download')) {
            $this->download();
            return [];
        }
        $file_name = $this->get_file_name_param();
        if ($file_name) {
            $response = [$this->get_singular_param_name() => $this->get_file_object($file_name)];
        } else {
            $response = [$this->options['param_name'] => $this->get_file_objects()];
        }
        return $this->generate_response($response, $print_response);
    }

    public function post(bool $print_response = true): array
    {
        if ($this->get_query_param('_method') === 'DELETE') {
            return $this->delete($print_response);
        }
        $upload = $this->get_upload_data($this->options['param_name']);
        $content_disposition_header = $this->get_server_var('HTTP_CONTENT_DISPOSITION');
        $file_name = $content_disposition_header ?
            rawurldecode(preg_replace('/(^[^"]+")|("$)/', '', $content_disposition_header)) : null;
        $content_range_header = $this->get_server_var('HTTP_CONTENT_RANGE');
        $content_range = $content_range_header ? preg_split('/[^0-9]+/', $content_range_header) : null;
        $size = $content_range ? $content_range[3] : null;
        $files = [];

        if ($upload) {
            if (is_array($upload['tmp_name'])) {
                foreach ($upload['tmp_name'] as $index => $value) {
                    $files[] = $this->handle_file_upload(
                        $upload['tmp_name'][$index],
                        $file_name ?: $upload['name'][$index],
                        $size ?: $upload['size'][$index],
                        $upload['type'][$index],
                        $upload['error'][$index],
                        $index,
                        $content_range
                    );
                }
            } else {
                $files[] = $this->handle_file_upload(
                    $upload['tmp_name'] ?? null,
                    $file_name ?: ($upload['name'] ?? null),
                    $size ?: ($upload['size'] ?? $this->get_server_var('CONTENT_LENGTH')),
                    $upload['type'] ?? $this->get_server_var('CONTENT_TYPE'),
                    $upload['error'] ?? null,
                    null,
                    $content_range
                );
            }
        }

        $response = [$this->options['param_name'] => $files];
        return $this->generate_response($response, $print_response);
    }

    public function delete(bool $print_response = true): array
    {
        $file_names = $this->get_file_names_params();
        if (empty($file_names)) {
            $file_names = [$this->get_file_name_param()];
        }
        $response = [];
        foreach ($file_names as $file_name) {
            $file_path = $this->get_upload_path($file_name);
            $success = is_file($file_path) && $file_name[0] !== '.' && unlink($file_path);
            if ($success) {
                foreach ($this->options['image_versions'] as $version => $options) {
                    if (!empty($version)) {
                        $file = $this->get_upload_path($file_name, $version);
                        if (is_file($file)) {
                            unlink($file);
                        }
                    }
                }
            }
            $response[$file_name] = $success;
        }
        return $this->generate_response($response, $print_response);
    }

    protected function download(): void
    {
        switch ($this->options['download_via_php']) {
            case 1:
                $redirect_header = null;
                break;
            case 2:
                $redirect_header = 'X-Sendfile';
                break;
            case 3:
                $redirect_header = 'X-Accel-Redirect';
                break;
            default:
                $this->header('HTTP/1.1 403 Forbidden');
                return;
        }
        $file_name = $this->get_file_name_param();
        if (!$this->is_valid_file_object($file_name)) {
            $this->header('HTTP/1.1 404 Not Found');
            return;
        }
        if ($redirect_header) {
            $this->header($redirect_header . ': ' . $this->get_download_url($file_name, $this->get_version_param(), true));
            return;
        }
        $file_path = $this->get_upload_path($file_name, $this->get_version_param());
        $this->header('X-Content-Type-Options: nosniff');
        if (!preg_match($this->options['inline_file_types'], $file_name)) {
            $this->header('Content-Type: application/octet-stream');
            $this->header('Content-Disposition: attachment; filename="' . $file_name . '"');
        } else {
            $this->header('Content-Type: ' . $this->get_file_type($file_path));
            $this->header('Content-Disposition: inline; filename="' . $file_name . '"');
        }
        $this->header('Content-Length: ' . $this->get_file_size($file_path));
        $this->header('Last-Modified: ' . gmdate('D, d M Y H:i:s T', filemtime($file_path)));
        $this->readfile($file_path);
    }

    protected function get_file_type(string $file_path): string
    {
        switch (strtolower(pathinfo($file_path, PATHINFO_EXTENSION))) {
            case 'jpeg':
            case 'jpg':
                return 'image/jpeg';
            case 'png':
                return 'image/png';
            case 'gif':
                return 'image/gif';
            default:
                return '';
        }
    }

    protected function get_file_names_params(): ?array
    {
        $params = $this->get_query_param($this->options['param_name']);
        if (!$params) {
            return null;
        }
        foreach ($params as $key => $value) {
            $params[$key] = basename(stripslashes($value));
        }
        return $params;
    }
}
