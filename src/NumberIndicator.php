<?php
declare(strict_types=1);

namespace Doxadoxa\PhpIndicators;

class NumberIndicator implements Indicator
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function value()
    {
        return $this->value;
    }

    public function last(int $n = 1)
    {
        return $this->value;
    }
}
