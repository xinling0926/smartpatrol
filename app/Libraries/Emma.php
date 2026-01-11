<?php

namespace App\Libraries;

class Emma
{
    private string $server_url = '';
    public string $serviceName = '';
    public string $machineHostname = '';

    public function set_server_url(string $url): void
    {
        $this->server_url = rtrim($url, '/') . '/';
    }

    /**
     * 調用EMMA平台，傳送訊息
     *
     * @param string|array $receiver
     * @param string $message
     * @return array|string|false
     */
    public function send_message(string|array $receiver, string $message): array|string|false
    {
        if ($this->server_url && $this->serviceName && $this->machineHostname) {
            if (is_array($receiver)) {
                $result = [];
                foreach ($receiver as $item) {
                    $result[] = $this->send_message($item, $message);
                }
                return $result;
            } else {
                $post_data = [
                    'serviceName'     => $this->serviceName,
                    'machineHostname' => $this->machineHostname,
                    'receiver'        => $receiver,
                    'msgType'         => 'text',
                    'message'         => $message
                ];
                return $this->http_post($this->server_url . 'emma/chat.api', $post_data);
            }
        } else {
            log_message('error', '未設定config/emma');
            return false;
        }
    }

    private function http_post(string $url, array|string $data = '', array|string $header = '', int $timeout = 0): string|false
    {
        if (function_exists('curl_init') && !empty($url)) {
            // 初始化
            $curl = curl_init();
            if ($curl) {
                if (!empty($header)) {
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
                }

                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                if ($timeout) {
                    curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
                }

                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                $response = curl_exec($curl);
                curl_close($curl);
                return $response;
            }
        }

        return false;
    }
}
