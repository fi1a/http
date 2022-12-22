<?php

declare(strict_types=1);

namespace Fi1a\Http;

use SessionHandler as PhpSessionHandler;

/**
 * Обработчик сохранения сессий
 */
class SessionHandler extends PhpSessionHandler implements SessionHandlerInterface
{
}
