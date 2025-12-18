<?php

declare(strict_types=1);

return new PDO(
    'sqlite:' . __DIR__ . '/database/database.db',
    null,
    null,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]
);
