<?php

namespace Helpers;

class OGIC {

    protected $attributes = [
        'width' => 1200,
        'height' => 630,
        'background' => 'ffffff',
        'color' => '000000',
        'title' => 'Diary.by',
        'description' => 'A place for writing.',
        'titleFont' => 'Templates/Fonts/Inter-Bold.ttf',
        'descriptionFont' => 'Templates/Fonts/Inter-Regular.ttf',
        'stamp' => 'Templates/watermark.png',
    ];

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    public function __get($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        return false;
    }

    public function create() {
        header('Content-Type: image/png');
        $image = imagecreatetruecolor($this->width, $this->height);
        $bgC = $this->hex2rgb($this->background);
        $bgF = $this->hex2rgb($this->color);
        $background = imagecolorallocate($image, $bgC['r'], $bgC['g'], $bgC['b']);
        $color = imagecolorallocate($image, $bgF['r'], $bgF['g'], $bgF['b']);
        imagefilledrectangle($image, 0, 0, ($this->width - 1), ($this->height - 1), $background);
        imagettftext($image, 40, 0, 60, 120, $color, $this->titleFont, $this->title);
        imagettftext($image, 30, 0, 60, 200, $color, $this->descriptionFont, $this->description);
        $stamp = imagecreatefrompng($this->stamp);
        imagecopy($image, $stamp, ($this->width - imagesx($stamp) - 60), ($this->height - imagesy($stamp) - 60), 0, 0, imagesx($stamp), imagesy($stamp));
        imagepng($image);
        imagedestroy($image);
    }

    private function hex2rgb(string $hex) : array {
        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2)),
        ];
    }

    public function title(string $title) {
        $this->title = $title;
        return $this;
    }

    public function description($description) {
        $description = implode(' ', array_slice(explode(' ', $description), 0, 30));
        $description = wordwrap($description, 55);
        $this->description = $description;
        return $this;
    }

    public function size(int $width, int $height) {
        $this->width = $width;
        $this->height = $height;
        return $this;
    }

    public function background(string $background) {
        $this->background = $background;
        return $this;
    }

    public function color(string $color) {
        $this->color = $color;
        return $this;
    }

}