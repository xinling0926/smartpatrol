<?php

namespace App\Controllers;

use App\Libraries\User;

/**
 * Class AdminController
 *
 * Base controller for all admin pages that require authentication
 */
class AdminController extends BaseController
{
    protected User $user;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Initialize user library
        $this->user = new User();

        // Check authentication
        $this->user->authCheck();
        $this->addEventLog();

        // Load main menu
        $this->loadMainMenu();

        // Clear query options on index action
        if ($this->actionName === 'index') {
            $this->clearQueryOption();
        }

        // Pass user and session data to view
        $this->data['user'] = $this->user;
        $this->data['current_user'] = $this->getCurrentUserInfo();
        $this->data['session_data'] = [
            'ent0103' => $this->session->get('ent0103'),
            'ent0105' => $this->session->get('ent0105'),
        ];
        $this->data['setting'] = $this->setting;
    }

    /**
     * Get current user info from database
     */
    protected function getCurrentUserInfo(): ?object
    {
        $userId = $this->session->get('user_id');
        if (!$userId) {
            return null;
        }

        $user = $this->db->table('sys01')
            ->where('sys0101', $userId)
            ->get()
            ->getRow();

        if ($user) {
            helper('common');
            $user->name = user_display_name($user);
        }

        return $user;
    }

    /**
     * Add event log
     */
    private function addEventLog(): void
    {
        if ($this->user->id && $this->pageInfo) {
            $log03 = [
                'log0302' => date('Y-m-d H:i:s'),
                'log0303' => $this->request->getIPAddress(),
                'log0304' => $this->user->id,
                'log0305' => $this->pageInfo->sys0401 ?? null,
            ];

            if (class_exists('App\Models\Log03Model')) {
                $log03Model = model('Log03Model');
                $log03Model->insert($log03, false);
            }
        }
    }

    /**
     * Clear query option
     */
    protected function clearQueryOption(): void
    {
        $queryOptionName = $this->controllerName . '_opt';
        $this->session->remove($queryOptionName);
    }

    /**
     * Load query option
     */
    protected function loadQueryOption(): array
    {
        $queryOptionName = $this->controllerName . '_opt';
        $option = $this->session->get($queryOptionName);
        return is_array($option) ? $option : [];
    }

    /**
     * Get query option from POST
     */
    protected function getQueryOption(): array
    {
        $opt = $this->request->getPost();

        // Filter out non-database fields (CSRF token, etc.)
        $excludeFields = ['cuid', 'csrf_token', 'csrf_test_name'];
        foreach ($excludeFields as $field) {
            unset($opt[$field]);
        }

        $this->setQueryOption($opt);
        return $opt;
    }

    /**
     * Set query option
     */
    protected function setQueryOption(array $opt): void
    {
        $queryOptionName = $this->controllerName . '_opt';
        $this->session->set($queryOptionName, $opt);
    }

    /**
     * Get page size
     */
    protected function getPageSize(string $item = ''): int
    {
        return (int)($this->setting->item('default_page_size') ?? 20);
    }

    /**
     * Set pagination
     */
    protected function setPage(int $totalRows, int $page, int $pageSize, string $url = '', string $div = ''): void
    {
        $this->data['total_rows'] = $totalRows;
        $this->data['page_size'] = $pageSize;
        $this->data['current_page'] = $page;

        // Generate custom AJAX pagination
        $this->data['pager'] = $this->generateAjaxPagination($totalRows, $page, $pageSize, $div);
    }

    /**
     * Generate AJAX-compatible pagination HTML
     */
    protected function generateAjaxPagination(int $totalRows, int $currentPage, int $pageSize, string $div = ''): string
    {
        $totalPages = (int) ceil($totalRows / $pageSize);

        if ($totalPages <= 1) {
            return '';
        }

        $div = $div ?: 'pane_list';
        $html = '<ul class="pagination">';

        // Previous button
        if ($currentPage > 1) {
            $html .= '<li><a href="javascript:void(0)" onclick="setpage(this)" data-ci-pagination-page="' . ($currentPage - 1) . '" data-div="' . $div . '">&laquo;</a></li>';
        } else {
            $html .= '<li class="disabled"><span>&laquo;</span></li>';
        }

        // Page numbers - show limited range around current page
        $range = 2;
        $start = max(1, $currentPage - $range);
        $end = min($totalPages, $currentPage + $range);

        // Always show first page
        if ($start > 1) {
            $html .= '<li><a href="javascript:void(0)" onclick="setpage(this)" data-ci-pagination-page="1" data-div="' . $div . '">1</a></li>';
            if ($start > 2) {
                $html .= '<li class="disabled"><span>...</span></li>';
            }
        }

        // Page range
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $currentPage) {
                $html .= '<li class="active"><span>' . $i . '</span></li>';
            } else {
                $html .= '<li><a href="javascript:void(0)" onclick="setpage(this)" data-ci-pagination-page="' . $i . '" data-div="' . $div . '">' . $i . '</a></li>';
            }
        }

        // Always show last page
        if ($end < $totalPages) {
            if ($end < $totalPages - 1) {
                $html .= '<li class="disabled"><span>...</span></li>';
            }
            $html .= '<li><a href="javascript:void(0)" onclick="setpage(this)" data-ci-pagination-page="' . $totalPages . '" data-div="' . $div . '">' . $totalPages . '</a></li>';
        }

        // Next button
        if ($currentPage < $totalPages) {
            $html .= '<li><a href="javascript:void(0)" onclick="setpage(this)" data-ci-pagination-page="' . ($currentPage + 1) . '" data-div="' . $div . '">&raquo;</a></li>';
        } else {
            $html .= '<li class="disabled"><span>&raquo;</span></li>';
        }

        $html .= '</ul>';

        return $html;
    }

    /**
     * Load main menu
     */
    protected function loadMainMenu(): void
    {
        if ($this->session->has('menu')) {
            $this->data['main_menu'] = $this->session->get('menu');
        } else {
            if (!class_exists('App\Models\Sys05Model')) {
                $this->data['main_menu'] = '';
                return;
            }

            $sys05Model = model('Sys05Model');
            $mainMenu = $sys05Model->getUserMenu($this->user->isAdmin, $this->session->get('permissions') ?? []);
            $output = '';

            foreach ($mainMenu as $menu) {
                $subOut = '';
                $subActive = false;

                if (!empty($menu->submenu)) {
                    $subOut = '<ul class="treeview-menu">';
                    foreach ($menu->submenu as $sm) {
                        if ($sm->sys0401) {
                            if ($sm->sys0404 === 'index') {
                                $url = ($sm->sys0409 ?? '') . $sm->sys0403;
                            } else {
                                $url = ($sm->sys0409 ?? '') . $sm->sys0403 . '/' . $sm->sys0404;
                            }
                            $url = base_url($url);
                        } else {
                            $url = '#';
                        }

                        if ($sm->sys0401 && $this->pageInfo) {
                            if ($sm->sys0401 === $this->pageInfo->sys0401) {
                                $subOut .= '<li class="active">';
                                $subActive = true;
                            } else {
                                $subOut .= '<li>';
                            }
                        } else {
                            $subOut .= '<li>';
                        }

                        $target = !empty($sm->sys0507) ? " target=\"{$sm->sys0507}\"" : '';
                        $subOut .= "<a href=\"{$url}\"{$target}><i class=\"fa {$sm->sys0503}\"></i> {$sm->title}</a></li>";
                    }
                    $subOut .= '</ul>';
                } else {
                    if ($menu->sys0401 === null) {
                        continue;
                    }
                }

                if ($menu->sys0401) {
                    if ($menu->sys0404 === 'index') {
                        $url = ($menu->sys0409 ?? '') . $menu->sys0403;
                    } else {
                        $url = ($menu->sys0409 ?? '') . $menu->sys0403 . '/' . $menu->sys0404;
                    }
                    $url = base_url($url);
                } else {
                    $url = '#';
                }

                $class = '';
                if ($subActive) {
                    $class = 'active';
                } else {
                    if ($menu->sys0401) {
                        if ($menu->sys0403 === $this->controllerName && $menu->sys0404 === $this->actionName) {
                            $class = 'active';
                        }
                    }
                }

                if (!empty($menu->submenu)) {
                    if ($class) {
                        $class .= ' ';
                    }
                    $class .= 'treeview';
                }

                if ($class) {
                    $output .= "<li class=\"{$class}\">";
                } else {
                    $output .= '<li>';
                }

                $target = !empty($menu->sys0507) ? " target=\"{$menu->sys0507}\"" : '';
                $output .= "<a href=\"{$url}\"{$target}><i class=\"fa {$menu->sys0503}\"></i><span>{$menu->title}</span>";
                if (!empty($menu->submenu)) {
                    $output .= '<i class="fa fa-angle-left pull-right"></i>';
                }
                $output .= "</a>{$subOut}</li>";
            }

            $this->session->set('menu', $output);
            $this->data['main_menu'] = $output;
        }
    }

    /**
     * Get allowed enterprise list
     */
    protected function getAllowEnt10(): array
    {
        return $this->db->table('ent10')
            ->select('ent1001, ent1004')
            ->get()
            ->getResultArray();
    }
}
