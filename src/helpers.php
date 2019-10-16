<?php

if (!function_exists('admin_base_path')) {
    /**
     * Get admin url.
     *
     * @param string $path
     *
     * @return string
     */
    function admin_base_path($path = '')
    {
        $prefix = '/'.trim(config('thinkAdmin.route.prefix'), '/');

        $prefix = ($prefix == '/') ? '' : $prefix;

        $path = trim($path, '/');

        if (is_null($path) || strlen($path) == 0) {
            return $prefix ?: '/';
        }

        return $prefix.'/'.$path;
    }
}

if (!function_exists('json_error')) {
    function json_error($message = 'eroor', $code = 500, $data = [])
    {
        return json([
            'code' => $code,
            'msg' => $message,
            'data' => $data
        ]);
    }
}

if (!function_exists('json_success')) {
    function json_success($message = 'success', $data = [])
    {
        return json([
            'code' => 200,
            'message' => $message,
            'data' => $data
        ]);
    }
}

if (!function_exists('json_return')) {
    function json_return($data)
    {
        return json([
            'code' => 200,
            'data' => $data
        ]);
    }
}