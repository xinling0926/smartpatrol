<?php

namespace App\Libraries;

/**
 * Seccode Library - CI4 Version
 * 驗證碼產生函式庫
 */
class Seccode
{
    protected string $seccode = '';
    protected int $height = 34;

    /**
     * 建立驗證碼物件
     */
    public static function create(): SeccodeInterface
    {
        if (self::supperGD()) {
            return new GdSeccode();
        }
        return new BmpSeccode();
    }

    /**
     * 檢查是否支援 GD
     */
    protected static function supperGD(): bool
    {
        return function_exists('imagecreate')
            && function_exists('imagecolorallocate')
            && function_exists('imagesetpixel')
            && function_exists('imageString')
            && function_exists('imagedestroy')
            && function_exists('imagefilledrectangle')
            && function_exists('imagerectangle')
            && (function_exists('imagepng') || function_exists('imagejpeg'));
    }
}

/**
 * 驗證碼介面
 */
interface SeccodeInterface
{
    public function display(): void;
    public function setSeccode(string $seccode): self;
    public function setHeight(int $height): self;
}

/**
 * 抽象驗證碼類別
 */
abstract class AbstractSeccode implements SeccodeInterface
{
    protected string $seccode = '';
    protected int $height = 34;

    public function setSeccode(string $seccode): self
    {
        $this->seccode = $seccode;
        return $this;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;
        return $this;
    }
}

/**
 * GD 驗證碼實作
 */
class GdSeccode extends AbstractSeccode
{
    public function display(): void
    {
        $codeLen = strlen($this->seccode);
        $xSize = 18 * $codeLen;
        $ySize = $this->height;
        $aimg = imagecreate($xSize, $ySize);

        $black = imagecolorallocate($aimg, 0xFF, 0xFF, 0xFF);
        $border = imagecolorallocate($aimg, 0xCC, 0xCC, 0xCC);
        imagefilledrectangle($aimg, 0, 0, $xSize - 1, $ySize - 1, $black);
        imagerectangle($aimg, 0, 0, $xSize - 1, $ySize - 1, $border);

        // 干擾點
        for ($i = 1; $i <= 20; $i++) {
            $dot = imagecolorallocate($aimg, mt_rand(50, 255), mt_rand(0, 120), mt_rand(50, 255));
            imagesetpixel($aimg, mt_rand(2, $xSize - 2), mt_rand(2, $ySize - 2), $dot);
        }

        // 干擾線
        $dot = imagecolorallocate($aimg, mt_rand(50, 255), mt_rand(0, 120), mt_rand(50, 255));
        imageline($aimg, mt_rand(1, 5), mt_rand(1, $ySize), mt_rand(5, $xSize / 2), mt_rand(1, $ySize), $dot);
        imageline($aimg, mt_rand($xSize / 2, ($xSize / 2) + 5), mt_rand(1, $ySize), mt_rand(($xSize / 2 + 5), $xSize), mt_rand(1, $ySize), $dot);

        // 字型檔案路徑
        $fontFiles = ROOTPATH . 'public/assets/fonts/Futurama Bold Font.ttf';

        if (function_exists('imagettftext') && is_file($fontFiles)) {
            imagealphablending($aimg, true);
            for ($i = 0; $i < strlen($this->seccode); $i++) {
                $color = imagecolorallocate($aimg, mt_rand(50, 255), mt_rand(0, 120), mt_rand(50, 255));
                $angle = (mt_rand(1, 10) % 2 === 0 ? '-' : '') . mt_rand(10, 30);
                $fontSize = mt_rand(12, 14);
                $x = $i * $xSize / $codeLen + mt_rand(2, 5);
                $y = mt_rand($fontSize + 2, $ySize - 2);
                imagettftext($aimg, $fontSize, (int)$angle, (int)$x, (int)$y, $color, $fontFiles, $this->seccode[$i]);
            }
        } else {
            $font = 5;
            $fontHeight = imagefontheight($font);
            for ($i = 0; $i < strlen($this->seccode); $i++) {
                $x = $i * $xSize / $codeLen + mt_rand(2, 5);
                $y = mt_rand(1, $ySize - $fontHeight);
                $color = imagecolorallocate($aimg, mt_rand(50, 255), mt_rand(0, 120), mt_rand(50, 255));
                imagestring($aimg, $font, (int)$x, (int)$y, $this->seccode[$i], $color);
            }
        }

        if (ob_get_length()) {
            ob_clean();
        }

        header("Pragma:no-cache");
        header("Cache-control:no-cache");

        if (function_exists('imagepng')) {
            header("Content-type: image/png");
            imagepng($aimg);
        } else {
            header("Content-type: image/jpeg");
            imagejpeg($aimg);
        }

        imagedestroy($aimg);
        exit;
    }
}

/**
 * BMP 驗證碼實作
 */
class BmpSeccode extends AbstractSeccode
{
    public function display(): void
    {
        header("Pragma:no-cache");
        header("Cache-control:no-cache");
        header("ContentType: Image/BMP");

        $Color = [];
        $Color[0] = chr(0) . chr(0) . chr(0);
        $Color[1] = chr(255) . chr(255) . chr(255);

        $_Num = [];
        $_Num[0] = "1110000111110111101111011110111101001011110100101111010010111101001011110111101111011110111110000111";
        $_Num[1] = "1111011111110001111111110111111111011111111101111111110111111111011111111101111111110111111100000111";
        $_Num[2] = "1110000111110111101111011110111111111011111111011111111011111111011111111011111111011110111100000011";
        $_Num[3] = "1110000111110111101111011110111111110111111100111111111101111111111011110111101111011110111110000111";
        $_Num[4] = "1111101111111110111111110011111110101111110110111111011011111100000011111110111111111011111111000011";
        $_Num[5] = "1100000011110111111111011111111101000111110011101111111110111111111011110111101111011110111110000111";
        $_Num[6] = "1111000111111011101111011111111101111111110100011111001110111101111011110111101111011110111110000111";
        $_Num[7] = "1100000011110111011111011101111111101111111110111111110111111111011111111101111111110111111111011111";
        $_Num[8] = "1110000111110111101111011110111101111011111000011111101101111101111011110111101111011110111110000111";
        $_Num[9] = "1110001111110111011111011110111101111011110111001111100010111111111011111111101111011101111110001111";

        echo chr(66) . chr(77) . chr(230) . chr(4) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(54) . chr(0) . chr(0) . chr(0) . chr(40) . chr(0) . chr(0) . chr(0) . chr(40) . chr(0) . chr(0) . chr(0) . chr(10) . chr(0) . chr(0) . chr(0) . chr(1) . chr(0);
        echo chr(24) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(176) . chr(4) . chr(0) . chr(0) . chr(18) . chr(11) . chr(0) . chr(0) . chr(18) . chr(11) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0) . chr(0);

        for ($i = 9; $i >= 0; $i--) {
            for ($j = 0; $j <= 3; $j++) {
                for ($k = 1; $k <= 10; $k++) {
                    if (mt_rand(0, 7) < 1) {
                        echo $Color[mt_rand(0, 1)];
                    } else {
                        echo $Color[(int)substr($_Num[(int)$this->seccode[$j]], $i * 10 + $k, 1)];
                    }
                }
            }
        }
        exit;
    }
}
