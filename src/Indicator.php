<?php
declare(strict_types=1);

namespace Doxadoxa\PhpIndicators;

interface Indicator
{
    public function value();
    public function last(int $n = 1);
}
