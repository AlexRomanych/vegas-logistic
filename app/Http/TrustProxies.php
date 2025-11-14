<?php

// В новых Laravel этот класс часто находится в App\Http, а не в Middleware
namespace App\Http;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Symfony\Component\HttpFoundation\Request;

class TrustProxies extends Middleware
{
    protected $proxies = '*';
    // Или используйте конкретные IP:
    // protected $proxies = [
    //     // '127.0.0.1',
    //     // '192.168.1.1',
    // ];
    /**
     * The headers that should be used to detect proxies.
     * Мы используем псевдоним 'Middleware' для доступа к константам родителя.
     *
     * @var int
     */
    // protected $headers =
    //     Request::HEADER_X_FORWARDED_FOR |
    //     Request::HEADER_X_FORWARDED_HOST |
    //     Request::HEADER_X_FORWARDED_PORT |
    //     Request::HEADER_X_FORWARDED_PROTO |
    //     Request::HEADER_X_FORWARDED_AWS_ELB;
}
