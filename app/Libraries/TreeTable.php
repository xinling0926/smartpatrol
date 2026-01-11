<?php

namespace App\Libraries;

/**
 * 通用的表格无限级分类
 */
class TreeTable
{
    /**
     * 來源資料裡 存放ID資料的欄位名稱
     */
    protected string $id_field = 'id';

    /**
     * 來源資料裡 存放ID資料的欄位名稱
     */
    protected string $parent_id_field = 'parent_id';

    /**
     * 來源資料裡 存放要顯示文字的欄位名稱
     */
    protected string $text_field = 'text';

    /**
     * 來源資料裡 存放顯示順序的欄位名稱
     */
    protected string $order_field = 'order';

    /**
     * 是否要在第一格打印序號
     */
    protected bool $show_index = false;

    /**
     * 生成树型结构所需要的2维数组
     */
    protected array $data = [];
    public array $sorted = [];

    /**
     * 总行数
     */
    protected int $columns = 0;

    /**
     * 表格行数
     */
    protected int $total_rows = 0;

    /**
     * 每個資料結點的class
     */
    protected string $item_class = 'tree-table-item';

    /**
     * 每個資料結點要加上去的data欄位名稱
     */
    protected array $data_field = [];

    /**
     * @param mixed $data
     * @param array|null $option
     * @return bool|string
     */
    public function build_tree_table(mixed $data, ?array $option = null): bool|string
    {
        if (isset($option)) {
            $this->paser_option($option);
        }

        if (!is_array($data)) {
            return false;
        }
        $this->init_data($data);

        return $this->get_tree_table();
    }

    protected function init_data(array $arr = []): bool
    {
        if (!is_array($arr)) {
            return false;
        }
        foreach ($arr as $node) {
            $this->data[$node->{$this->id_field}] = $node;
        }

        // 計算父子關係與位在第幾層
        foreach ($this->data as $node) {
            if (!isset($node->_parents)) {
                $this->get_parents($node);
            }
        }

        // 計算每個節點有多少底層節點
        foreach ($this->data as $node) {
            if (!isset($node->_bottom_num)) {
                $this->get_bottom_num($node);
            }
        }

        $this->sort_data_root();
        for ($i = 2; $i <= $this->columns; $i++) {
            $this->sort_data_child($i);
        }

        return true;
    }

    /**
     * 解析參數
     */
    protected function paser_option(array $option): void
    {
        foreach ($option as $key => $value) {
            switch ($key) {
                case 'data_field':
                    $this->data_field = explode(',', $value);
                    break;
                default:
                    if (isset($this->$key)) {
                        $this->$key = $value;
                    }
            }
        }
    }

    /**
     * 計算该节点所有父节点ID号
     */
    protected function get_parents(object &$node): void
    {
        $this_parent_id = $node->{$this->parent_id_field};
        if ($this_parent_id > 0) {
            $parent = $this->data[$this_parent_id];
            if (!isset($parent->_depth)) {
                $this->get_parents($parent);
            }
            $node->_depth = $parent->_depth + 1;

            // 把自己的ID加入父親的child
            if (!isset($parent->_child)) {
                $parent->_child = [$node->{$this->id_field}];
            } else {
                $parent->_child[] = $node->{$this->id_field};
            }
        } else {
            $node->_depth = 1;
        }

        if ($node->_depth > $this->columns) {
            $this->columns = $node->_depth;
        }
    }

    /**
     * 計算每個節點有多少底層結點數
     */
    protected function get_bottom_num(object &$node): void
    {
        $node->_bottom_num = 0;
        if (isset($node->_child) && count($node->_child) > 0) {
            foreach ($node->_child as $child_id) {
                $child = $this->data[$child_id];
                if (!isset($child->_bottom_num)) {
                    $this->get_bottom_num($child);
                }
                if ($child->_bottom_num == 0) {
                    $node->_bottom_num++;
                } else {
                    $node->_bottom_num += $child->_bottom_num;
                }
            }
        }
    }

    /**
     * 排序第一行資料並計算 所在第幾列
     */
    protected function sort_data_root(): void
    {
        $order_arr = [];
        $data = [];
        // 要进行排序的字段
        foreach ($this->data as $id => $node) {
            if ($node->_depth == 1) {
                $order_arr[$id] = $node->{$this->order_field};
                $data[] = $node;
            }
        }

        // 先根据_parentd排序，再根据order_field号排序
        array_multisort(
            $order_arr, SORT_ASC, SORT_NUMERIC,
            $data
        );

        // 計算每個節點在第幾列
        $i = 0;
        foreach ($data as $node) {
            $node->_row = $i;
            if ($node->_bottom_num <= 1) {
                $i++;
            } else {
                $i += $node->_bottom_num;
            }
            $this->sorted[$node->{$this->id_field}] = $node;
        }

        $this->total_rows = $i;
    }

    /**
     * 以_parents與order_field排序資料
     */
    protected function sort_data_child(int $depth): void
    {
        $pid_arr = [];
        $order_arr = [];
        $data = [];
        // 要进行排序的字段
        foreach ($this->data as $id => $node) {
            if ($node->_depth == $depth) {
                $pid_arr[$id] = $node->{$this->parent_id_field};
                $order_arr[$id] = $node->{$this->order_field};
                $data[] = $node;
            }
        }

        // 先根据_parentd排序，再根据order_field号排序
        array_multisort(
            $pid_arr, SORT_ASC, SORT_NUMERIC,
            $order_arr, SORT_ASC, SORT_NUMERIC,
            $data
        );

        $parent_id = 0;
        $row = 0;
        foreach ($data as $node) {
            if ($parent_id <> $node->{$this->parent_id_field}) {
                $parent_id = $node->{$this->parent_id_field};
                $row = $this->data[$node->{$this->parent_id_field}]->_row;
            }

            $node->_row = $row;
            $this->sorted[$node->{$this->id_field}] = $node;

            if ($node->_bottom_num <= 1) {
                $row++;
            } else {
                $row += $node->_bottom_num;
            }
        }
    }

    /**
     * 获取分类的表格展现形式(不包含表头)
     */
    protected function get_tree_table(): string
    {
        $rows = [];
        foreach ($this->sorted as $node) {
            $rowspan = $node->_bottom_num > 1 ? " rowspan='{$node->_bottom_num}'" : '';
            $col_span = $node->_bottom_num == 0 ? $this->columns - $node->_depth + 1 : 0;
            $colspan = $col_span > 1 ? " colspan='{$col_span}'" : '';

            if (isset($node->_child)) {
                $data = ' data-has-chill="1"';
            } else {
                $data = ' data-has-chill="0"';
            }
            $data .= ' data-depth="' . $node->_depth . '"';
            foreach ($this->data_field as $field_name) {
                $data .= " data-{$field_name}='{$node->$field_name}'";
            }

            if (isset($rows[$node->_row])) {
                $rows[$node->_row] .= "<td{$rowspan}{$colspan} class='{$this->item_class}'{$data}>" . $node->{$this->text_field} . '</td>';
            } else {
                $rows[$node->_row] = "<td{$rowspan}{$colspan} class='{$this->item_class}'{$data}>" . $node->{$this->text_field} . '</td>';
            }
        }

        $table_string = '';
        for ($i = 0; $i < $this->total_rows; $i++) {
            $table_string .= "<tr>";
            if ($this->show_index) {
                $table_string .= '<td>' . ($i + 1) . '</td>';
            }
            $table_string .= $rows[$i];
            $table_string .= "</tr>";
        }

        return $table_string;
    }
}
