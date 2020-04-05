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
            'msg' => $message,
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

if (!function_exists('thinkAdminPath')) {
    function thinkAdminPath()
    {
        return __DIR__ . DIRECTORY_SEPARATOR;
    }
}

if (!function_exists('__UITableBuildItemsUrl')) {
    function __UITableBuildItemsUrl($item)
    {
        $type = $item['type'] ?? 'link';
        $rowClick = [
            'type' => $type
        ];
        $vars = $item['vars'] ?? [];
        if (isset($item['url'])) {
            $rowClick['url'] = $item['url'];
        }
        if (in_array($type, ['link', 'dialog']) && isset($item['url'])) {
            $urlArgs = [];
            foreach ($vars as $var) {
                if (strpos($var, '@') !== false) {
                    $var = explode('@', $var);
                    $urlArgs[$var[1]] = "__{$var[1]}__";
                } else {
                    $urlArgs[$var] = "__{$var}__";
                }
            }
            if (is_object($item['url'])) {
                $rowClick['url'] = $item['url']->vars($urlArgs)->build();
            } elseif (strpos('http', $item['url']) === 0) {
                $rowClick['url'] = $item['url'];
            } else {
                $rowClick['url'] = url($item['url'], $urlArgs)->build();
            }
        }
        if (is_object($rowClick['url'])) {
            $rowClick['url'] = $rowClick['url']->build();
        }

        if ($vars) {
            $rowClick['vars'] = $vars;
        }
        if (isset($item['confirm'])) {
            $rowClick['confirm'] = $item['confirm'];
        }
        if (isset($item['blank'])) {
            $rowClick['blank'] = $item['blank'];
        }
        if (isset($item['blankName'])) {
            $rowClick['blankName'] = $item['blankName'];
        }
        $rowClick = json_encode($rowClick);
        return $rowClick;
    }
}