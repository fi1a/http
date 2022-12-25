<?php

declare(strict_types=1);

use Fi1a\Http\Middlewares\JsonResponseMiddleware;
use Fi1a\Http\Middlewares\RedirectMiddleware;

http()->withMiddleware(new RedirectMiddleware());
http()->withMiddleware(new JsonResponseMiddleware());
