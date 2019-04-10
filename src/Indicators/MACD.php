<?php
declare(strict_types=1);

namespace Doxadoxa\PhpIndicators\Indicators;

use Doxadoxa\PhpIndicators\ArrayIndicator;

/**
 * Class MACD
 * @package Doxadoxa\PhpIndicators\Indicators
 */
class MACD extends ArrayIndicator
{
    /**
     * MACD constructor.
     * @param ArrayIndicator $indicator
     * @param int|null $fastPeriod
     * @param int|null $slowPeriod
     * @param int|null $signalPeriod
     * @throws \Doxadoxa\PhpIndicators\Exceptions\PeriodCantBeLessNumberException
     */
    public function __construct( ArrayIndicator $indicator, ?int $fastPeriod = null, ?int $slowPeriod = null, ?int $signalPeriod = null )
    {
        $this->assertPeriodLess( $fastPeriod,  2 );
        $this->assertPeriodLess( $slowPeriod,  2 );

        parent::__construct( array_values( trader_macd($indicator->toArray(), $fastPeriod, $slowPeriod, $signalPeriod)[0] ) );
    }

}
