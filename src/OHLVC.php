<?php
declare(strict_types=1);

namespace Doxadoxa\PhpIndicators;

use Exception;

class OHLVC
{
    private $open;
    private $high;
    private $low;
    private $volume;
    private $close;

    private $indicators;

    public function __construct( array $candles )
    {
        $this->open = new ArrayIndicator( array_column( $candles, 'open' ) );
        $this->high = new ArrayIndicator( array_column( $candles, 'high' ) );
        $this->low = new ArrayIndicator( array_column( $candles, 'low' ) );
        $this->volume = new ArrayIndicator( array_column( $candles, 'volume' ) );
        $this->close = new ArrayIndicator( array_column( $candles, 'close' ) );

        $this->indicators = new Indicators();
    }

    /**
     * @return ArrayIndicator
     */
    public function open(): ArrayIndicator
    {
        return $this->open;
    }

    /**
     * @return ArrayIndicator
     */
    public function high(): ArrayIndicator
    {
        return $this->high;
    }

    /**
     * @return ArrayIndicator
     */
    public function low(): ArrayIndicator
    {
        return $this->low;
    }

    /**
     * @return ArrayIndicator
     */
    public function volume(): ArrayIndicator
    {
        return $this->volume;
    }

    /**
     * @return ArrayIndicator
     */
    public function close(): ArrayIndicator
    {
        return $this->close;
    }

    /**
     * @param int $period
     * @return ArrayIndicator
     * @throws Exception
     */
    public function atr( int $period ): ArrayIndicator
    {
        return $this->indicators->atr( $this->high, $this->low, $this->close, $period);
    }

    /**
     * @return float
     */
    public function hl2(): float
    {
        return ( $this->high[0] + $this->low[0] ) / 2;
    }

    /**
     * @return float
     */
    public function co2(): float
    {
        return ( $this->close[0] + $this->open[0] ) / 2;
    }

    /**
     * @param callable $callback
     * @return ArrayIndicator
     */
    public function mapOC( callable $callback ): ArrayIndicator
    {
        return new ArrayIndicator(
            array_map( $callback,
                $this->open->toArray(),
                $this->close->toArray()
            )
        );
    }

    /**
     * @param callable $callback
     * @return ArrayIndicator
     */
    public function mapHL( callable $callback ): ArrayIndicator
    {
        return new ArrayIndicator(
            array_map( $callback,
                $this->high->toArray(),
                $this->low->toArray()
            )
        );
    }

    /**
     * @param callable $callback
     * @return ArrayIndicator
     */
    public function mapOHLC( callable $callback ): ArrayIndicator
    {
        return new ArrayIndicator(
            array_map( $callback,
                $this->open->toArray(),
                $this->high->toArray(),
                $this->low->toArray(),
                $this->close->toArray()
            )
        );
    }

    /**
     * @param callable $callback
     * @return ArrayIndicator
     */
    public function mapOHLVC( callable $callback ): ArrayIndicator
    {
        return new ArrayIndicator(
            array_map( $callback,
                $this->open->toArray(),
                $this->high->toArray(),
                $this->low->toArray(),
                $this->volume->toArray(),
                $this->close->toArray()
            )
        );
    }
}
