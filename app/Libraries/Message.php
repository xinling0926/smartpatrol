<?php

namespace App\Libraries;

/**
 * Message Library - 訊息管理
 */
class Message
{
    protected array $msg = [];

    public function __construct()
    {
        $this->msg = [];
    }

    /**
     * 新增訊息
     */
    public function add(string $text): void
    {
        $this->msg[] = $text;
    }

    /**
     * 輸出處理訊息
     */
    public function output(): string
    {
        $output = '';
        foreach ($this->msg as $message) {
            $output .= '<p>' . $message . '</p>';
        }
        return $output;
    }

    /**
     * 取得所有訊息
     */
    public function getAll(): array
    {
        return $this->msg;
    }

    /**
     * 清除所有訊息
     */
    public function clear(): void
    {
        $this->msg = [];
    }

    /**
     * 檢查是否有訊息
     */
    public function hasMessages(): bool
    {
        return !empty($this->msg);
    }
}
