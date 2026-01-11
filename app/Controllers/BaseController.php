<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation.
     *
     * @var list<string>
     */
    protected $helpers = ['url', 'form', 'common', 'date', 'assets', 'html'];

    /**
     * Properties declaration for PHP 8.2+ compatibility
     */
    protected $session;
    protected $db;
    protected array $data = [];
    protected string $folder = '';
    protected string $controllerName = '';
    protected string $actionName = '';
    protected string $previousControllerName = '';
    protected string $previousActionName = '';
    public ?object $pageInfo = null;
    public ?object $parentPageInfo = null;
    public ?object $currentUser = null;
    protected $setting;
    protected $message;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Set locale to default (zh-TW) to ensure language files load correctly
        $defaultLocale = config('App')->defaultLocale;
        $this->request->setLocale($defaultLocale);
        service('language')->setLocale($defaultLocale);

        // Preload services
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
        $this->setting = new \App\Libraries\Setting();
        $this->message = new \App\Libraries\Message();

        // Load patrol.ini config if exists
        $iniFile = APPPATH . 'Config/patrol.ini';
        if (file_exists($iniFile)) {
            $config = parse_ini_file($iniFile, true);
        }

        // Save the previous controller and action name from session
        $this->previousControllerName = $this->session->getFlashdata('previous_controller_name') ?? '';
        $this->previousActionName = $this->session->getFlashdata('previous_action_name') ?? '';

        // Set the current controller and action name
        $router = service('router');
        $this->folder = '';

        $controllerClass = $router->controllerName();
        if ($controllerClass) {
            $this->controllerName = strtolower(basename(str_replace('\\', '/', $controllerClass)));
        }
        // Convert camelCase method name to underscore format (editFmd03 -> edit_fmd03)
        $methodName = $router->methodName();
        $this->actionName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $methodName));

        // Load page info
        if (class_exists('App\Models\Sys04Model')) {
            $sys04Model = model('Sys04Model');
            if (method_exists($sys04Model, 'getPageInfo')) {
                $this->pageInfo = $sys04Model->getPageInfo($this->folder, $this->controllerName, $this->actionName);
                if (!$this->pageInfo) {
                    $this->parentPageInfo = $sys04Model->getPageInfo($this->folder, $this->controllerName, 'index');
                }
            }
        }

        log_message('debug', "Controller init = {$this->controllerName}");
        $this->data['controller_name'] = $this->controllerName;
    }

    /**
     * Render view with layout
     */
    protected function render(?string $template = '', string $view = ''): string
    {
        // 支援 null 值，視為空字串
        if ($template === null) {
            $template = '';
        }

        if ($template === '') {
            if ($this->actionName === 'index') {
                $template = 'main';
            }
        }

        $this->session->setFlashdata('previous_controller_name', $this->controllerName);
        $this->session->setFlashdata('previous_action_name', $this->actionName);

        if ($view === '') {
            $view = $this->folder . $this->controllerName . '/' . $this->actionName;
        }

        if ($template !== '') {
            $url = base_url();
            $this->data['js'] = "var base_url='{$url}';";
            $this->data['js'] .= "var controller='{$this->controllerName}';";
            $this->data['js'] .= "var folder='{$this->folder}';";
            $this->data['js'] .= "var cuid='" . csrf_hash() . "';";
            $this->data['js'] .= "var langType='" . $this->request->getLocale() . "';";

            $this->setTitle();
            $this->setTheme();

            if (file_exists(APPPATH . 'Views/' . $view . '.php')) {
                $this->data['content'] = view($view, $this->data);
            }
            return view("layout/{$template}", $this->data);
        } else {
            $this->response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate');
            $this->response->setHeader('Pragma', 'no-cache');
            $this->data['view'] = $view;
            return view($view, $this->data);
        }
    }

    /**
     * Output JSON data
     */
    protected function json($data): \CodeIgniter\HTTP\ResponseInterface
    {
        return $this->response->setJSON($data);
    }

    /**
     * Return AJAX response
     */
    protected function ajaxReturn(string $message = '', $data = ''): \CodeIgniter\HTTP\ResponseInterface
    {
        $arr = ['message' => $message];
        if (is_array($data)) {
            $arr = array_merge($arr, $data);
        } else {
            $arr['data'] = $data;
        }
        return $this->response->setJSON($arr);
    }

    /**
     * Return error response
     */
    protected function error(string $message, int $statusCode = 500): string
    {
        $this->response->setStatusCode($statusCode);
        return $message;
    }

    /**
     * Set page title
     */
    protected function setTitle(): void
    {
        $siteTitle = $this->setting->item('site_title') ?? 'Smart Patrol';
        $this->data['site_title'] = $siteTitle;

        if ($this->pageInfo && isset($this->pageInfo->title)) {
            $this->data['page_title'] = $this->pageInfo->title;
            $this->data['title'] = $siteTitle . ' - ' . $this->pageInfo->title;
        } else {
            $this->data['page_title'] = '';
            $this->data['title'] = $siteTitle;
        }
    }

    /**
     * Set theme
     */
    protected function setTheme(): void
    {
        $theme = $this->setting->byEnterprise('theme');
        $this->data['theme'] = $theme ?: 'skin-blue-light';
    }
}
