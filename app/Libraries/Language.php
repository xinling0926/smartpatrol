<?php

namespace App\Libraries;

use CodeIgniter\Language\Language as BaseLanguage;

/**
 * Extended Language class with CI3 compatibility
 *
 * This class extends CI4's Language class to support CI3-style keys without file prefix.
 * If no file prefix is found, it will search through common language files.
 */
class Language extends BaseLanguage
{
    /**
     * Common language files to search when no prefix is provided
     */
    protected array $searchFiles = [
        'Auth', 'Globe', 'Home', 'Enterprise', 'Form', 'Device', 'Repair',
        'Notice', 'Jobtitle', 'Missing', 'Photograph', 'Webapi', 'Account',
        'OperationLog', 'Department', 'Role', 'SystemSetting', 'QueryReport',
        'Rawdata', 'Approve', 'ApproveSetting', 'RepairFrom', 'RepairTo',
        'AnnualCheckup', 'DeviceMessage', 'DeviceLog', 'FormItem', 'GenerateReport',
        'QueryReportItem', 'Model'
    ];

    /**
     * Parses the language string for a file, loads the file, if necessary,
     * getting the line.
     *
     * @param array<int|string, string> $args
     *
     * @return array<string, string>|string
     */
    public function getLine(string $line, array $args = [])
    {
        // If the line contains a dot, use CI4's standard behavior
        if (strpos($line, '.') !== false) {
            return parent::getLine($line, $args);
        }

        // CI3 compatibility: search through common language files
        foreach ($this->searchFiles as $file) {
            $result = parent::getLine("{$file}.{$line}", $args);
            // If the result is different from the key, we found a translation
            if ($result !== "{$file}.{$line}") {
                return $result;
            }
        }

        // If no translation found, return the original key
        return $line;
    }
}
