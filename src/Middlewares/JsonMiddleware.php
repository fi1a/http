<?php

declare(strict_types=1);

namespace Fi1a\Http\Middlewares;

use Fi1a\Http\Exception\BadRequestException;
use Fi1a\Http\JsonResponseInterface;
use Fi1a\Http\MimeInterface;
use Fi1a\Http\RequestInterface;
use Fi1a\Http\ResponseInterface;
use JsonException;

use const JSON_THROW_ON_ERROR;

/**
 * Поддержка JSON
 */
class JsonMiddleware extends AbstractMiddleware
{
    /**
     * @inheritDoc
     */
    public function handleRequest(RequestInterface $request): RequestInterface
    {
        $header = $request->headers()->getLastHeader('Content-Type');
        if (!$header || $header->getValue() !== MimeInterface::JSON) {
            return $request;
        }

        try {
            $request->setBody(
                json_decode(
                    stream_get_contents($request->rawBody()),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                )
            );
        } catch (JsonException $exception) {
            throw new BadRequestException($exception->getMessage());
        }

        return $request;
    }

    /**
     * @inheritDoc
     */
    public function handleResponse(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!($response instanceof JsonResponseInterface)) {
            return $response;
        }

        buffer()->clear();
        $response->withoutHeader('Content-Type');
        $response->withHeader('Content-Type', MimeInterface::JSON);
        buffer()->send($response);
        $this->terminate();

        return $response;
    }
}
