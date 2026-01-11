<?php

/**
 * Language Helper - CI4 Compatibility Layer for CI3 style lang() calls
 *
 * This helper overrides CI4's lang() function to support CI3-style keys without file prefix.
 * If no file prefix is found, it will search through common language files.
 */

if (!function_exists('lang')) {
    /**
     * A convenience method to translate a string or array of them and format
     * the result with the intl extension's MessageFormatter.
     *
     * @param array|string $line       Language line key or array of keys
     * @param list<mixed>  $args       Arguments for message formatting
     * @param string|null  $locale     Locale to use (defaults to current)
     *
     * @return array<string, string>|string
     */
    function lang(array|string $line, array $args = [], ?string $locale = null): array|string
    {
        // If the line contains a dot, use CI4's standard behavior
        if (is_string($line) && strpos($line, '.') !== false) {
            return service('language', $locale)->getLine($line, $args);
        }

        // CI3 compatibility: search through common language files
        $searchFiles = [
            'Globe', 'Auth', 'Home', 'Enterprise', 'Form', 'FormItem', 'Device', 'DeviceLog', 'DeviceMessage',
            'Repair', 'RepairFrom', 'RepairTo', 'Notice', 'Jobtitle', 'Missing', 'Photograph', 'Webapi',
            'Account', 'OperationLog', 'Department', 'Role', 'SystemSetting', 'AnnualCheckup',
            'Approve', 'ApproveSetting', 'EmmaLink', 'GenerateReport', 'Migrate', 'Model',
            'QueryReport', 'QueryReportItem', 'Rawdata'
        ];

        $language = service('language', $locale);

        if (is_string($line)) {
            // Try each language file
            foreach ($searchFiles as $file) {
                $result = $language->getLine("{$file}.{$line}", $args);
                // If the result is different from the key, we found a translation
                if ($result !== "{$file}.{$line}") {
                    return $result;
                }
            }
            // If no translation found, return the original key
            return $line;
        }

        // Handle array of lines
        if (is_array($line)) {
            $result = [];
            foreach ($line as $key) {
                $result[$key] = lang($key, $args, $locale);
            }
            return $result;
        }

        return $line;
    }
}
