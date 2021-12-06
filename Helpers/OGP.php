<?php

namespace Helpers;

class OGP {

    protected $properties = [
    ];

    public function set(string $property, string $value) {
        $this->properties[$property] = $value;
        return $this;
    }

    public function print() {
        foreach ($this->properties as $property => $value) {
            echo "<meta property=\"og:$property\" content=\"$value\" />\n";
        }
        return $this;
    }

}