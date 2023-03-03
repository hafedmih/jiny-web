<?php

namespace App\Http\Middleware;

class PageSpeedCollapseWhitespace extends PageSpeed
{
    public function apply($buffer)
    {
        $replace = [
            "/\n([\S])/" => '$1',
            "/\r/" => '',
            "/\n/" => '',
            "/\t/" => '',
            "/ +/" => ' ',
            "/> +</" => '><',
        ];

        return $this->replace($replace, $buffer);
    }
}
