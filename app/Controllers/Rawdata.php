<?php

namespace App\Controllers;

/**
 * Rawdata Controller - 巡檢紀錄查詢
 */
class Rawdata extends AdminController
{
    protected $dev01Model;
    protected $sys01Model;
    protected $pad01Model;
    protected $fmd08Model;
    protected $ent10Model;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper('language');
        $this->dev01Model = model('Dev01Model');
        $this->sys01Model = model('Sys01Model');
        $this->pad01Model = model('Pad01Model');
        $this->fmd08Model = model('Fmd08Model');
        $this->ent10Model = model('Ent10Model');
    }

    public function index(): string
    {
        $stime = microtime(true);
        $ent0101 = $this->session->get('ent0101');
        // 預設只查最近 7 天，避免全表掃描
        $defaultStart = date('Y-m-d', strtotime('-7 days'));
        $defaultEnd = date('Y-m-d');
        $options = [
            'ent1002' => $ent0101,
            'ent1007' => 1,
            'pad0109s' => $defaultStart,
            'pad0109e' => $defaultEnd,
        ];
        $pageSize = $this->getPageSize();
        $totalRows = $this->pad01Model->countBy($options);
        $etime = microtime(true);
        $this->data['time0'] = $etime - $stime;

        $this->data['data'] = $this->pad01Model->getBy(
            $options,
            $pageSize,
            0,
            'pad0109 desc',
            'ent1004,dev0104,fmd0804,fmd0204,fmd0908,pad0101,fmd0703,sys0103,sys0104,pad0109,pad0112'
        );
        $this->data['offset'] = 0;
        $this->setPage($totalRows, 1, $pageSize);
        $etime = microtime(true);
        $this->data['time1'] = $etime - $stime;

        $ent10Id = $this->ent10Model->getCurrentUserSeeDepartmentId();

        $this->data['ent10s'] = $this->ent10Model->getNameArray(
            'ent1001',
            'ent1004',
            ['' => lang('Rawdata.search_select_ent1001_default')],
            ['ent1002' => $ent0101, 'ent1007' => 1, '*ent1001' => "ent1001 in ({$ent10Id})"]
        );
        $this->data['dev01s'] = $this->dev01Model->getNameArray(
            'dev0101',
            'dev0104',
            ['' => lang('Rawdata.search_select_dev0101_default')],
            ['dev0102' => $ent0101, '*dev0103' => "(dev0103 is null or dev0103 in ({$ent10Id}))"],
            'dev0104'
        );
        $this->data['fmd08s'] = $this->fmd08Model->getFmd0804Array(
            ['' => lang('Rawdata.search_select_fmd0804_default')],
            ['*ent1001' => "ent1001 in ({$ent10Id})"]
        );
        $this->data['sys01s'] = $this->sys01Model->getNameArray(
            'sys0101',
            'sys0103,sys0104',
            ['' => lang('Rawdata.search_select_sys0101_default')],
            ['sys0205' => 1, 'sys0108' => 1, '*sys0110' => "sys0110 in ({$ent10Id})"]
        );

        $this->data['fmd07s'] = ['' => lang('Rawdata.search_select_fmd07s_default')];
        $this->data['fmd02s'] = ['' => lang('Rawdata.search_select_fmd02s_default')];
        $this->data['orders'] = [
            '' => lang('Rawdata.search_select_orders_default'),
            'pad0109 desc' => lang('Rawdata.search_select_orders_v_1'),
            'fmd0104 asc' => lang('Rawdata.search_select_orders_v_2'),
            'fmd0804 asc' => lang('Rawdata.search_select_orders_v_3'),
            'fmd0703 asc' => lang('Rawdata.search_select_orders_v_4'),
            'fmd0908 asc' => lang('Rawdata.search_select_orders_v_5'),
            'sys0103 asc' => lang('Rawdata.search_select_orders_v_6'),
        ];

        $etime = microtime(true);
        $this->data['time2'] = $etime - $stime;
        $this->data['options'] = $options;

        return $this->render();
    }

    public function query(int $page = 0): string
    {
        $ent0101 = $this->session->get('ent0101');
        if ($page) {
            $option = $this->loadQueryOption();
        } else {
            $page = 1;
            $option = $this->getQueryOption();
        }

        foreach ($option as $k1 => $v1) {
            if ($v1 === '') {
                unset($option[$k1]);
            }
        }

        // 沒指定日期範圍時，預設查最近 7 天，避免全表掃描
        if (!isset($option['pad0109s']) && !isset($option['pad0109e'])) {
            $option['pad0109s'] = date('Y-m-d', strtotime('-7 days'));
            $option['pad0109e'] = date('Y-m-d');
        }

        $orders = 'pad0109 desc';
        if (isset($option['orders']) && !empty($option['orders'])) {
            $sorts = ['fmd0104 asc', 'fmd0804 asc', 'fmd0703 asc', 'fmd0908 asc', 'sys0103 asc', 'pad0109 desc'];
            $orders = in_array($option['orders'], $sorts) ? $option['orders'] : 'pad0109 desc';
            unset($option['orders']);
        }

        $option['ent1002'] = $ent0101;
        $option['ent1007'] = 1;
        $pageSize = $this->getPageSize();
        $offset = ($page - 1) * $pageSize;
        $this->data['offset'] = $offset;
        $totalRows = $this->pad01Model->countBy($option);
        $this->data['data'] = $this->pad01Model->getBy(
            $option,
            $pageSize,
            $offset,
            $orders,
            'ent1004,dev0104,fmd0804,fmd0204,fmd0908,pad0101,fmd0703,sys0103,sys0104,pad0109,pad0112'
        );
        $this->setPage($totalRows, $page, $pageSize);

        return $this->render('');
    }

    public function detail(int $id): string
    {
        $tag01s = $this->pad01Model->get($id);
        $tag01s->pics = $this->pad01Model->getPad02s($id);
        $tag01s->pad0107 = json_decode($tag01s->pad0107, true);
        $juli = '';
        if ($tag01s->pad0110 > 10 && $tag01s->pad0111 > 10 && $tag01s->fmd0910 > 10 && $tag01s->fmd0911 > 10) {
            $juli = (int)$this->getDistance($tag01s->pad0110, $tag01s->pad0111, $tag01s->fmd0910, $tag01s->fmd0911);
            $juli = $juli > 1000 ? ($juli / 1000) . 'km' : $juli . 'm';
        }
        $tag01s->juli = $juli;
        $this->data['data'] = $tag01s;

        $fmd07Model = model('Fmd07Model');
        $fmd01Model = model('Fmd01Model');
        $fmd02Model = model('Fmd02Model');
        $isoModel = model('IsoModel');

        $fmd07s = $fmd07Model->find($tag01s->pad0102);
        $fmd01s = $fmd01Model->find($fmd07s->fmd0702);

        if ($fmd02Data = $fmd02Model->find($tag01s->pad0105)) {
            $fmd02s = $fmd02Data;
        } else {
            $fmd02s = new \stdClass();
            $fmd02s->fmd0203 = 0;
            $fmd02s->fmd0204 = '';
        }

        $fmd01 = $fmd01Model->find($fmd01s->fmd0106);
        $isoModel->setTableName($fmd01);

        $date = date("Y-m-d", strtotime($tag01s->pad0109));
        $isoDate = $isoModel->isoDate($fmd01->fmd0105, $date);

        $masterId = null;
        if ($isos = $isoModel->getMasterByDate($isoDate)) {
            $masterId = $isos->id;
        }

        if (!isset($masterId)) {
            $this->data['param'] = '';
            $this->data['commentRaws'] = [];
            return $this->render();
        }

        $tmp = new \stdClass();
        $tmp->fmd0106 = $fmd01s->fmd0106;
        $tmp->fmd0201 = $tag01s->pad0105;
        $tmp->fmd0203 = $fmd02s->fmd0203;
        $tmp->fmd0204 = $fmd02s->fmd0204;
        $tmp->fmd0701 = $tag01s->pad0102;
        $tmp->master_id = $masterId;
        $this->data['param'] = urlencode(json_encode($tmp));

        $commentRaws = [];
        if ($reportMaster = $isoModel->getMasterOnly($masterId)) {
            if ($comments = json_decode($reportMaster->comments)) {
                if (isset($comments->{$tag01s->pad0105}->{$tag01s->pad0102})) {
                    $commentRaws = $comments->{$tag01s->pad0105}->{$tag01s->pad0102};
                    krsort($commentRaws);
                }
            }
        }
        $this->data['commentRaws'] = $commentRaws;

        return $this->render();
    }

    public function detailDialog(int $id, ?int $masterId = null): string
    {
        $tag01s = $this->pad01Model->get($id);
        $tag01s->pics = $this->pad01Model->getPad02s($id);
        $tag01s->pad0107 = json_decode($tag01s->pad0107, true);

        $juli = '';
        if ($tag01s->pad0110 > 10 && $tag01s->pad0111 > 10 && $tag01s->fmd0910 > 10 && $tag01s->fmd0911 > 10) {
            $juli = (int)$this->getDistance($tag01s->pad0110, $tag01s->pad0111, $tag01s->fmd0910, $tag01s->fmd0911);
            $juli = $juli > 1000 ? ($juli / 1000) . 'km' : $juli . 'm';
        }
        $tag01s->juli = $juli;
        $this->data['data'] = $tag01s;

        $fmd07Model = model('Fmd07Model');
        $fmd01Model = model('Fmd01Model');
        $fmd02Model = model('Fmd02Model');
        $isoModel = model('IsoModel');

        $fmd07s = $fmd07Model->find($tag01s->pad0102);
        $fmd01s = $fmd01Model->find($fmd07s->fmd0702);

        if ($fmd02s = $fmd02Model->find($tag01s->pad0105)) {
            // Has shift
        } else {
            $fmd02s = new \stdClass();
            $fmd02s->fmd0203 = '';
            $fmd02s->fmd0204 = '';
        }

        $tmp = new \stdClass();
        $tmp->fmd0106 = $fmd01s->fmd0106;
        $tmp->fmd0201 = $tag01s->pad0105;
        $tmp->fmd0203 = $fmd02s->fmd0203;
        $tmp->fmd0204 = $fmd02s->fmd0204;
        $tmp->fmd0701 = $tag01s->pad0102;
        $tmp->master_id = $masterId;
        $this->data['param'] = urlencode(json_encode($tmp));

        $fmd01 = $fmd01Model->find($fmd01s->fmd0106);
        $isoModel->setTableName($fmd01);

        $commentRaws = [];
        if ($reportMaster = $isoModel->getMasterOnly($masterId)) {
            if ($comments = json_decode($reportMaster->comments)) {
                if ($tag01s->pad0105 == 0) {
                    $tag01s->pad0105 = 'all';
                }
                if (isset($comments->{$tag01s->pad0105}->{$tag01s->pad0102})) {
                    $commentRaws = $comments->{$tag01s->pad0105}->{$tag01s->pad0102};
                    krsort($commentRaws);
                }
            }
        }
        $this->data['commentRaws'] = $commentRaws;

        return $this->render();
    }

    public function detailDialogMiss(string $jsonData = ''): string
    {
        if (empty($jsonData)) {
            $jsonData = $this->request->getGet('data') ?? '';
        }
        $tmp = urldecode($jsonData);
        $tmpJson = json_decode($tmp);

        $tag01s = new \stdClass();
        $tag01s->ent1004 = '';
        $tag01s->dev0104 = '';
        $tag01s->fmd0804 = '';
        $tag01s->fmd0203 = '';
        $tag01s->fmd0204 = '';
        $tag01s->fmd0908 = '';
        $tag01s->fmd0701 = '';
        $tag01s->fmd0703 = '';
        $tag01s->pad0109 = '';
        $tag01s->sys0103 = '';
        $tag01s->sys0104 = '';
        $tag01s->pad0112 = '';
        $tag01s->pad0107 = '';
        $tag01s->juli = '';
        $tag01s->fmd0106 = '';

        $fmd01Model = model('Fmd01Model');
        $fmd02Model = model('Fmd02Model');
        $fmd04Model = model('Fmd04Model');
        $fmd05Model = model('Fmd05Model');
        $isoModel = model('IsoModel');

        if (!empty($tmpJson->fmd0201) && $tmpJson->fmd0201 !== 'all') {
            $fmd02s = $fmd02Model->getBy(['fmd0201' => $tmpJson->fmd0201], 1);
        } else {
            $fmd02s = new \stdClass();
            $fmd02s->fmd0201 = 'all';
            $fmd02s->fmd0203 = '';
            $fmd02s->fmd0204 = '';
        }

        $fmd01s = $fmd01Model->find($tmpJson->fmd0101);
        $fmd01 = $fmd01Model->find($fmd01s->fmd0106);
        $isoModel->setTableName($fmd01);
        $tag01s->fmd0203 = $fmd02s->fmd0203;
        $tag01s->fmd0204 = $fmd02s->fmd0204;

        $sql = 'SELECT fmd0101, fmd0106, fmd0409, fmd0504, fmd0601, fmd0603, fmd0604, fmd0606, fmd0701, fmd0703, fmd0704, ent1004 FROM `fmd01`, `fmd04`, `fmd05`, `fmd06`, `fmd07`, `ent10` WHERE fmd0101 = fmd0602 AND fmd0401 = fmd0603 AND fmd0604 = fmd0501 AND fmd0610 = fmd0701 AND ent1001 = fmd0102 AND fmd0501 = ' . $tmpJson->fmd0501 . ' ORDER BY fmd0409 ASC';
        $fmd07s = $this->db->query($sql)->getResult();

        $commentRaws = [];
        $fmd0701 = null;

        if (count($fmd07s)) {
            $fmd0701 = $fmd07s[$tmpJson->line]->fmd0701 ?? null;

            $tag01s->fmd0701 = $fmd07s[$tmpJson->line]->fmd0701 ?? null;
            $tag01s->fmd0703 = $fmd07s[$tmpJson->line]->fmd0703 ?? null;
            $tag01s->ent1004 = $fmd07s[$tmpJson->line]->ent1004 ?? null;
            $tag01s->fmd0106 = $fmd07s[$tmpJson->line]->fmd0106 ?? null;

            $fmd0101 = $fmd07s[$tmpJson->line]->fmd0101 ?? null;

            $sql = "SELECT fmd0905, fmd0908, fmd0804 FROM fmd08, fmd09 WHERE fmd0906 LIKE '%{$fmd0701}%' AND fmd0802 = '{$fmd0101}' AND fmd0903 = fmd0801 LIMIT 1";
            if ($result2 = $this->db->query($sql)->getRow()) {
                $tag01s->fmd0804 = $result2->fmd0804;
                $tag01s->fmd0908 = $result2->fmd0908;
            }

            if ($reportMaster = $isoModel->getMasterOnly($tmpJson->master_id)) {
                if ($comments = json_decode($reportMaster->comments)) {
                    if (isset($comments->{$tmpJson->fmd0201}->{$fmd0701})) {
                        $commentRaws = $comments->{$tmpJson->fmd0201}->{$fmd0701};
                        krsort($commentRaws);
                    }
                }
            }
        }

        $pad0107 = [];
        foreach ($fmd07s as $key => $fmd07) {
            if ($fmd07->fmd0701 == $fmd0701 && $fmd0701 !== null) {
                $fmd0603 = $fmd07s[$key]->fmd0603 ?? null;
                $val = new \stdClass();
                if (substr($fmd07->fmd0704, ((int)$tag01s->fmd0203 - 1), 1) !== '0') {
                    $val->{"未檢"} = true;
                } else {
                    $val->{"限定班別"} = false;
                }

                $raw = new \stdClass();
                $raw->fmd0601 = $fmd07s[$tmpJson->line]->fmd0601 ?? null;
                $raw->fmd0606 = $fmd07s[$tmpJson->line]->fmd0606 ?? null;
                $prompt = '';
                if ($fmd0603) {
                    $prompt = $fmd04Model->getPatrolItemPrompt($fmd0603);
                }
                $raw->prompt = $prompt;
                $raw->fmd0504 = $fmd07s[$tmpJson->line]->fmd0504 ?? null;
                $raw->files = [];
                $raw->info = '';
                $raw->value = [$val];
                $pad0107[] = $raw;
            }
        }

        $tag01s->pad0107 = json_decode(json_encode($pad0107), true);
        $this->data['data'] = $tag01s;

        $tmp = new \stdClass();
        $tmp->fmd0106 = $tag01s->fmd0106;
        $tmp->fmd0201 = $tmpJson->fmd0201;
        $tmp->fmd0203 = $tag01s->fmd0203;
        $tmp->fmd0204 = $tag01s->fmd0204;
        $tmp->fmd0701 = $tag01s->fmd0701;
        $tmp->master_id = $tmpJson->master_id;
        $this->data['param'] = urlencode(json_encode($tmp));
        $this->data['commentRaws'] = $commentRaws;

        return $this->render();
    }

    public function detailAddComment(?string $param = null): string|\CodeIgniter\HTTP\ResponseInterface
    {
        $sys01Model = model('Sys01Model');
        $fmd01Model = model('Fmd01Model');
        $fmd02Model = model('Fmd02Model');
        $isoModel = model('IsoModel');
        $rec01Model = model('Rec01Model');

        if ($postData = $this->request->getPost()) {
            $rules = ['comments' => 'required'];

            if ($this->validate($rules)) {
                $fmd01 = $fmd01Model->find($postData['fmd0106']);
                $isoModel->setTableName($fmd01);

                $reportMaster = null;
                $reportDetail = null;

                if ($reportMaster = $isoModel->getMasterOnly($postData['master_id'])) {
                    $reportDetail = $isoModel->getDetailByMaster($reportMaster->id);
                }

                if (is_null($reportMaster) || is_null($reportDetail)) {
                    $this->message->add(lang('Rawdata.not_data_hint2'));
                    return $this->ajaxReturn('ERR', $this->message->output());
                }

                $sys01s = $sys01Model->getByIdentity($this->session->get('identity'));
                $commentMsg = sprintf("[%s] %s:\r\n%s", date("Y-m-d H:i:s"), $sys01s->sys0103 . $sys01s->sys0104, $postData['comments']);

                if (!empty($postData['auto_comment']) && !empty($commentMsg)) {
                    $commentMsg = '<font style="color: blue;">' . $commentMsg . '</font>';
                }

                if (isset($reportMaster->comments) && $reportMaster->comments) {
                    $comments = json_decode($reportMaster->comments);
                } else {
                    $comments = new \stdClass();
                }

                if (empty($postData['fmd0201'])) {
                    $postData['fmd0201'] = 'all';
                }

                if (!isset($comments->{$postData['fmd0201']}->{$postData['fmd0701']})) {
                    if (!isset($comments->{$postData['fmd0201']})) {
                        $comments->{$postData['fmd0201']} = new \stdClass();
                    }
                    $comments->{$postData['fmd0201']}->{$postData['fmd0701']} = [$commentMsg];
                } else {
                    $comments->{$postData['fmd0201']}->{$postData['fmd0701']}[] = $commentMsg;
                }

                $reportMaster->comments = json_encode($comments);
                $tableName = $isoModel->getTableName();
                if (isset($reportMaster->id)) {
                    $this->db->table($tableName)->where('id', $reportMaster->id)->update((array)$reportMaster);
                }

                // Auto comment propagation to future dates
                if (!empty($postData['auto_comment']) && !empty($commentMsg)) {
                    $insertData = [];
                    if ($fmd01s = $fmd01Model->getActiveByFmd0106($postData['fmd0106'])) {
                        $insertData['rec0102'] = $fmd01s->fmd0101;
                    } else {
                        $insertData['rec0102'] = $postData['fmd0106'];
                    }
                    $insertData['rec0103'] = $postData['fmd0203'] ?? 0;
                    $insertData['rec0104'] = $postData['fmd0701'];
                    $insertData['rec0105'] = date('Y-m-d');
                    $insertData['rec0106'] = $commentMsg;
                    $rec01Model->insert($insertData);

                    // Propagate comments to future dates
                    $beginDate = strtotime($reportMaster->date);
                    $beginFmd0203 = empty($postData['fmd0203']) ? 0 : $postData['fmd0203'];
                    $beginFlag = $reportMaster->date . '_' . $beginFmd0203;
                    $endDate = strtotime(date('Y-m-d'));

                    if ($endFmd02 = $fmd02Model->getCurrentDaypart($reportMaster->fmd0101)) {
                        $endFmd0203 = empty($endFmd02->fmd0201) ? 0 : $endFmd02->fmd0203;
                    } else {
                        $endFmd0203 = 0;
                    }

                    while ($beginDate <= $endDate) {
                        if ($loopReportMaster = $isoModel->getMasterByDate(date('Y-m-d', $beginDate))) {
                            if ($fmd02s = $fmd02Model->getByFmd0101($loopReportMaster->fmd0101)) {
                                foreach ($fmd02s as $fmd02) {
                                    $a = $beginFlag;
                                    $b = $loopReportMaster->date . '_' . $fmd02->fmd0203;
                                    $c = date('Y-m-d') . '_' . $endFmd0203;

                                    if ($a < $b) {
                                        if (isset($loopReportMaster->comments) && $loopReportMaster->comments) {
                                            $loopComments = json_decode($loopReportMaster->comments);
                                        } else {
                                            $loopComments = new \stdClass();
                                        }

                                        if (!isset($loopComments->{$fmd02->fmd0201}->{$postData['fmd0701']})) {
                                            if (!isset($loopComments->{$fmd02->fmd0201})) {
                                                $loopComments->{$fmd02->fmd0201} = new \stdClass();
                                            }
                                            $loopComments->{$fmd02->fmd0201}->{$postData['fmd0701']} = [$commentMsg];
                                        } else {
                                            $loopComments->{$fmd02->fmd0201}->{$postData['fmd0701']}[] = $commentMsg;
                                        }

                                        $loopReportMaster->comments = json_encode($loopComments);
                                        if (isset($loopReportMaster->id)) {
                                            $this->db->table($tableName)->where('id', $loopReportMaster->id)->update((array)$loopReportMaster);
                                        }

                                        if ($b === $c) {
                                            break 2;
                                        }
                                    }
                                }
                            } else {
                                // No shifts - propagate to 'all'
                                $a = $beginFlag;
                                $b = $loopReportMaster->date . '_0';
                                if ($a < $b) {
                                    if (isset($loopReportMaster->comments) && $loopReportMaster->comments) {
                                        $loopComments = json_decode($loopReportMaster->comments);
                                    } else {
                                        $loopComments = new \stdClass();
                                    }

                                    if (!isset($loopComments->{"all"}->{$postData['fmd0701']})) {
                                        if (!isset($loopComments->{"all"})) {
                                            $loopComments->{"all"} = new \stdClass();
                                        }
                                        $loopComments->{"all"}->{$postData['fmd0701']} = [$commentMsg];
                                    } else {
                                        $loopComments->{"all"}->{$postData['fmd0701']}[] = $commentMsg;
                                    }

                                    $loopReportMaster->comments = json_encode($loopComments);
                                    if (isset($loopReportMaster->id)) {
                                        $this->db->table($tableName)->where('id', $loopReportMaster->id)->update((array)$loopReportMaster);
                                    }
                                }
                            }
                        }
                        $beginDate = strtotime(date('Y-m-d', $beginDate) . ' +1 day');
                    }
                }

                return $this->ajaxReturn("OK");
            }

            return $this->ajaxReturn('ERR', $this->message->output());
        }

        if ($param) {
            $tmp = urldecode($param);
            $data = json_decode($tmp);

            $fmd01 = $fmd01Model->find($data->fmd0106);
            $isoModel->setTableName($fmd01);

            $reportMaster = null;
            $reportDetail = null;

            if ($reportMaster = $isoModel->getMaster($data->master_id)) {
                $reportDetail = $isoModel->getDetailByMaster($reportMaster->id);
            }

            if (is_null($reportMaster) || is_null($reportDetail)) {
                $this->message->add(lang('Rawdata.not_data_hint2'));
                return $this->error(lang('Rawdata.not_data_hint2'), 400);
            }

            $this->data['data'] = $data;
            $this->data['report_master'] = $reportMaster;

            return $this->render();
        }

        return $this->error(lang('Rawdata.not_data_hint2'), 400);
    }

    public function detailAutoCommentEdit(?string $param = null): string|\CodeIgniter\HTTP\ResponseInterface
    {
        if (!$param) {
            return $this->error(lang('Rawdata.not_data_hint2'), 400);
        }

        $fmd01Model = model('Fmd01Model');
        $isoModel = model('IsoModel');
        $rec01Model = model('Rec01Model');

        if ($postData = $this->request->getPost()) {
            foreach ($postData as $key => $val) {
                if (preg_match('/^id_(\d+)$/', $key)) {
                    $rec01Model->delete($val);
                }
            }
            return $this->ajaxReturn("OK");
        }

        $tmp = urldecode($param);
        $data = json_decode($tmp);

        $fmd01 = $fmd01Model->find($data->fmd0106);
        $isoModel->setTableName($fmd01);

        $reportMaster = null;
        $reportDetail = null;

        if ($reportMaster = $isoModel->getMaster($data->master_id)) {
            $reportDetail = $isoModel->getDetailByMaster($reportMaster->id);
        }

        if (is_null($reportMaster) || is_null($reportDetail)) {
            $this->message->add(lang('Rawdata.not_data_hint2'));
            return $this->error(lang('Rawdata.not_data_hint2'), 400);
        }

        if ($rec01s = $rec01Model->getByRec0104($data->fmd0701)) {
            $this->data['rec01s'] = $rec01s;
        } else {
            $this->data['rec01s'] = [];
        }

        $this->data['data'] = $data;
        $this->data['report_master'] = $reportMaster;

        return $this->render();
    }

    public function linkageByEnt1001(int $ent1001 = 0): \CodeIgniter\HTTP\ResponseInterface
    {
        $ent0101 = $this->session->get('ent0101');
        $ent10Id = $this->ent10Model->getSubDepartmentId($ent1001);
        $ent10Id = empty($ent10Id) ? (string)$ent1001 : $ent1001 . ',' . $ent10Id;

        $sys01s = $this->sys01Model->getNameArray(
            'sys0101',
            'sys0103,sys0104',
            null,
            ['sys0205' => 1, 'sys0108' => 1, '*sys0110' => "sys0110 in ({$ent10Id})"]
        );

        $fmd08s = $this->fmd08Model->getFmd0804Array(
            ['' => lang('Rawdata.search_select_fmd0804_default')],
            ['*ent1001' => "ent1001 in ({$ent10Id})"]
        );

        $dev01s = $this->dev01Model->getNameArray(
            'dev0101',
            'dev0104',
            null,
            ['dev0102' => $ent0101, '*dev0103' => "(dev0103 is null or dev0103 in ({$ent10Id}))"],
            'dev0104'
        );

        return $this->response->setJSON([
            'sys01s' => $sys01s,
            'fmd08s' => $fmd08s,
            'dev01s' => $dev01s,
        ]);
    }

    public function linkagePad0104(): \CodeIgniter\HTTP\ResponseInterface
    {
        $fmd0804 = trim($this->request->getPost('fmd0804') ?? '');
        $listdb = ['fmd02s' => [], 'fmd07s' => []];

        $sql = sprintf(
            "select fmd02.* from fmd02,fmd08,fmd01 where fmd0802=fmd0202 and fmd0202=fmd0101 and fmd0108=2 and fmd0804='%s' order by fmd0203 asc",
            $this->db->escapeString($fmd0804)
        );
        $fmd02s = $this->db->query($sql)->getResult();
        foreach ($fmd02s as $v1) {
            $listdb['fmd02s'][$v1->fmd0203] = $v1->fmd0204;
        }

        $sql = sprintf(
            "select fmd07.* from fmd08,fmd09,fmd07 where fmd0903=fmd0801 and fmd0708=fmd0901 and fmd0804='%s' group by fmd0707",
            $this->db->escapeString($fmd0804)
        );
        $fmd07s = $this->db->query($sql)->getResult();
        foreach ($fmd07s as $v1) {
            $fmd0707 = (int)$v1->fmd0707 ? (int)$v1->fmd0707 : $v1->fmd0701;
            $listdb['fmd07s'][$fmd0707] = $v1->fmd0703;
        }

        return $this->response->setJSON($listdb);
    }

    private function getDistance(float $lng1, float $lat1, float $lng2, float $lat2): float
    {
        $radLat1 = deg2rad($lat1);
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
        return $s;
    }
}
