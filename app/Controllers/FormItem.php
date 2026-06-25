<?php

namespace App\Controllers;

/**
 * FormItem Controller - 巡檢項目管理
 */
class FormItem extends AdminController
{
    protected $fmd01Model;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper('language');
        $this->fmd01Model = model('Fmd01Model');
    }

    public function index(): string
    {
        $pageSize = $this->getPageSize();
        $totalRows = $this->fmd01Model->countByEdit(null);
        $this->data['data'] = $this->fmd01Model->getByEdit(null, $pageSize);
        $this->data['offset'] = 0;
        $this->setPage($totalRows, 1, $pageSize);

        $cod02Model = model('Cod02Model');
        $this->data['fmd0108_opt'] = $cod02Model->getOptionArrayByCode('fmd0108');
        $this->data['fmd0105_opt'] = $cod02Model->getOptionArrayByCode('fmd0105');

        $ent10Model = model('Ent10Model');
        $this->data['dep_opt'] = $ent10Model->getNameArray(
            'ent1001',
            'ent1004',
            ['' => lang('FormItem.index_select_default')],
            ['ent1002' => $this->session->get('ent0101'), 'ent1007' => 1]
        );

        return $this->render();
    }

    public function query(int $page = 0): string
    {
        if ($page) {
            $option = $this->loadQueryOption();
        } else {
            $page = 1;
            $option = $this->getQueryOption();
            if (isset($option['search']) && $option['search']) {
                $option['*search'] = "(fmd0103 like '%" . $option['search'] . "%' or fmd0104 like '%" . $option['search'] . "%')";
                unset($option['search']);
            }
            $this->setQueryOption($option);
        }

        $pageSize = $this->getPageSize();
        $offset = ($page - 1) * $pageSize;
        $this->data['offset'] = $offset;
        $totalRows = $this->fmd01Model->countByEdit($option);
        $this->data['data'] = $this->fmd01Model->getByEdit($option, $pageSize, $offset);
        $this->setPage($totalRows, $page, $pageSize);

        $cod02Model = model('Cod02Model');
        $this->data['fmd0105_opt'] = $cod02Model->getOptionArrayByCode('fmd0105');
        $this->data['fmd0108_opt'] = $cod02Model->getOptionArrayByCode('fmd0108');

        $ent10Model = model('Ent10Model');
        $this->data['dep_opt'] = $ent10Model->getNameArray('ent1001', 'ent1004', 0, ['ent1002' => $this->session->get('ent0101')]);

        return $this->render('');
    }

    public function detail(int $id): string
    {
        $page = (int)($this->request->getGet('page') ?? 1);
        ini_set('max_execution_time', '60');

        $this->data['data'] = $this->fmd01Model->find($id);

        $cod02Model = model('Cod02Model');
        $this->data['fmd0105_opt'] = $cod02Model->getOptionArrayByCode('fmd0105');
        $this->data['fmd0108_opt'] = $cod02Model->getOptionArrayByCode('fmd0108');
        $this->data['data_type_opt'] = $cod02Model->getOptionArrayByCode('data_type');

        $fmd02Model = model('Fmd02Model');
        $fmd02s = $fmd02Model->getByFmd0101($id);

        if ($this->data['data']->fmd0105 != 1 && $page == 4) {
            $page = 1;
        }

        $fmd03Model = model('Fmd03Model');
        $fmd04Model = model('Fmd04Model');
        $fmd05Model = model('Fmd05Model');
        $fmd06Model = model('Fmd06Model');

        $fmd03s = $fmd03Model->getByFmd0101($id);
        $fmd04sO = $fmd04Model->getByFmd0101($id, 'fmd0401,fmd0404,fmd0409,fmd0410,fmd0303');
        $fmd04s = [];
        foreach ($fmd04sO as $fmd04) {
            $fmd04s[$fmd04->fmd0401] = $fmd04;
        }

        log_message('debug', 'FormItem::detail() - fmd0101: ' . $id);
        log_message('debug', 'FormItem::detail() - fmd03s count: ' . count($fmd03s));
        log_message('debug', 'FormItem::detail() - fmd04s count: ' . count($fmd04sO));

        $fmd05s = $fmd05Model->getByFmd0101($id);
        $fmd06s = $fmd06Model->getByFmd0101($id, 'fmd0601,fmd0603,fmd0606,fmd0608,fmd0610,fmd0614');

        if ($page == 1) {
            $this->data['patrol_table'] = $this->buildPatrolTable($id, $fmd03s, $fmd04s, $fmd05s, $fmd06s);
        }

        $fmd07Model = model('Fmd07Model');
        $fmd07s = $fmd07Model->getByFmd0101($id);

        if ($page == 2) {
            $this->data['form_table'] = $this->buildFormTable($id, $fmd03s, $fmd04s, $fmd05s, $fmd06s, $fmd07s, $fmd02Model);
        }

        if ($page == 3) {
            $fmd08Model = model('Fmd08Model');
            $fmd09Model = model('Fmd09Model');
            $fmd08s = $fmd08Model->getByFmd0101($id);
            $fmd09s = $fmd09Model->getByFmd0101($id);
            $this->data['route_table'] = $this->buildRouteTable($fmd07s, $fmd08s, $fmd09s);
        }

        $this->data['page'] = $page;
        $this->data['fmd02s'] = $fmd02s;

        return $this->render();
    }

    protected function buildPatrolTable(int $fmd0101, array $fmd03s, array $fmd04s, array $fmd05s, array $fmd06s): string
    {
        $header = '<tr><th>' . lang('FormItem.f_common_no') . '</th>';
        foreach ($fmd03s as $d) {
            $header .= "<th>{$d->fmd0304}</th>";
        }
        foreach ($fmd05s as $d) {
            $header .= "<th>{$d->fmd0504}</th>";
        }
        $header .= '</tr>';

        $rows = [];
        $columns = count($fmd03s);
        $lastRow = 0;

        foreach ($fmd04s as $node) {
            $rowspan = ($node->fmd0410 ?? 0) > 1 ? " rowspan='{$node->fmd0410}'" : '';
            $colSpan = ($node->fmd0410 ?? 0) == 0 ? $columns - ($node->fmd0303 ?? 0) + 1 : 0;
            $colspan = $colSpan > 1 ? " colspan='{$colSpan}'" : '';

            $data = isset($node->_child) ? ' data-has-chill="1"' : ' data-has-chill="0"';
            $data .= ' data-fmd0303="' . ($node->fmd0303 ?? '') . '"';
            $data .= " data-fmd0401='{$node->fmd0401}'";

            $idx = $node->fmd0409 ?? 0;
            if (isset($rows[$idx])) {
                $rows[$idx] .= "<td{$rowspan}{$colspan} class='patrol-item'{$data}>" . htmlspecialchars($node->fmd0404 ?? '') . "</td>";
            } else {
                $rows[$idx] = "<td{$rowspan}{$colspan} class='patrol-item'{$data}>" . htmlspecialchars($node->fmd0404 ?? '') . "</td>";
            }
            if ($lastRow < $idx) {
                $lastRow = $idx;
            }
        }

        $cod02Model = model('Cod02Model');
        $dataType = $cod02Model->getOptionArrayByCode('data_type');

        foreach ($fmd06s as $fmd06) {
            if (isset($fmd04s[$fmd06->fmd0603])) {
                $fmd04 = $fmd04s[$fmd06->fmd0603];
                $data = " data-fmd0601='{$fmd06->fmd0601}'";
                $text = $dataType[$fmd06->fmd0606] ?? '';

                switch ($fmd06->fmd0606) {
                    case 0:
                        $text = '';
                        break;
                    case 1:
                    case 2:
                        $text .= '(' . ($fmd06->fmd0614 ?? '') . ')';
                        break;
                    case 4:
                    case 5:
                    case 7:
                    case 8:
                        $text .= '(' . str_replace(["\r\n", "\n"], '/', $fmd06->fmd0608 ?? '') . ')';
                        break;
                }
                $idx = $fmd04->fmd0409 ?? 0;
                $rows[$idx] = ($rows[$idx] ?? '') . "<td class='patrol-field'{$data}>{$text}</td>";
            }
        }

        $body = '';
        if ($rows) {
            for ($i = 0; $i <= $lastRow; $i++) {
                $body .= "<tr>";
                $body .= '<td>' . ($i + 1) . '</td>';
                $body .= $rows[$i] ?? '';
                $body .= "</tr>";
            }
        } else {
            // Add empty clickable row when no fmd04 data
            $totalCols = count($fmd03s) + count($fmd05s) + 1;
            $body .= "<tr class='patrol-item-empty' data-fmd0101='{$fmd0101}'>";
            $body .= "<td colspan='{$totalCols}' style='text-align:center;cursor:pointer;color:#999;'>" . lang('FormItem.click_to_add_first_item') . "</td>";
            $body .= "</tr>";
        }

        return "<table id=\"patrol_table\" class=\"table table-striped dataTable table-bordered\"><thead>{$header}</thead><tbody>{$body}</tbody></table>";
    }

    protected function buildFormTable(int $fmd0101, array $fmd03s, array $fmd04s, array $fmd05s, array $fmd06s, array $fmd07s, $fmd02Model): string
    {
        $header = '<tr><th>' . lang('FormItem.f_common_no') . '</th>';
        foreach ($fmd03s as $d) {
            $header .= "<th>{$d->fmd0304}</th>";
        }
        foreach ($fmd05s as $d) {
            $header .= "<th>{$d->fmd0504}</th>";
        }
        $header .= '</tr>';

        $rows = [];
        $columns = count($fmd03s);
        $lastRow = 0;
        foreach ($fmd04s as $node) {
            $rowspan = $node->fmd0410 > 1 ? " rowspan='{$node->fmd0410}'" : '';
            $colSpan = $node->fmd0410 == 0 ? $columns - $node->fmd0303 + 1 : 0;
            $colspan = $colSpan > 1 ? " colspan='{$colSpan}'" : '';
            if (isset($rows[$node->fmd0409])) {
                $rows[$node->fmd0409] .= "<td{$rowspan}{$colspan}>" . htmlspecialchars($node->fmd0404) . "</td>";
            } else {
                $rows[$node->fmd0409] = "<td{$rowspan}{$colspan}>" . htmlspecialchars($node->fmd0404) . "</td>";
            }
            if ($lastRow < $node->fmd0409) {
                $lastRow = $node->fmd0409;
            }
        }

        // Get daypart names for fmd0704 display
        $fmd02s = $fmd02Model->getNameArray('fmd0203', 'fmd0204', null, ['fmd0202' => $fmd0101], 'fmd0203');

        $fmd07sKey = [];
        foreach ($fmd07s as $k => $fmd07) {
            $fmd07sKey[$k] = $fmd07->fmd0701;
            // Process daypart restriction description
            if (!empty($fmd07->fmd0704)) {
                $fmd0704Names = [];
                for ($i = 0; $i < strlen($fmd07->fmd0704); $i++) {
                    if ($fmd07->fmd0704[$i] == '1' && isset($fmd02s[$i + 1])) {
                        $fmd0704Names[] = $fmd02s[$i + 1];
                    }
                }
                $fmd07->fmd0704_name = implode(',', $fmd0704Names);
            }
        }

        foreach ($fmd06s as $fmd06) {
            if (array_key_exists($fmd06->fmd0603, $fmd04s)) {
                $fmd04 = $fmd04s[$fmd06->fmd0603];
                if ($fmd06->fmd0610) {
                    $idx2 = array_search($fmd06->fmd0610, $fmd07sKey);
                    if ($idx2 !== false) {
                        $fmd07 = $fmd07s[$idx2];
                        $data = " data-id='{$fmd06->fmd0610}' data-fmd0601='{$fmd06->fmd0601}'";
                        $rows[$fmd04->fmd0409] .= "<td class='patrol-form-edit'{$data}>{$fmd07->fmd0703}";
                        if (!empty($fmd07->fmd0704) && !empty($fmd07->fmd0704_name)) {
                            $rows[$fmd04->fmd0409] .= "<br>" . lang('FormItem.fun_build_str_1') . $fmd07->fmd0704_name;
                        }
                        if (!empty($fmd07->fmd0706)) {
                            $rows[$fmd04->fmd0409] .= "<br>" . lang('FormItem.fun_build_str_2') . "{$fmd07->fmd0706}";
                        }
                        $rows[$fmd04->fmd0409] .= "</td>";
                    } else {
                        $rows[$fmd04->fmd0409] .= "<td></td>";
                    }
                } elseif ($fmd06->fmd0606 > 0) {
                    $data = " data-id='{$fmd06->fmd0601}'";
                    $rows[$fmd04->fmd0409] .= "<td class='patrol-form-select'{$data}></td>";
                } else {
                    $rows[$fmd04->fmd0409] .= "<td></td>";
                }
            }
        }

        $body = '';
        if ($rows) {
            for ($i = 0; $i <= $lastRow; $i++) {
                $body .= "<tr>";
                $body .= '<td>' . ($i + 1) . '</td>';
                $body .= $rows[$i] ?? '';
                $body .= "</tr>";
            }
        }

        return "<table id=\"form_table\" class=\"table dataTable table-bordered\"><thead>{$header}</thead><tbody>{$body}</tbody></table>";
    }

    protected function buildRouteTable(array $fmd07s, array $fmd08s, array $fmd09s): string
    {
        $fmd07sKey = [];
        foreach ($fmd07s as $k => $fmd07) {
            $fmd07sKey[$k] = $fmd07->fmd0701;
        }

        $header = "<tr><th>" . lang('FormItem.fun_build_str_3') . "</th><th>" . lang('FormItem.fun_build_str_4') . "</th><th>" . lang('FormItem.fun_build_str_5') . "</th></tr>";
        $body = "";

        foreach ($fmd08s as $fmd08) {
            $count = 0;
            $s = '';
            foreach ($fmd09s as $fmd09) {
                if ($fmd09->fmd0903 == $fmd08->fmd0801) {
                    if ($count > 0) {
                        $s .= '</tr><tr>';
                    }
                    $s .= "<td class='patrol-tag' data-fmd0901='{$fmd09->fmd0901}'>{$fmd09->fmd0904}. ";
                    $s .= "{$fmd09->fmd0905} {$fmd09->fmd0908}</td>";
                    $s .= '<td>';
                    foreach ($fmd07s as $fmd07) {
                        if ($fmd07->fmd0708 == $fmd09->fmd0901) {
                            $s .= "<span class='patrol-form' data-id='{$fmd09->fmd0901}-{$fmd07->fmd0701}'><i class='fa fa-fw fa-file-text'></i>{$fmd07->fmd0703}</span>";
                        }
                    }
                    $s .= '</td>';
                    $count++;
                }
            }
            $data = " data-fmd0801='{$fmd08->fmd0801}'";
            $rowspan = $count > 1 ? " rowspan='{$count}'" : '';
            if ($count > 0) {
                $body .= "<tr><td class='patrol-route'{$rowspan}{$data}>{$fmd08->fmd0804}</td>{$s}</tr>";
            } else {
                $body .= "<tr><td class='patrol-route'{$rowspan}{$data}>{$fmd08->fmd0804}</td><td></td><td></td></tr>";
            }
        }

        return "<table id=\"route_table\" class=\"table table-striped dataTable table-bordered\"><tbody>{$header}{$body}</tbody></table>";
    }

    public function editFmd02(int $id = 0): string|\CodeIgniter\HTTP\ResponseInterface
    {
        $fmd02Model = model('Fmd02Model');

        if ($postData = $this->request->getPost()) {
            $rules = ['fmd0204' => 'required'];

            if ($this->validate($rules)) {
                if (!isset($postData['fmd0201'])) {
                    if ($id = $fmd02Model->insertData($postData)) {
                        return $this->ajaxReturn("OK", ['id' => $id]);
                    }
                } else {
                    if ($fmd02Model->updateData($postData)) {
                        return $this->ajaxReturn("OK", ['id' => $postData['fmd0201']]);
                    }
                }
            }
            return $this->ajaxReturn($this->message->output());
        }

        if ($id) {
            $this->data['data'] = $fmd02Model->find($id);
            $reportId = $this->data['data']->fmd0202;
        } else {
            $this->data['data'] = null;
            $reportId = $this->request->getGet('parent');
            $this->data['fmd0202'] = $reportId;
        }
        $this->data['fmd01'] = $this->fmd01Model->find($reportId);

        return $this->render();
    }

    public function deleteFmd02(): \CodeIgniter\HTTP\ResponseInterface
    {
        $id = $this->request->getPost('id') ?? $this->request->getGet('id') ?? $this->request->getVar('id');
        if ($id) {
            $fmd02Model = model('Fmd02Model');
            if ($fmd02Model->delete($id)) {
                return $this->ajaxReturn("OK");
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn('ERR');
    }

    public function revert(): \CodeIgniter\HTTP\ResponseInterface
    {
        $id = $this->request->getPost('id') ?? $this->request->getGet('id') ?? $this->request->getVar('id');
        if ($id) {
            if ($this->fmd01Model->revert($id)) {
                return $this->ajaxReturn("OK");
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn('ERR');
    }

    public function commit(): \CodeIgniter\HTTP\ResponseInterface
    {
        $id = $this->request->getPost('id') ?? $this->request->getGet('id') ?? $this->request->getVar('id');
        if ($id) {
            if ($this->fmd01Model->commit($id)) {
                $dev01Model = model('Dev01Model');
                $dev01s = $dev01Model->getBy([
                    'dev0102' => $this->session->get('ent0101'),
                    'dev0106' => 1,
                    '*dev0107' => "dev0107 is not null and dev0107 <> ''"
                ]);

                $dev0107 = '';
                foreach ($dev01s as $v1) {
                    if (empty($v1->dev0107) || trim($v1->dev0107) === 'null') {
                        continue;
                    }
                    $dev0107 .= $v1->dev0107 . ';';
                }
                $dev0107 = rtrim($dev0107, ';');

                $fmd01s = $this->fmd01Model->find($id);
                $alert = [
                    'title' => lang('FormItem.fun_commit_hint_title'),
                    'info' => sprintf(lang('FormItem.fun_commit_hint_info'), $fmd01s->fmd0104, $fmd01s->fmd0107),
                ];

                helper('common');
                pushToDevice(['alert' => $alert], $dev0107);

                return $this->ajaxReturn("OK");
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn('ERR');
    }

    public function checkOut(): \CodeIgniter\HTTP\ResponseInterface
    {
        $id = $this->request->getPost('id') ?? $this->request->getGet('id') ?? $this->request->getVar('id');
        if ($id) {
            if ($newId = $this->fmd01Model->checkOut($id)) {
                return $this->ajaxReturn("OK", ['id' => $newId]);
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn('ERR');
    }

    public function formHistory(int $fmd0101): string
    {
        if ($fmd01 = $this->fmd01Model->find($fmd0101)) {
            $this->data['fmd01s'] = $this->fmd01Model->getBy(['fmd0106' => $fmd01->fmd0106], 0, 0, 'fmd0107 desc');
            return $this->render();
        }
        return '';
    }

    public function addFmd04Sub(int $selectId): string|\CodeIgniter\HTTP\ResponseInterface
    {
        $fmd04Model = model('Fmd04Model');
        $select = $fmd04Model->find($selectId);

        $fmd03Model = model('Fmd03Model');
        $fmd03s = $fmd03Model->getByFmd0101($select->fmd0402);

        if (($child = $fmd04Model->getFirstChild($selectId))
            || $select->fmd0403 == $fmd03s[count($fmd03s) - 1]->fmd0301
        ) {
            // Has child, add sibling item
            $order = $select->fmd0408 + 1;
            if ($select->fmd0405) {
                $parent = $fmd04Model->find($select->fmd0405);
            } else {
                $parent = null;
            }
        } else {
            $parent = $select;
            $order = 1;
        }

        if ($postData = $this->request->getPost()) {
            if ($fmd04Model->add($select->fmd0402, $parent, $order, $postData)) {
                return $this->ajaxReturn("OK");
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }

        if ($parent) {
            for ($i = count($fmd03s) - 1; $i >= 0; $i--) {
                if ($fmd03s[$i]->fmd0301 == $parent->fmd0403) {
                    $fmd03s[$i]->text = $parent->fmd0404;
                    $pid = $parent->fmd0405;
                    for ($j = $i - 1; $j >= 0; $j--) {
                        if ($p = $fmd04Model->find($pid)) {
                            $fmd03s[$j]->text = $p->fmd0404;
                            $pid = $p->fmd0405;
                        }
                    }
                    break;
                }
            }
        }
        $this->data['fmd03s'] = $fmd03s;

        return $this->render();
    }

    /**
     * Edit patrol item (fmd04)
     */
    public function editFmd04(int $id): string|\CodeIgniter\HTTP\ResponseInterface
    {
        $fmd04Model = model('Fmd04Model');

        if ($postData = $this->request->getPost()) {
            $rules = ['fmd0404' => 'required'];

            if ($this->validate($rules)) {
                if ($fmd04Model->updateData($postData)) {
                    return $this->ajaxReturn("OK", ['id' => $id]);
                }
            }
            return $this->ajaxReturn($this->message->output());
        }

        $this->data['data'] = $fmd04Model->find($id);

        return $this->render();
    }

    public function editFmd01(int $id): string|\CodeIgniter\HTTP\ResponseInterface
    {
        if ($postData = $this->request->getPost()) {
            $rules = [
                'fmd0101' => 'required',
                'fmd0104' => 'required',
            ];

            if ($this->validate($rules)) {
                if ($this->fmd01Model->updateData($postData)) {
                    return $this->ajaxReturn("OK", ['id' => $id]);
                }
            }
            return $this->ajaxReturn($this->message->output());
        }

        $this->data['data'] = $this->fmd01Model->find($id);

        $ent10Model = model('Ent10Model');
        $this->data['dept'] = $ent10Model->getNameArray('ent1001', 'ent1004');

        return $this->render();
    }

    /**
     * Edit field settings (fmd06)
     */
    public function editFmd06(int $id): string|\CodeIgniter\HTTP\ResponseInterface
    {
        $fmd06Model = model('Fmd06Model');

        if ($postData = $this->request->getPost()) {
            $rules = ['fmd0606' => 'required'];

            if ($this->validate($rules)) {
                switch ($postData['fmd0606']) {
                    case 1:
                    case 2:
                        $postData['fmd0614'] = $postData['fmd0614_2'] . '~' . $postData['fmd0614_3'];
                        $postData['fmd0607'] = 0;
                        break;
                    case 3:
                        $postData['fmd0614'] = $postData['fmd0614_1'];
                        break;
                    case 4:
                        $postData['fmd0614'] = $postData['fmd0614_1'];
                        $postData['fmd0607'] = 20;
                        break;
                    case 5:
                        $postData['fmd0609'] = $postData['fmd0609_1'];
                        $postData['fmd0614'] = $postData['fmd0614_1'];
                        $postData['fmd0607'] = 20;
                        break;
                    case 6:
                        $postData['fmd0609'] = $postData['fmd0609_1'];
                        $postData['fmd0614'] = '';
                        break;
                    case 7:
                        $postData['fmd0614'] = $postData['fmd0614_1'];
                        $postData['fmd0607'] = 20;
                        break;
                    case 8:
                        $postData['fmd0608'] = $postData['fmd0608_1'] . PHP_EOL . $postData['fmd0608_2'];
                        if ($postData['fmd0609_2'] == '0') {
                            $postData['fmd0609'] = $postData['fmd0608_1'];
                        } else {
                            $postData['fmd0609'] = $postData['fmd0608_2'];
                        }
                        $postData['fmd0614'] = '';
                        $postData['fmd0607'] = 10;
                        break;
                    case 9:
                        $postData['fmd0607'] = 20;
                        break;
                }
                if ($fmd06Model->updateData($postData)) {
                    return $this->ajaxReturn("OK", ['id' => $id]);
                }
            }
            return $this->ajaxReturn($this->message->output());
        }

        if ($id) {
            $this->data['data'] = $fmd06Model->find($id);
            $this->data['data']->fmd0608_1 = lang('FormItem.edit_fmd06_v_09_0');
            $this->data['data']->fmd0608_2 = lang('FormItem.edit_fmd06_v_09_1');
            $this->data['data']->fmd0609_1 = $this->data['data']->fmd0609;
            $this->data['data']->fmd0609_2 = 0;
            $this->data['data']->fmd0614_1 = $this->data['data']->fmd0614;
            $this->data['data']->fmd0614_2 = '';
            $this->data['data']->fmd0614_3 = '';

            switch ($this->data['data']->fmd0606) {
                case 1:
                case 2:
                    $this->data['data']->fmd0614_1 = '';
                    $fmd0614 = explode('~', $this->data['data']->fmd0614);
                    if (count($fmd0614) == 2) {
                        $this->data['data']->fmd0614_2 = $fmd0614[0];
                        $this->data['data']->fmd0614_3 = $fmd0614[1];
                    } else {
                        $this->data['data']->fmd0614_2 = '';
                        $this->data['data']->fmd0614_3 = '';
                    }
                    $this->data['data']->fmd0609_1 = '';
                    break;
                case 5:
                case 6:
                    $this->data['data']->fmd0609_1 = $this->data['data']->fmd0609;
                    $opt = explode(PHP_EOL, $this->data['data']->fmd0609);
                    $this->data['data']->fmd0609 = $opt[0];
                    break;
                case 8:
                    $opt = explode(PHP_EOL, $this->data['data']->fmd0608);
                    $this->data['data']->fmd0608_1 = $opt[0] ?? '';
                    $this->data['data']->fmd0608_2 = $opt[1] ?? '';
                    $this->data['data']->fmd0609_2 = array_search($this->data['data']->fmd0609, $opt);
                    $this->data['data']->fmd0609_1 = $this->data['data']->fmd0609;
                    $this->data['data']->fmd0609 = '';
                    break;
            }
        } else {
            $this->data['data'] = null;
        }

        $fmd04Model = model('Fmd04Model');
        $this->data['prompt'] = $fmd04Model->getPatrolItemPrompt($this->data['data']->fmd0603);

        $fmd05Model = model('Fmd05Model');
        $this->data['fmd05'] = $fmd05Model->find($this->data['data']->fmd0604);

        $cod02Model = model('Cod02Model');
        $this->data['data_type_opt'] = $cod02Model->getOptionArrayByCode('data_type');

        return $this->render();
    }

    /**
     * Add first patrol item when table is empty
     */
    public function addFmd04First(int $fmd0101): string|\CodeIgniter\HTTP\ResponseInterface
    {
        $fmd03Model = model('Fmd03Model');
        $fmd04Model = model('Fmd04Model');

        $fmd03s = $fmd03Model->getByFmd0101($fmd0101);

        if ($postData = $this->request->getPost()) {
            // Add first patrol item with no parent, order 1
            if ($fmd04Model->add($fmd0101, null, 1, $postData)) {
                return $this->ajaxReturn("OK");
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }

        $this->data['fmd03s'] = $fmd03s;

        return $this->render();
    }

    /**
     * Edit electronic form settings (auto split)
     */
    public function editFmd07(int $fmd0101): string|\CodeIgniter\HTTP\ResponseInterface
    {
        $fmd07Model = model('Fmd07Model');

        if ($postData = $this->request->getPost()) {
            $fields = $postData['fields'] ?? null;
            if ($fields === null) {
                return $this->ajaxReturn(lang('FormItem.fun_edit_fmd07_err_msg'));
            }

            if (!empty($postData['delete_old'])) {
                $fmd07Model->deleteByFmd0101($fmd0101);
            }
            $fmd07Model->autoSplit($fmd0101, $fields, $postData['prefix'] ?? '', $postData['suffix'] ?? '');

            return $this->ajaxReturn("OK");
        }

        $fmd03Model = model('Fmd03Model');
        $fmd03s = $fmd03Model->getByFmd0101($fmd0101);

        // Create name array from fmd03s
        $fmd03sArray = [];
        foreach ($fmd03s as $fmd03) {
            $fmd03sArray[$fmd03->fmd0301] = $fmd03->fmd0304;
        }
        $this->data['fmd03s'] = $fmd03sArray;
        $this->data['fmd0101'] = $fmd0101;

        return $this->render();
    }

    /**
     * 新增電子表單到巡檢欄位
     */
    public function addFmd07(string $id): string|\CodeIgniter\HTTP\ResponseInterface
    {
        $fmd06Model = model('Fmd06Model');
        $fmd07Model = model('Fmd07Model');

        $fmd06s = explode('_', $id);
        $fmd06 = $fmd06Model->find($fmd06s[0]);

        $fmd02Model = model('Fmd02Model');
        $fmd02s = $fmd02Model->getNameArray('fmd0203', 'fmd0204', null, ['fmd0202' => $fmd06->fmd0602], 'fmd0203');

        if ($postData = $this->request->getPost()) {
            $rules = ['method' => 'required|integer'];
            if (($postData['method'] ?? '') == '1') {
                $rules['fmd0701'] = 'required';
            } else {
                $rules['fmd0703'] = 'required';
            }

            if ($this->validate($rules)) {
                if ($postData['method'] == '2') {
                    // 還原fmd0704
                    $fmd0704_id = $postData['fmd0704'] ?? [];
                    if ($fmd0704_id) {
                        $fmd0704 = str_repeat('0', count($fmd02s));
                        for ($i = 0; $i < count($fmd02s); $i++) {
                            if (in_array($i + 1, $fmd0704_id)) {
                                $fmd0704[$i] = '1';
                            }
                        }
                    } else {
                        $fmd0704 = '';
                    }

                    // 組合fmd0706
                    $fmd0706 = '';
                    if (!empty($postData['fmd0706_d'])) {
                        $fmd0706 = 'd' . implode('', $postData['fmd0706_d']);
                    }
                    if (!empty($postData['fmd0706_w'])) {
                        $fmd0706 .= 'w' . implode('', $postData['fmd0706_w']);
                    }
                    if (!empty($postData['fmd0706_i'])) {
                        $fmd0706 .= ($postData['fmd0706_t'] ?? 'i') . implode('', $postData['fmd0706_i']);
                    }

                    // 先新增電子表單
                    $fmd0610 = $fmd07Model->add($fmd06->fmd0602, $postData['fmd0703'], $fmd0704, $fmd0706);
                } else if ($postData['method'] == '1') {
                    // 取得使用者選擇的電子表單
                    $fmd0610 = $postData['fmd0701'];
                }

                // 修改欄位資料記錄電子表單編號
                foreach ($fmd06s as $fmd0601) {
                    $fmd06Model->updateData(['fmd0601' => $fmd0601, 'fmd0610' => $fmd0610]);
                }

                return $this->ajaxReturn("OK");
            }
            return $this->ajaxReturn($this->message->output());
        }

        $this->data['id'] = $id;
        // 建立電子表單名稱陣列
        $fmd07Records = $fmd07Model->getByFmd0101($fmd06->fmd0602);
        $fmd07s = [];
        foreach ($fmd07Records as $fmd07) {
            $fmd07s[$fmd07->fmd0701] = $fmd07->fmd0703;
        }
        $this->data['fmd07s'] = $fmd07s;
        $this->data['fmd02s'] = $fmd02s;

        // 準備日期限定選項
        $cod02Model = model('Cod02Model');
        $this->data['week'] = $cod02Model->getOptionArrayByCode('week2');
        $day = [];
        for ($i = 1; $i <= 31; $i++) {
            $day[sprintf('%02d', $i)] = $i;
        }
        $this->data['day'] = $day;
        $this->data['fmd01'] = $this->fmd01Model->find($fmd06->fmd0602);

        return $this->render();
    }

    /**
     * 編輯路線設定
     */
    public function editFmd08(int $id): string|\CodeIgniter\HTTP\ResponseInterface
    {
        $fmd08Model = model('Fmd08Model');

        if ($postData = $this->request->getPost()) {
            $rules = [
                'fmd0803' => 'required|integer',
                'fmd0804' => 'required',
            ];
            if ($this->validate($rules)) {
                // 設定預設值
                if (!isset($postData['fmd0805'])) {
                    $postData['fmd0805'] = '0';
                }
                if (!array_key_exists('fmd0801', $postData)) {
                    // 新增
                    if ($newId = $fmd08Model->insertData($postData)) {
                        return $this->ajaxReturn("OK", ['id' => $newId]);
                    }
                } else {
                    // 修改
                    if ($fmd08Model->updateData($postData)) {
                        return $this->ajaxReturn("OK", ['id' => $postData['fmd0801']]);
                    }
                }
            }
            return $this->ajaxReturn($this->message->output());
        }

        if ($id) {
            $this->data['data'] = $fmd08Model->find($id);
        } else {
            $this->data['data'] = null;
            $this->data['fmd0802'] = $this->request->getGet('parent');
        }

        return $this->render();
    }

    /**
     * 新增巡檢點
     */
    public function addFmd09(int $fmd0801): string|\CodeIgniter\HTTP\ResponseInterface
    {
        $fmd09Model = model('Fmd09Model');

        if ($postData = $this->request->getPost()) {
            $rules = [
                'fmd0903' => 'required|integer',
                'fmd0904' => 'required|integer',
                'fmd0908' => 'required',
            ];
            if ($this->validate($rules)) {
                $fmd08Model = model('Fmd08Model');
                $fmd08 = $fmd08Model->find($fmd0801);
                $postData['fmd0902'] = $fmd08->fmd0802;

                if ($postData['fmd0910'] === '') {
                    $postData['fmd0910'] = null;
                }
                if ($postData['fmd0911'] === '') {
                    $postData['fmd0911'] = null;
                }

                if ($id = $fmd09Model->insertData($postData)) {
                    return $this->ajaxReturn("OK", ['id' => $id]);
                }
            }
            return $this->ajaxReturn($this->message->output());
        }

        $this->data['fmd0903'] = $fmd0801;
        $this->data['fmd0904'] = $fmd09Model->getNextFmd0904($fmd0801);

        return $this->render();
    }

    /**
     * 刪除路線設定
     */
    public function deleteFmd08(): \CodeIgniter\HTTP\ResponseInterface
    {
        if ($id = $this->request->getPost('id')) {
            $fmd08Model = model('Fmd08Model');
            if ($fmd08Model->delete($id)) {
                return $this->ajaxReturn("OK");
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn("ID is required");
    }

    /**
     * 編輯電子表單
     */
    public function editForm(int $id): string|\CodeIgniter\HTTP\ResponseInterface
    {
        $fmd04Model = model('Fmd04Model');
        $fmd06Model = model('Fmd06Model');
        $fmd07Model = model('Fmd07Model');

        $fmd07 = $fmd07Model->find($id);
        if (!$fmd07) {
            return $this->ajaxReturn(lang('FormItem.fun_edit_form_not_found'));
        }

        $fmd02Model = model('Fmd02Model');
        $fmd02s = $fmd02Model->getNameArray('fmd0203', 'fmd0204', null, ['fmd0202' => $fmd07->fmd0702], 'fmd0203');

        if ($postData = $this->request->getPost()) {
            // Validate fmd0706_i and fmd0706_w combination
            if (!empty($postData['fmd0706_i'])) {
                if (($postData['fmd0706_t'] ?? '') == 'm' && empty($postData['fmd0706_w'])) {
                    return $this->ajaxReturn(lang('FormItem.fun_edit_form_err_msg'));
                }
            }

            // Rebuild fmd0704 from checkbox array
            $fmd0704_id = $postData['fmd0704'] ?? [];
            if ($fmd0704_id) {
                $fmd0704 = str_repeat('0', count($fmd02s));
                for ($i = 0; $i < count($fmd02s); $i++) {
                    if (in_array($i + 1, $fmd0704_id)) {
                        $fmd0704[$i] = '1';
                    }
                }
            } else {
                $fmd0704 = '';
            }

            // Build fmd0706 string
            $fmd0706 = '';
            if (!empty($postData['fmd0706_d'])) {
                $fmd0706 = 'd' . implode('', $postData['fmd0706_d']);
            }
            if (!empty($postData['fmd0706_w'])) {
                $fmd0706 .= 'w' . implode('', $postData['fmd0706_w']);
            }
            if (!empty($postData['fmd0706_i'])) {
                $fmd0706 .= ($postData['fmd0706_t'] ?? 'i') . implode('', $postData['fmd0706_i']);
            }

            // Check what needs to be updated
            $fmd07 = $fmd07Model->find($id);
            $update = ['fmd0701' => $id];
            $needsUpdate = false;

            if ($fmd07->fmd0703 != ($postData['fmd0703'] ?? '')) {
                $update['fmd0703'] = $postData['fmd0703'];
                $needsUpdate = true;
            }
            if ($fmd07->fmd0704 != $fmd0704) {
                $update['fmd0704'] = $fmd0704;
                $needsUpdate = true;
            }
            if ($fmd07->fmd0706 != $fmd0706) {
                $update['fmd0706'] = $fmd0706;
                $needsUpdate = true;
            }

            if ($needsUpdate) {
                $fmd07Model->updateData($update);
            }

            // Handle delete checkbox (remove fmd06 association)
            if ($deleteIds = $postData['delete'] ?? null) {
                foreach ($deleteIds as $fmd0601) {
                    $fmd06Model->updateData(['fmd0601' => $fmd0601, 'fmd0610' => null]);
                }
            }

            return $this->ajaxReturn("OK");
        }

        // Parse fmd0704 into array for checkboxes
        $fmd0704 = $fmd07->fmd0704 ?? '';
        $fmd0704_id = [];
        for ($i = 0; $i < strlen($fmd0704); $i++) {
            if ($fmd0704[$i] == '1') {
                $fmd0704_id[] = $i + 1;
            }
        }
        $fmd07->fmd0704 = $fmd0704_id;

        // Get field settings for this form
        $this->data['data'] = $fmd07;
        $this->data['fmd02s'] = $fmd02s;
        $this->data['fmd06s'] = $fmd06Model->getByFmd0701($id);
        $this->data['fmd04s'] = $fmd04Model->getDisplayNameByFmd0701($id);

        // Prepare date restriction options
        $cod02Model = model('Cod02Model');
        $this->data['week'] = $cod02Model->getOptionArrayByCode('week2');
        $day = [];
        for ($i = 1; $i <= 31; $i++) {
            $day[sprintf('%02d', $i)] = $i;
        }
        $this->data['day'] = $day;
        $this->data['fmd01'] = $this->fmd01Model->find($fmd07->fmd0702);

        // Parse fmd0706 string
        $this->data['fmd0706_t'] = 'i';
        $this->data['fmd0706_i'] = [];
        $this->data['fmd0706_w'] = [];
        $this->data['fmd0706_d'] = [];
        if ($fmd07->fmd0706) {
            $re = "/([wdim][\d]+)/";
            preg_match_all($re, $fmd07->fmd0706, $matches);
            foreach ($matches[0] as $item) {
                switch ($item[0]) {
                    case 'd':
                        $this->data['fmd0706_d'] = str_split(substr($item, 1), 2);
                        break;
                    case 'm':
                        $this->data['fmd0706_i'] = str_split(substr($item, 1));
                        $this->data['fmd0706_t'] = 'm';
                        break;
                    case 'i':
                        $this->data['fmd0706_i'] = str_split(substr($item, 1));
                        $this->data['fmd0706_t'] = 'i';
                        break;
                    case 'w':
                        $this->data['fmd0706_w'] = str_split(substr($item, 1));
                        break;
                }
            }
        }

        return $this->render();
    }

    /**
     * 新增電子表單到巡檢點
     */
    public function addFmd0906(int $fmd0901): string|\CodeIgniter\HTTP\ResponseInterface
    {
        $fmd09Model = model('Fmd09Model');

        if ($postData = $this->request->getPost()) {
            if (!isset($postData['fmd0906']) || empty($postData['fmd0906'])) {
                return $this->ajaxReturn(lang('FormItem.fun_add_fmd0906_err_msg'));
            }

            if ($fmd09Model->addFmd0906($postData['fmd0901'], $postData['fmd0906'])) {
                return $this->ajaxReturn("OK");
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }

        $fmd09 = $fmd09Model->find($fmd0901);
        if (!$fmd09) {
            $this->data['message'] = lang('FormItem.fun_add_fmd0906_not_found');
            return view('layout/error_dialog', $this->data);
        }

        // Get form IDs already assigned to patrol points
        $formIds = $fmd09Model->getFormIdByFmd0101($fmd09->fmd0902);
        $formIdStr = implode(',', $formIds);

        $fmd07Model = model('Fmd07Model');
        $option = ['fmd0702' => $fmd09->fmd0902];
        if ($formIdStr) {
            $option['*fmd0701'] = "fmd0701 not in ({$formIdStr})";
        }

        $fmd07s = $fmd07Model->getBy($option);
        if ($fmd07s) {
            $this->data['fmd0901'] = $fmd0901;
            $this->data['fmd07s'] = $fmd07s;
            return $this->render();
        } else {
            $this->data['message'] = lang('FormItem.fun_add_fmd0906_no_form');
            return view('layout/error_dialog', $this->data);
        }
    }

    /**
     * 編輯巡檢點
     */
    public function editFmd09(int $id): string|\CodeIgniter\HTTP\ResponseInterface
    {
        $fmd09Model = model('Fmd09Model');

        if ($postData = $this->request->getPost()) {
            // 驗證
            $rules = [
                'fmd0904' => 'required|integer',
                'fmd0908' => 'required',
                'fmd0910' => 'permit_empty|numeric',
                'fmd0911' => 'permit_empty|numeric',
            ];

            if (!$this->validate($rules)) {
                return $this->ajaxReturn($this->validator->getErrors());
            }

            // 處理空值
            if ($postData['fmd0910'] === '') {
                $postData['fmd0910'] = null;
            }
            if ($postData['fmd0911'] === '') {
                $postData['fmd0911'] = null;
            }

            $postData['fmd0901'] = $id;
            if ($fmd09Model->updateData($postData)) {
                return $this->ajaxReturn("OK", ['id' => $id]);
            }
            return $this->ajaxReturn($this->message->output());
        }

        $this->data['data'] = $fmd09Model->find($id);
        return $this->render();
    }

    /**
     * 刪除巡檢點
     */
    public function deleteFmd09(): string|\CodeIgniter\HTTP\ResponseInterface
    {
        if ($id = $this->request->getPost('id')) {
            $fmd09Model = model('Fmd09Model');
            if ($fmd09Model->delete($id)) {
                return $this->ajaxReturn("OK");
            }
            return $this->ajaxReturn($this->message->output());
        }
        return $this->ajaxReturn("ID is required");
    }

    /**
     * 刪除巡檢點的電子表單
     */
    public function deleteFmd0906(): string|\CodeIgniter\HTTP\ResponseInterface
    {
        if ($id = $this->request->getPost('id')) {
            $fmd09Model = model('Fmd09Model');
            $ids = explode('-', $id);
            if (count($ids) === 2 && $fmd09Model->deleteFmd0906((int)$ids[0], (int)$ids[1])) {
                return $this->ajaxReturn("OK");
            }
            return $this->ajaxReturn($this->message->output());
        }
        return $this->ajaxReturn("ID is required");
    }

    /**
     * 複製欄位設定
     */
    public function copyFmd06(): string|\CodeIgniter\HTTP\ResponseInterface
    {
        if ($postData = $this->request->getPost()) {
            $fmd06Model = model('Fmd06Model');
            $targets = explode('_', $postData['to']);
            $fmd06Model->copy((int)$postData['source'], $targets);
            return $this->ajaxReturn("OK");
        }
        return $this->ajaxReturn("Invalid request");
    }

    /**
     * 遞迴刪除巡檢項目
     */
    public function deleteFmd04(): \CodeIgniter\HTTP\ResponseInterface
    {
        if ($id = $this->request->getPost('id')) {
            $fmd04Model = model('Fmd04Model');
            $fmd04 = $fmd04Model->find($id);
            if ($fmd04Model->deleteRecursive((int)$id)) {
                // 如果都刪光了，加一筆空白的
                if ($fmd04Model->countBy(['fmd0402' => $fmd04->fmd0402, '*fmd0407' => 'fmd0407 <> 3']) == 0) {
                    $fmd04Model->add((int)$fmd04->fmd0402, null, 1, []);
                }
                return $this->ajaxReturn("OK");
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn("ID is required");
    }

    /**
     * 複製巡檢項目
     */
    public function copyFmd04(): \CodeIgniter\HTTP\ResponseInterface
    {
        if ($sourceId = $this->request->getPost('source')) {
            $fmd04Model = model('Fmd04Model');
            if ($fmd04Model->duplicate((int)$sourceId)) {
                return $this->ajaxReturn("OK");
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn("Source ID is required");
    }

    /**
     * 調整巡檢項目排序
     */
    public function orderFmd04(): \CodeIgniter\HTTP\ResponseInterface
    {
        $id = $this->request->getPost('id');
        $direction = $this->request->getPost('direction');
        log_message('debug', "FormItem::orderFmd04() - id: {$id}, direction: {$direction}");

        if ($id && $direction) {
            $fmd04Model = model('Fmd04Model');
            $fmd04Model->order((int)$id, (int)$direction);
            log_message('debug', "FormItem::orderFmd04() - order completed");
            return $this->ajaxReturn("OK");
        }
        log_message('debug', "FormItem::orderFmd04() - missing id or direction");
        return $this->ajaxReturn("ID and direction are required");
    }

    /**
     * 新增一行巡檢項目（在選取項目之後）
     */
    public function addFmd04(int $selectId): string|\CodeIgniter\HTTP\ResponseInterface
    {
        $fmd04Model = model('Fmd04Model');
        $selectNode = $fmd04Model->getRootNode($selectId);

        if ($selectNode) {
            $fmd03Model = model('Fmd03Model');
            $this->data['fmd03s'] = $fmd03Model->getByFmd0101((int)$selectNode->fmd0402);

            if ($postData = $this->request->getPost()) {
                if ($fmd04Model->add((int)$selectNode->fmd0402, null, (int)$selectNode->fmd0408 + 1, $postData)) {
                    return $this->ajaxReturn("OK");
                } else {
                    return $this->ajaxReturn($this->message->output());
                }
            }

            $this->data['fmd0402'] = $selectNode->fmd0402;
            return $this->render();
        }

        return $this->ajaxReturn("Node not found");
    }

    /**
     * 編輯 E-Mail 通知清單
     */
    public function editFmd31(?int $id = null): string|\CodeIgniter\HTTP\ResponseInterface
    {
        $fmd31Model = model('Fmd31Model');
        $sys01Model = model('Sys01Model');
        $ent10Model = model('Ent10Model');

        if ($postData = $this->request->getPost()) {
            log_message('info', '[' . get_class($this) . '] ' . json_encode($postData));

            $rules = ['fmd0101' => 'required'];
            if ($this->validate($rules)) {
                $fmd01 = $this->fmd01Model->getBy(['fmd0101' => $postData['fmd0101']], 1);
                if ($fmd01) {
                    $_fmd31 = new \stdClass();
                    $_fmd31->fmd3102 = $fmd01->fmd0106;

                    $_fmd3103 = new \stdClass();
                    $_fmd3103->ERROR = 0;
                    $_fmd3103->MISS = 0;

                    if (isset($postData['fmd3103']) && is_array($postData['fmd3103'])) {
                        foreach ($postData['fmd3103'] as $fmd3103) {
                            switch ($fmd3103) {
                                case '1':
                                    $_fmd3103->ERROR = 1;
                                    break;
                                case '2':
                                    $_fmd3103->MISS = 1;
                                    break;
                            }
                        }
                    }
                    $_fmd31->fmd3103 = json_encode($_fmd3103);

                    $data = [];
                    $sys0101v = explode(',', $postData['sys0101v'] ?? '');
                    foreach ($sys0101v as $sys0101) {
                        if (empty($sys0101)) {
                            continue;
                        }
                        $sys01s = $sys01Model->getNameArray('sys0101', 'sys0103,sys0104,sys0107', null, ['sys0101' => $sys0101, 'sys0108' => 1], 'sys0103,sys0104');
                        foreach ($sys01s as $_sys0101 => $val) {
                            $_fields = explode(' ', $val);
                            $_sys01 = new \stdClass();
                            $_sys01->sys0101 = $_sys0101;
                            $_sys01->name = $_fields[0] . ' ' . ($_fields[1] ?? '');
                            $_sys01->email = $_fields[2] ?? '';
                            $data[] = $_sys01;
                        }
                    }
                    $_fmd31->fmd3104 = json_encode($data);
                    log_message('info', '[' . get_class($this) . '] fmd31: ' . json_encode($_fmd31));

                    if (count($data) < 1) {
                        // 刪除
                        $fmd31 = $fmd31Model->getBy(['fmd3102' => $_fmd31->fmd3102], 1);
                        if ($fmd31) {
                            $fmd31Model->delete($fmd31->fmd3101);
                            log_message('info', '[' . get_class($this) . '] delete id: ' . $fmd31->fmd3101 . ', fmd3102: ' . $_fmd31->fmd3102);
                        }
                    } else {
                        $fmd31 = $fmd31Model->getBy(['fmd3102' => $_fmd31->fmd3102], 1);
                        if ($fmd31) {
                            // 更新
                            $_fmd31->fmd3101 = $fmd31->fmd3101;
                            $fmd31Model->updateData((array)$_fmd31);
                            log_message('info', '[' . get_class($this) . '] update fmd3101: ' . $_fmd31->fmd3101);
                        } else {
                            // 新增
                            $_id = $fmd31Model->insertData((array)$_fmd31);
                            log_message('info', '[' . get_class($this) . '] insert id: ' . $_id);
                        }
                    }
                }
                return $this->ajaxReturn("OK", ['id' => $id]);
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }

        $ent0101 = $this->session->get('ent0101');
        $this->data['ent1001'] = false;

        $sys01 = $sys01Model->find($this->user->id);
        if ($sys01) {
            $ent10 = $ent10Model->getBy(['ent1001' => $sys01->sys0110], 1);
            if ($ent10) {
                $this->data['ent1001'] = $ent10->ent1001;
            }
        }

        $this->data['data'] = new \stdClass();
        $fmd01 = $this->fmd01Model->getBy(['fmd0101' => $id, 'fmd0108' => 2], 1);
        if ($fmd01) {
            $fmd31 = $fmd31Model->getBy(['fmd3102' => $fmd01->fmd0106], 1);
            if ($fmd31) {
                $this->data['data']->fmd0101 = $fmd01->fmd0101;
                $fmd3103 = json_decode($fmd31->fmd3103);
                $this->data['data']->fmd3103 = [1 => $fmd3103->ERROR ?? 0, 2 => $fmd3103->MISS ?? 0];
                $this->data['data']->fmd3104 = json_decode($fmd31->fmd3104) ?: [];
            } else {
                $this->data['data']->fmd0101 = $id;
                $this->data['data']->fmd3103 = [1 => 0, 2 => 0];
                $this->data['data']->fmd3104 = [];
            }
        } else {
            $this->data['data']->fmd0101 = $id;
            $this->data['data']->fmd3103 = [1 => 0, 2 => 0];
            $this->data['data']->fmd3104 = [];
        }

        $_errType = [1 => '異常', 2 => '漏檢'];
        $errTypes = [];
        foreach ($_errType as $_id => $name) {
            $errType = new \stdClass();
            $errType->id = $_id;
            $errType->name = $name;
            $errTypes[] = $errType;
        }
        $this->data['errTypes'] = $errTypes;

        $option = ['ent1002' => $ent0101];
        $this->data['ent10s'] = $ent10Model->getBy($option);

        $option = ['ent1002' => $ent0101, 'sys0108' => 1, '*sys0102' => "sys0102 not in ('sysadmin','admin')"];
        if (isset($ent10) && $ent10 && !empty($ent10->ent1001)) {
            $option['*sys0110'] = 'sys0110=' . $ent10->ent1001;
        }
        $this->data['sys01s'] = $sys01Model->getBy($option);

        return $this->render();
    }

    /**
     * 根據部門編號取得帳戶和裝置
     */
    public function getsys012dev01(int $ent1001): \CodeIgniter\HTTP\ResponseInterface
    {
        $sys01Model = model('Sys01Model');
        $ent10Model = model('Ent10Model');
        $ent0101 = $this->session->get('ent0101');
        $result = ['sys01' => []];

        // 取得所有下級部門編號
        $depId = $ent10Model->getSubDepartmentId($ent1001);
        $depId = empty($depId) ? (string)$ent1001 : $ent1001 . ',' . $depId;

        if ($depId) {
            $result['sys01'] = $sys01Model->getBy([
                'ent1002' => $ent0101,
                '*sys0110' => "sys0110 in ({$depId})",
                '*sys0102' => "sys0102 not in ('sysadmin','admin')"
            ]);
        } elseif ($ent1001 == 0) {
            $result['sys01'] = $sys01Model->getBy([
                'ent1002' => $ent0101,
                '*sys0102' => "sys0102 not in ('sysadmin','admin')"
            ]);
        }

        return $this->response->setJSON($result);
    }
}
