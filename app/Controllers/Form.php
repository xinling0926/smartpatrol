<?php

namespace App\Controllers;

/**
 * Form Controller - 表單管理
 */
class Form extends AdminController
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
        $option = ['*fmd0108' => 'fmd0108<=2'];
        $this->setQueryOption($option);
        $option['ent1002'] = $this->session->get('ent0101');

        $this->data['fmd0108'] = '';

        $pageSize = $this->getPageSize();
        $totalRows = $this->fmd01Model->countBy($option);
        $this->data['data'] = $this->fmd01Model->getBy($option, $pageSize);
        $this->data['offset'] = 0;
        $this->setPage($totalRows, 1, $pageSize);

        $cod02Model = model('Cod02Model');
        $this->data['fmd0108_opt'] = $cod02Model->getOptionArrayByCode('fmd0108', ['' => lang('Form.index_select_state_default')]);
        unset($this->data['fmd0108_opt'][3]);
        $this->data['fmd0105_opt'] = $cod02Model->getOptionArrayByCode('fmd0105');

        $ent10Model = model('Ent10Model');
        $this->data['dep_opt'] = $ent10Model->getNameArray('ent1001', 'ent1004', ['' => lang('Form.index_select_dep_default')], ['ent1002' => $this->session->get('ent0101'), 'ent1007' => 1]);

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
                $search = $this->db->escapeLikeString($option['search']);
                $option['*search'] = "(fmd0103 like '%{$search}%' or fmd0104 like '%{$search}%')";
                unset($option['search']);
            }

            if (empty($option['fmd0108'])) {
                $option['*fmd0108'] = 'fmd0108<=2';
            }

            $this->setQueryOption($option);
        }

        $option['ent1002'] = $this->session->get('ent0101');

        $pageSize = $this->getPageSize();
        $offset = ($page - 1) * $pageSize;
        $this->data['offset'] = $offset;
        $totalRows = $this->fmd01Model->countBy($option);
        $this->data['data'] = $this->fmd01Model->getBy($option, $pageSize, $offset);
        $this->setPage($totalRows, $page, $pageSize);

        $cod02Model = model('Cod02Model');
        $this->data['fmd0105_opt'] = $cod02Model->getOptionArrayByCode('fmd0105');
        $this->data['fmd0108_opt'] = $cod02Model->getOptionArrayByCode('fmd0108');

        return $this->render('');
    }

    public function detail(int $id): string
    {
        $this->data['data'] = $this->fmd01Model->find($id);

        $cod02Model = model('Cod02Model');
        $this->data['fmd0105_opt'] = $cod02Model->getOptionArrayByCode('fmd0105');
        $this->data['fmd0108_opt'] = $cod02Model->getOptionArrayByCode('fmd0108');
        $this->data['data_type_opt'] = $cod02Model->getOptionArrayByCode('data_type');

        $fmd02Model = model('Fmd02Model');
        $fmd03Model = model('Fmd03Model');
        $fmd05Model = model('Fmd05Model');

        $this->data['fmd02s'] = $fmd02Model->getByFmd0101($id);
        $this->data['fmd03s'] = $fmd03Model->getByFmd0101($id);
        $this->data['fmd05s'] = $fmd05Model->getByFmd0101($id);

        return $this->render();
    }

    public function edit(int $id = 0): string|\CodeIgniter\HTTP\ResponseInterface
    {
        if ($this->request->is('post')) {
            $postData = $this->request->getPost();

            $rules = [];
            if (empty($postData['fmd0101'])) {
                $rules['fmd0102'] = 'required';
            }
            $rules['fmd0104'] = 'required';

            if ($this->validate($rules)) {
                if (empty($postData['fmd0101'])) {
                    // Insert
                    $postData['fmd0107'] = 1;
                    $postData['fmd0108'] = 0;

                    if ($fmd0101 = $this->fmd01Model->insert($postData)) {
                        $this->fmd01Model->updateFmd0106($fmd0101);
                        return $this->ajaxReturn('OK', ['id' => $fmd0101, 'title' => $postData['fmd0104']]);
                    }
                } else {
                    // Update
                    if ($this->fmd01Model->save($postData)) {
                        return $this->ajaxReturn('OK', ['id' => $id, 'title' => $postData['fmd0104']]);
                    }
                }
            }

            return $this->ajaxReturn($this->message->output());
        }

        // GET request
        $ent10Model = model('Ent10Model');
        $this->data['dept'] = $ent10Model->getNameArray('ent1001', 'ent1004', ['' => ''], ['ent1002' => $this->session->get('ent0101'), 'ent1007' => 1]);

        $cod02Model = model('Cod02Model');
        $this->data['fmd0105_opt'] = $cod02Model->getOptionArrayByCode('fmd0105');
        $this->data['fmd0108_opt'] = $cod02Model->getOptionArrayByCode('fmd0108', ['' => '']);

        if ($id) {
            $this->data['data'] = $this->fmd01Model->find($id);
        } else {
            $this->data['data'] = null;
        }

        return $this->render();
    }

    public function editFmd02(int $id = 0): string|\CodeIgniter\HTTP\ResponseInterface
    {
        $fmd02Model = model('Fmd02Model');

        if ($this->request->is('post')) {
            $postData = $this->request->getPost();

            $rules = [
                'fmd0203' => 'required|numeric',
                'fmd0204' => 'required',
            ];

            if ($this->validate($rules)) {
                if (empty($postData['fmd0201'])) {
                    if ($newId = $fmd02Model->insert($postData)) {
                        return $this->ajaxReturn('OK', ['id' => $newId, 'title' => $postData['fmd0204']]);
                    }
                } else {
                    if ($fmd02Model->save($postData)) {
                        return $this->ajaxReturn('OK', ['id' => $id, 'title' => $postData['fmd0204']]);
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

    public function editFmd03(int $id = 0): string|\CodeIgniter\HTTP\ResponseInterface
    {
        $fmd03Model = model('Fmd03Model');

        if ($this->request->is('post')) {
            $postData = $this->request->getPost();

            $rules = [
                'fmd0303' => 'required|numeric',
                'fmd0304' => 'required',
                'fmd0305' => 'required|numeric',
            ];

            if ($this->validate($rules)) {
                if (empty($postData['fmd0301'])) {
                    if ($fmd0301 = $fmd03Model->insert($postData)) {
                        return $this->ajaxReturn('OK', ['id' => $fmd0301, 'title' => $postData['fmd0304']]);
                    }
                } else {
                    if ($fmd03Model->save($postData)) {
                        return $this->ajaxReturn('OK', ['id' => $id, 'title' => $postData['fmd0304']]);
                    }
                }
            }

            return $this->ajaxReturn($this->message->output());
        }

        if ($id) {
            $this->data['data'] = $fmd03Model->find($id);
        } else {
            $this->data['data'] = null;
            $this->data['fmd0302'] = $this->request->getGet('parent');
        }

        return $this->render();
    }

    public function editFmd05(int $id = 0): string|\CodeIgniter\HTTP\ResponseInterface
    {
        $fmd05Model = model('Fmd05Model');

        if ($this->request->is('post')) {
            $postData = $this->request->getPost();

            $rules = [
                'fmd0503' => 'required|numeric',
                'fmd0504' => 'required',
                'fmd0505' => 'required',
            ];

            if ($this->validate($rules)) {
                // Process data based on type
                $fmd0505 = (int)$postData['fmd0505'];
                switch ($fmd0505) {
                    case 1:
                    case 2:
                        $postData['fmd0512'] = ($postData['fmd0512_2'] ?? '') . '~' . ($postData['fmd0512_3'] ?? '');
                        $postData['fmd0506'] = 10;
                        break;
                    case 3:
                        $postData['fmd0512'] = $postData['fmd0512_1'] ?? '';
                        break;
                    case 4:
                        $postData['fmd0512'] = $postData['fmd0512_1'] ?? '';
                        $postData['fmd0506'] = 20;
                        break;
                    case 5:
                        $postData['fmd0509'] = $postData['fmd0509_1'] ?? '';
                        $postData['fmd0512'] = $postData['fmd0512_1'] ?? '';
                        $postData['fmd0506'] = 20;
                        break;
                    case 6:
                        $postData['fmd0509'] = $postData['fmd0509_1'] ?? '';
                        $postData['fmd0512'] = '';
                        break;
                    case 7:
                        $postData['fmd0512'] = $postData['fmd0512_1'] ?? '';
                        $postData['fmd0506'] = 20;
                        break;
                    case 8:
                        $postData['fmd0508'] = ($postData['fmd0508_1'] ?? '') . PHP_EOL . ($postData['fmd0508_2'] ?? '');
                        if (($postData['fmd0509_2'] ?? '0') === '0') {
                            $postData['fmd0509'] = $postData['fmd0508_1'] ?? '';
                        } else {
                            $postData['fmd0509'] = $postData['fmd0508_2'] ?? '';
                        }
                        $postData['fmd0512'] = '';
                        $postData['fmd0506'] = 10;
                        break;
                    case 9:
                        $postData['fmd0506'] = 20;
                        break;
                }

                if (empty($postData['fmd0501'])) {
                    if ($newId = $fmd05Model->insert($postData)) {
                        return $this->ajaxReturn('OK', ['id' => $newId, 'title' => $postData['fmd0504']]);
                    }
                } else {
                    if ($fmd05Model->save($postData)) {
                        return $this->ajaxReturn('OK', ['id' => $id, 'title' => $postData['fmd0504']]);
                    }
                }
            }

            return $this->ajaxReturn($this->message->output());
        }

        // GET request
        if ($id) {
            $this->data['data'] = $fmd05Model->find($id);
            $data = &$this->data['data'];

            $data->fmd0508_1 = lang('Form.v_fmd0508_1');
            $data->fmd0508_2 = lang('Form.v_fmd0508_2');
            $data->fmd0509_1 = $data->fmd0509;
            $data->fmd0509_2 = 0;
            $data->fmd0512_1 = $data->fmd0512;
            $data->fmd0512_2 = '';
            $data->fmd0512_3 = '';

            switch ((int)$data->fmd0505) {
                case 1:
                case 2:
                    $data->fmd0512_1 = '';
                    $fmd0512 = explode('~', $data->fmd0512);
                    if (count($fmd0512) === 2) {
                        $data->fmd0512_2 = $fmd0512[0];
                        $data->fmd0512_3 = $fmd0512[1];
                    }
                    $data->fmd0509_1 = '';
                    break;
                case 5:
                case 6:
                    $data->fmd0509_1 = $data->fmd0509;
                    $opt = explode(PHP_EOL, $data->fmd0509);
                    $data->fmd0509 = $opt[0];
                    break;
                case 8:
                    $opt = explode(PHP_EOL, $data->fmd0508);
                    $data->fmd0508_1 = $opt[0] ?? '';
                    $data->fmd0508_2 = $opt[1] ?? '';
                    $data->fmd0509_2 = array_search($data->fmd0509, $opt);
                    $data->fmd0509_1 = $data->fmd0509;
                    $data->fmd0509 = '';
                    break;
            }
        } else {
            $this->data['data'] = null;
            $this->data['fmd0502'] = $this->request->getGet('parent');
        }

        $cod02Model = model('Cod02Model');
        $this->data['data_type_opt'] = $cod02Model->getOptionArrayByCode('data_type');

        return $this->render();
    }

    public function state(): \CodeIgniter\HTTP\ResponseInterface
    {
        $id = $this->request->getPost('id');
        $state = $this->request->getPost('s');

        if ($id) {
            if ($this->fmd01Model->changeFormState($id, $state)) {
                return $this->ajaxReturn('OK');
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }

        return $this->ajaxReturn('No ID provided');
    }

    public function delete(): \CodeIgniter\HTTP\ResponseInterface
    {
        $id = $this->request->getPost('id');
        if ($id) {
            if ($this->fmd01Model->delete($id)) {
                $this->deleteFmd05();
                $this->deleteFmd03();
                $this->deleteFmd02();
                return $this->ajaxReturn('OK');
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn('No ID provided');
    }

    public function deleteFmd02(): \CodeIgniter\HTTP\ResponseInterface
    {
        $id = $this->request->getPost('id');
        if ($id) {
            $fmd02Model = model('Fmd02Model');
            if ($fmd02Model->delete($id)) {
                return $this->ajaxReturn('OK');
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn('No ID provided');
    }

    public function deleteFmd03(): \CodeIgniter\HTTP\ResponseInterface
    {
        $id = $this->request->getPost('id');
        if ($id) {
            $fmd03Model = model('Fmd03Model');
            if ($fmd03Model->delete($id)) {
                return $this->ajaxReturn('OK');
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn('No ID provided');
    }

    public function deleteFmd05(): \CodeIgniter\HTTP\ResponseInterface
    {
        $id = $this->request->getPost('id');
        if ($id) {
            $fmd05Model = model('Fmd05Model');
            if ($fmd05Model->delete($id)) {
                return $this->ajaxReturn('OK');
            } else {
                return $this->ajaxReturn($this->message->output());
            }
        }
        return $this->ajaxReturn('No ID provided');
    }
}
