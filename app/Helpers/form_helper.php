<?php

/**
 * Form Helper - CI4 Version
 * 表單輔助函數擴展
 */

if (!function_exists('form_hidden')) {
    /**
     * 覆寫 CI4 的 form_hidden()
     * 自動將值轉為字串，避免 TypeError
     */
    function form_hidden(string $name, $value = '', bool $recursing = false): string
    {
        if (is_array($value)) {
            $hidden = '';
            foreach ($value as $k => $v) {
                $hidden .= form_hidden($name . '[' . $k . ']', $v, true);
            }
            return $hidden;
        }

        return '<input type="hidden" name="' . $name . '" value="' . esc((string)$value) . "\" />\n";
    }
}

if (!function_exists('form_text_input')) {
    /**
     * 產生文字輸入框
     */
    function form_text_input(string $fieldName, ?string $value = null, string|array $style = '', string|array $extra = ''): string
    {
        $data = ['name' => $fieldName, 'id' => $fieldName, 'type' => 'text', 'value' => $value ?? ''];

        if ($style) {
            if (is_array($style)) {
                $s = '';
                foreach ($style as $key => $val) {
                    $s .= "$key:$val;";
                }
                $data['style'] = $s;
            } else {
                $data['style'] = $style;
            }
        }

        if (is_array($extra)) {
            $data = array_merge($data, $extra);
        } elseif ($extra !== '') {
            $extra = trim($extra);
            foreach (explode(' ', $extra) as $h) {
                $ss = explode('=', $h);
                $data[$ss[0]] = $ss[1] ?? null;
            }
        }

        $data['class'] = isset($data['class']) ? 'form-control ' . $data['class'] : 'form-control';

        return form_input($data);
    }
}

if (!function_exists('form_text_field')) {
    /**
     * 產生文字欄位
     */
    function form_text_field(string $fieldName, ?object $editData = null, string $default = '', string|array $style = '', bool $editReadonly = false, string|array $extra = ''): string
    {
        if ($editData && $editReadonly) {
            return '<div class="form-control" readonly>' . ($editData->$fieldName ?? '') . '</div>';
        }
        if ($editData === null) {
            $value = set_value($fieldName, $default);
        } else {
            $value = set_value($fieldName, $editData->$fieldName ?? '', false);
        }
        return form_text_input($fieldName, $value, $style, $extra);
    }
}

if (!function_exists('form_textarea_input')) {
    /**
     * 產生文字區域輸入框
     */
    function form_textarea_input(string $fieldName, ?string $value = null, string|array $style = '', string|array $extra = ''): string
    {
        $data = ['name' => $fieldName, 'id' => $fieldName, 'value' => $value ?? ''];

        if ($style) {
            if (is_array($style)) {
                $s = '';
                foreach ($style as $key => $val) {
                    $s .= "$key:$val;";
                }
                $data['style'] = $s;
            } else {
                $data['style'] = $style;
            }
        }

        if (is_array($extra)) {
            $data = array_merge($data, $extra);
        } elseif ($extra !== '') {
            foreach (explode(' ', $extra) as $h) {
                $ss = explode('=', $h);
                $data[$ss[0]] = $ss[1] ?? null;
            }
        }

        $data['class'] = isset($data['class']) ? 'form-control ' . $data['class'] : 'form-control';
        $data['rows'] = $data['rows'] ?? '5';

        return form_textarea($data);
    }
}

if (!function_exists('form_textarea_field')) {
    /**
     * 產生文字區域欄位
     */
    function form_textarea_field(string $fieldName, ?object $editData = null, string $default = '', string|array $style = '', bool $editReadonly = false, string|array $extra = ''): string
    {
        if ($editData && $editReadonly) {
            return $editData->$fieldName ?? '';
        }
        if ($editData === null) {
            $value = set_value($fieldName, $default);
        } else {
            $value = set_value($fieldName, $editData->$fieldName ?? '');
        }
        return form_textarea_input($fieldName, $value, $style, $extra);
    }
}

