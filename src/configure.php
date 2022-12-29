<?php

declare(strict_types=1);

use Fi1a\DI\Builder;
use Fi1a\Http\BufferOutput;
use Fi1a\Http\BufferOutputInterface;
use Fi1a\Http\Http;
use Fi1a\Http\HttpInterface;
use Fi1a\Http\JsonResponse;
use Fi1a\Http\JsonResponseInterface;
use Fi1a\Http\Middlewares\JsonMiddleware;
use Fi1a\Http\Middlewares\RedirectMiddleware;
use Fi1a\Http\RedirectResponse;
use Fi1a\Http\RedirectResponseInterface;
use Fi1a\Http\RequestInterface;
use Fi1a\Http\Response;
use Fi1a\Http\ResponseInterface;
use Fi1a\Http\Session\SessionStorage;
use Fi1a\Http\Session\SessionStorageInterface;
use Fi1a\Http\SetCookie;

di()->config()->addDefinition(
    Builder::build(HttpInterface::class)
    ->defineFactory(function () {
        /** @var HttpInterface|null $http */
        static $http = null;
        if (is_null($http)) {
            // @codeCoverageIgnoreStart
            /** @psalm-suppress InvalidArgument */
            $request = Http::createRequestWithGlobals(
                $_GET,
                $_POST,
                $_COOKIE,
                $_FILES,
                $_SERVER
            );
            $http = new Http(
                $request,
                new SessionStorage(),
                new Response(
                    ResponseInterface::HTTP_OK,
                    null,
                    $request
                ),
                new BufferOutput(new SetCookie())
            );
            // @codeCoverageIgnoreEnd
        }

        return $http;
    })
    ->getDefinition()
);

di()->config()->addDefinition(
    Builder::build(RequestInterface::class)
    ->defineFactory(function () {
        return request();
    })
    ->getDefinition()
);

di()->config()->addDefinition(
    Builder::build(ResponseInterface::class)
        ->defineFactory(function () {
            return response();
        })
        ->getDefinition()
);

di()->config()->addDefinition(
    Builder::build(SessionStorageInterface::class)
        ->defineFactory(function () {
            return session();
        })
        ->getDefinition()
);

di()->config()->addDefinition(
    Builder::build(BufferOutputInterface::class)
        ->defineFactory(function () {
            return buffer();
        })
        ->getDefinition()
);

di()->config()->addDefinition(
    Builder::build(RedirectResponseInterface::class)
        ->defineClass(RedirectResponse::class)
        ->getDefinition()
);

di()->config()->addDefinition(
    Builder::build(JsonResponseInterface::class)
        ->defineClass(JsonResponse::class)
        ->getDefinition()
);

http()->withMiddleware(new RedirectMiddleware());
http()->withMiddleware(new JsonMiddleware());
