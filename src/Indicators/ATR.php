<?php
declare(strict_types=1);

namespace Doxadoxa\PhpIndicators\Indicators;

use Doxadoxa\PhpIndicators\ArrayIndicator;

class ATR extends ArrayIndicator
{

    /**
     * ATR constructor.
     * @param ArrayIndicator $high
     * @param ArrayIndicator $low
     * @param ArrayIndicator $close
     * @param int $period
     * @throws \Doxadoxa\PhpIndicators\Exceptions\PeriodCantBeLessNumberException
     */
    public function __construct( ArrayIndicator $high, ArrayIndicator $low, ArrayIndicator $close, int $period = 14 )
    {
        $this->assertPeriodLess( $period,  2 );

        parent::__construct( array_values( trader_atr( $high->toArray(), $low->toArray(), $close->toArray(), $period ) ));
    }

}