if (!function_exists('form_password_input')) {
    /**
     * 產生密碼輸入框
     */
    function form_password_input(string $fieldName, ?string $value = null, string|array $style = '', string|array $extra = ''): string
    {
        $data = ['name' => $fieldName, 'id' => $fieldName, 'type' => 'password', 'value' => $value ?? '', 'autocomplete' => 'off'];

        if ($style) {
            if (is_array($style)) {
                $s = '';
                foreach ($style as $key => $val) {
                    $s .= "$key:$val;";
                }
                $data['style'] = $s;
            } else {
                $data['style'] = $style;
            }
        }

        if (is_array($extra)) {
            $data = array_merge($data, $extra);
        } elseif ($extra !== '') {
            foreach (explode(' ', $extra) as $h) {
                $ss = explode('=', $h);
                $data[$ss[0]] = $ss[1] ?? null;
            }
        }

        $data['class'] = isset($data['class']) ? 'form-control ' . $data['class'] : 'form-control';

        return form_input($data);
    }
}

if (!function_exists('form_password_field')) {
    /**
     * 產生密碼欄位
     */
    function form_password_field(string $fieldName, ?object $editData = null, string $default = '', string|array $style = '', bool $editReadonly = false, string|array $extra = ''): string
    {
        if ($editData && $editReadonly) {
            return $editData->$fieldName ?? '';
        }
        if ($editData === null) {
            $value = set_value($fieldName, $default);
        } else {
            $value = set_value($fieldName, $editData->$fieldName ?? '');
        }
        return form_password_input($fieldName, $value, $style, $extra);
    }
}

if (!function_exists('form_dropdown_input')) {
    /**
     * 產生下拉選單輸入框
     */
    function form_dropdown_input(string $fieldName, array $options = [], ?string $value = null, string|array $style = '', string|array $extra = ''): string
    {
        $data = ['name' => $fieldName, 'id' => $fieldName];

        if ($style) {
            if (is_array($style)) {
                $s = '';
                foreach ($style as $key => $val) {
                    $s .= "$key:$val;";
                }
                $data['style'] = $s;
            } else {
                $data['style'] = $style;
            }
        }

        if (is_array($extra)) {
            $data = array_merge($data, $extra);
        } elseif ($extra !== '') {
            foreach (explode(' ', $extra) as $h) {
                $ss = explode('=', $h);
                $data[$ss[0]] = $ss[1] ?? null;
            }
        }

        $data['class'] = isset($data['class']) ? 'form-control ' . $data['class'] : 'form-control';

        return form_dropdown($fieldName, $options, $value, stringify_attributes($data));
    }
}

if (!function_exists('form_dropdown_field')) {
    /**
     * 產生下拉選單欄位
     */
    function form_dropdown_field(string $fieldName, array $options = [], ?object $editData = null, mixed $default = '', string|array $style = '', bool $editReadonly = false, string|array $extra = ''): string
    {
        if ($editData && $editReadonly) {
            if (array_key_exists($editData->$fieldName ?? '', $options)) {
                return '<div class="form-control">' . $options[$editData->$fieldName] . '</div>';
            }
            return '<div class="form-control">' . ($editData->$fieldName ?? '') . '</div>';
        }
        if ($editData === null) {
            $value = set_value($fieldName, $default);
        } else {
            $value = set_value($fieldName, $editData->$fieldName ?? '');
        }
        return form_dropdown_input($fieldName, $options, $value, $style, $extra);
    }
}

if (!function_exists('form_checkbox_input')) {
    /**
     * 產生核取方塊輸入框
     */
    function form_checkbox_input(string $fieldName, ?string $value = null, string $label = '', bool $checked = false, string|array $extra = '', string|array $checkboxExtra = ''): string
    {
        $id = strpos($fieldName, '[]') !== false
            ? str_replace('[]', '_' . $value, $fieldName)
            : $fieldName;

        $data = ['name' => $fieldName, 'id' => $id, 'value' => $value, 'checked' => $checked];

        if (!is_array($checkboxExtra) && $checkboxExtra !== '') {
            $arr = explode(' ', $checkboxExtra);
            foreach ($arr as $h) {
                $ss = explode('=', $h);
                $data[$ss[0]] = $ss[1] ?? null;
            }
        } elseif (is_array($checkboxExtra)) {
            $data = array_merge($data, $checkboxExtra);
        }

        if (!is_array($extra)) {
            $arr = explode(' ', $extra);
            $extra = [];
            foreach ($arr as $h) {
                if ($h) {
                    $ss = explode('=', $h);
                    $extra[$ss[0]] = $ss[1] ?? null;
                }
            }
        }
        $extra['class'] = isset($extra['class']) ? 'checkbox ' . $extra['class'] : 'checkbox';

        return "<div" . stringify_attributes($extra) . "><label>" . form_checkbox($data) . $label . ' </label></div>';
    }
}

