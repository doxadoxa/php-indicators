<?php
declare(strict_types=1);

namespace Doxadoxa\PhpIndicators\Exceptions;

use Exception;

class CantCompareLessTwoElementsException extends Exception
{
    protected $message = "You can't compare less two elements.";
}
