<?php

namespace App\Controllers;

use App\Libraries\User;

/**
 * Auth Controller - 認證相關
 */
class Auth extends BaseController
{
    protected User $user;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->user = new User();
        helper('language');
    }

    /**
     * Index - redirect to login
     */
    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        return $this->login();
    }

    public function login(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        // Get RBAC config
        $rbacConfig = config('Rbac');
        $useSecCode = $rbacConfig->useSecCode ?? false;
        $identityColumn = $rbacConfig->identity ?? 'sys0102';

        // Pass variables to view
        $this->data['use_sec_code'] = $useSecCode;
        $this->data['identity_column'] = $identityColumn;

        if ($this->request->is('post')) {
            $rules = [
                'identity' => 'required',
                'password' => 'required',
            ];

            // Check if security code is required
            if ($useSecCode === true) {
                $rules['sec_code'] = 'required';
            }

            if ($this->validate($rules)) {
                // Validate security code manually
                if ($useSecCode && !$this->secCodeCheck($this->request->getPost('sec_code'))) {
                    $this->data['message'] = lang('Auth.sec_code_check_error');
                    return $this->render('auth');
                }

                $remember = (bool)$this->request->getPost('remember');

                if ($this->user->login($this->request->getPost('identity'), $this->request->getPost('password'), $remember, false)) {
                    return redirect()->to('home');
                } else {
                    // Login failed - show error without redirect to preserve form data
                    $this->data['message'] = $this->user->getMessageOutput();
                }
            } else {
                $this->data['message'] = \Config\Services::validation()->listErrors();
            }
        } else {
            $this->data['message'] = $this->session->getFlashdata('message');
        }

        return $this->render('auth');
    }

    public function logout(): \CodeIgniter\HTTP\RedirectResponse
    {
        $this->user->logout();
        return redirect()->to('auth/login');
    }

    /**
     * Generate security code image
     */
    public function secode(): void
    {
        helper('text');
        $code = strtolower(random_string('numeric', 4));
        $this->session->set('sec_code', $code);

        if (class_exists('App\Libraries\Seccode')) {
            $seccode = \App\Libraries\Seccode::create();
            $seccode->setSeccode($code)->display();
        }
    }

    /**
     * Verify security code
     */
    public function secCodeCheck(string $secCode): bool
    {
        return $this->session->get('sec_code') === $secCode;
    }

    public function forgotPassword(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        $identityLabel = lang('Auth.f_' . $this->user->identityColumn);

        $rules = [
            'identity' => 'required',
        ];

        if (!$this->request->is('post') || !$this->validate($rules)) {
            $this->data['identity_label'] = $identityLabel;
            $this->data['message'] = \Config\Services::validation()->listErrors() ?: $this->session->getFlashdata('message');
            return $this->render('auth');
        }

        $forgotten = $this->user->forgottenPassword($this->request->getPost('identity'));
        if ($forgotten) {
            $this->session->setFlashdata('message', $this->message->output());
            return redirect()->to('auth/login');
        } else {
            $this->session->setFlashdata('message', $this->message->output());
            return redirect()->to('auth/forgot_password');
        }
    }

    public function resetPassword(?string $code = null): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (!$code) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $user = $this->user->forgottenPasswordCheck($code);

        if ($user) {
            $rbacConfig = config('Rbac');
            $minLength = $rbacConfig->minPasswordLength ?? 8;
            $maxLength = $rbacConfig->maxPasswordLength ?? 20;

            $rules = [
                'new' => "required|min_length[{$minLength}]|max_length[{$maxLength}]|matches[new_confirm]",
                'new_confirm' => 'required',
            ];

            if (!$this->request->is('post') || !$this->validate($rules)) {
                $this->data['message'] = \Config\Services::validation()->listErrors() ?: $this->session->getFlashdata('message');
                $this->data['min_password_length'] = $minLength;
                return $this->render('auth');
            }

            $change = $this->user->changePassword($user->sys0101, $this->request->getPost('new'));
            if ($change) {
                $this->session->setFlashdata('message', lang('Auth.password_change_successful'));
                $this->user->logout();
            } else {
                $this->session->setFlashdata('message', $this->message->output());
                return redirect()->to('auth/reset_password/' . $code);
            }
        } else {
            $this->session->setFlashdata('message', $this->message->output());
            return redirect()->to('auth/forgot_password');
        }

        return redirect()->to('auth/login');
    }

    public function noEnterprise(): string
    {
        $this->data['message'] = lang('Auth.no_enterprise');
        return $this->render('auth', 'auth/error');
    }

    public function permissionError(): string
    {
        $this->data['message'] = lang('Auth.permission_error');
        return $this->render('auth', 'auth/error');
    }

    public function licenseError(): string
    {
        $this->data['message'] = lang('Auth.license_error');
        return $this->render('auth', 'auth/error');
    }
}