if (!function_exists('form_radio_input')) {
    /**
     * 產生單選按鈕輸入框
     */
    function form_radio_input(string $fieldName, array $options = [], ?string $default = null, string|array $style = '', string|array $extra = ''): string
    {
        $data = ['name' => $fieldName];

        if ($style) {
            if (is_array($style)) {
                $s = '';
                foreach ($style as $key => $value) {
                    $s .= "$key:$value;";
                }
                $data['style'] = $s;
            } else {
                $data['style'] = $style;
            }
        }

        if (is_array($extra)) {
            $data = array_merge($data, $extra);
        } elseif ($extra !== '') {
            foreach (explode(' ', $extra) as $h) {
                $ss = explode('=', $h);
                $data[$ss[0]] = $ss[1] ?? null;
            }
        }

        $result = '';
        foreach ($options as $k => $v) {
            $data['id'] = $fieldName . $k;
            $data['value'] = $k;
            if ($k == $default) {
                $data['checked'] = '1';
            } else {
                unset($data['checked']);
            }
            $result .= "<label style='padding-right: 10px'>" . form_radio($data, ($k == $default)) . $v . "</label>  ";
        }

        return "<div class=\"radio\">{$result}</div>";
    }
}

if (!function_exists('form_radio_field')) {
    /**
     * 產生單選按鈕欄位
     */
    function form_radio_field(string $fieldName, array $options = [], ?object $editData = null, string $default = '', string|array $style = ''): string
    {
        $value = $editData === null ? $default : ($editData->$fieldName ?? '');
        return form_radio_input($fieldName, $options, $value, $style);
    }
}

if (!function_exists('form_date_input')) {
    /**
     * 產生日期輸入框
     */
    function form_date_input(string $fieldName, ?string $value = null, string|array $style = '', string|array $extra = ''): string
    {
        $output = form_text_input($fieldName, $value, $style, $extra);
        return '<div class="input-group date">' . $output . '<span class="input-group-addon"><i class="fa fa-calendar"></i></span></div>';
    }
}

if (!function_exists('form_date_field')) {
    /**
     * 產生日期欄位
     */
    function form_date_field(string $fieldName, ?object $editData = null, string $default = '', bool $editReadonly = false, string|array $style = '', string|array $extra = ''): string
    {
        if ($editData && $editReadonly) {
            return '<div class="form-control">' . ($editData->$fieldName ?? '') . '</div>';
        }
        $value = $editData === null ? $default : ($editData->$fieldName ?? '');
        return form_date_input($fieldName, $value, $style, $extra);
    }
}

if (!function_exists('form_time_input')) {
    /**
     * 產生時間輸入框
     */
    function form_time_input(string $fieldName, ?string $value = null, string|array $style = '', string|array $extra = ''): string
    {
        $output = form_text_input($fieldName, $value, $style, $extra);
        return '<div class="input-group time">' . $output . '<span class="input-group-addon"><i class="fa fa-clock-o"></i></span></div>';
    }
}

if (!function_exists('form_time_field')) {
    /**
     * 產生時間欄位
     */
    function form_time_field(string $fieldName, ?object $editData = null, string $default = '', bool $editReadonly = false, string|array $style = '', string|array $extra = ''): string
    {
        if ($editData && $editReadonly) {
            return '<div class="form-control">' . ($editData->$fieldName ?? '') . '</div>';
        }
        $value = $editData === null ? $default : ($editData->$fieldName ?? '');
        return form_time_input($fieldName, $value, $style, $extra);
    }
}

