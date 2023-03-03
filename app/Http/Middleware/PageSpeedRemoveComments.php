<?php

namespace App\Http\Middleware;

class PageSpeedRemoveComments extends PageSpeed
{
    public function apply($buffer)
    {
        $replace = [
            '/<!--[^]><!\[](.*?)[^\]]-->/s' => ''
        ];

        return $this->replace($replace, $buffer);
    }
}
