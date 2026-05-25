<?php

declare(strict_types=1);

namespace App\Domain\Employee\Exceptions;

use RuntimeException;

final class EmployeeNotFoundException extends RuntimeException
{
    public function __construct(string $id)
    {
        parent::__construct("Employee with ID [{$id}] not found.");
    }
}