if (!function_exists('form_checkbox_array_input')) {
    /**
     * 產生核取方塊陣列輸入框
     *
     * @param string $fieldName 欄位名稱
     * @param array $options 選項陣列 [value => label]
     * @param string|array $value 已選擇的值 (逗號分隔字串或陣列)
     * @param string|array $checkExtra 額外屬性
     * @param string|array $extra 容器額外屬性
     * @param string|array $checkboxExtra checkbox額外屬性
     * @return string
     */
    function form_checkbox_array_input(string $fieldName, array $options = [], string|array|null $value = '', string|array $checkExtra = '', string|array $extra = '', string|array $checkboxExtra = ''): string
    {
        if (!is_array($value)) {
            $value = ($value !== '' && $value !== null) ? explode(',', $value) : [];
        }

        $result = '';
        foreach ($options as $k => $v) {
            $checked = in_array((string)$k, $value);
            $result .= form_checkbox_input($fieldName . '[]', (string)$k, $v, $checked, $checkExtra, $checkboxExtra);
        }

        if (!is_array($extra)) {
            $arr = explode(' ', $extra);
            $extra = [];
            foreach ($arr as $h) {
                if ($h !== '') {
                    $ss = explode('=', $h);
                    $extra[$ss[0]] = $ss[1] ?? null;
                }
            }
        }

        return "<div id=\"checkbox_{$fieldName}\"" . stringify_attributes($extra) . ">{$result}</div>";
    }
}

if (!function_exists('form_checkbox_array_field')) {
    /**
     * 產生核取方塊陣列欄位
     *
     * @param string $fieldName 欄位名稱
     * @param array $options 選項陣列 [value => label]
     * @param object|null $editData 編輯資料物件
     * @param string $default 預設值 (逗號分隔)
     * @param string|array $style 樣式
     * @param string $class 容器CSS類別
     * @param string|array $extra 額外屬性
     * @return string
     */
    function form_checkbox_array_field(string $fieldName, array $options = [], ?object $editData = null, string $default = '', string|array $style = '', string $class = '', string|array $extra = ''): string
    {
        // 取得已選擇的值
        if ($editData !== null && isset($editData->$fieldName)) {
            $value = $editData->$fieldName;
            if (is_array($value)) {
                $selectedValues = $value;
            } elseif ($value !== null && $value !== '') {
                $selectedValues = explode(',', $value);
            } else {
                $selectedValues = [];
            }
        } else {
            $selectedValues = $default ? explode(',', $default) : [];
        }

        // 處理額外屬性
        $extraStr = '';
        if (is_array($extra)) {
            $extraStr = stringify_attributes($extra);
        } elseif ($extra !== '') {
            $extraStr = $extra;
        }

        $output = '';
        foreach ($options as $value => $label) {
            $checked = in_array((string)$value, $selectedValues);
            $id = $fieldName . '_' . $value;

            $checkboxData = [
                'name' => $fieldName . '[]',
                'id' => $id,
                'value' => $value,
                'checked' => $checked,
            ];

            // 添加 disabled 屬性
            if (strpos($extraStr, 'disabled') !== false) {
                $checkboxData['disabled'] = 'disabled';
            }

            $output .= '<div class="checkbox"><label>';
            $output .= form_checkbox($checkboxData);
            $output .= ' ' . esc($label);
            $output .= '</label></div>';
        }

        if ($class) {
            return '<div class="' . $class . '">' . $output . '</div>';
        }

        return $output;
    }
}

if (!function_exists('form_checkbox_field')) {
    /**
     * 產生核取方塊欄位（用於編輯表單）
     *
     * @param string $fieldName 欄位名稱
     * @param mixed $value 核取方塊值
     * @param string $label 標籤文字
     * @param object|null $editData 編輯資料物件
     * @param bool $default 預設是否勾選
     * @param string|array $extra 額外屬性
     * @return string
     */
    function form_checkbox_field(string $fieldName, mixed $value = '', string $label = '', ?object $editData = null, bool $default = false, string|array $extra = ''): string
    {
        if ($editData === null) {
            // 新增模式
            $checked = set_value($fieldName, $default);
        } else {
            // 編輯模式
            $checked = set_value($fieldName, isset($editData->$fieldName) && $editData->$fieldName == $value);
        }

        return form_checkbox_input($fieldName, (string)$value, $label, (bool)$checked, $extra);
    }
}
