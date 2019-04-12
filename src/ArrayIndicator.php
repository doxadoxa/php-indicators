<?php
declare(strict_types=1);

namespace Doxadoxa\PhpIndicators;

use ArrayAccess;
use Doxadoxa\PhpIndicators\Exceptions\CantCompareLessTwoElementsException;
use Doxadoxa\PhpIndicators\Exceptions\PeriodCantBeLessNumberException;
use Doxadoxa\PhpIndicators\Exceptions\PeriodCantBeNegativeException;
use Exception;

use function count;

class ArrayIndicator implements Indicator, ArrayAccess
{
    private $indicator;
    private $defaultValue = 0;

    private $indicators;

    public function __construct(array $indicator, ?int $defaultValue = 0 )
    {
        $this->indicator = $indicator;
        $this->defaultValue = $defaultValue;

        $this->indicators = new Indicators();
    }

    public function value()
    {
        return $this->last();
    }

    public function last(int $n = 0)
    {
        if (count($this->indicator) - 1 - $n < 0) {
            return $this->defaultValue;
        }

        return $this->indicator[count($this->indicator) - 1 - $n];
    }

    public function isExists( int $n = 0)
    {
        return isset( $this->indicator[count($this->indicator) - 1 - $n] );
    }

    public function first(int $n = 0)
    {
        return $this->indicator[$n];
    }

    public function crossOver(Indicator $indicator):bool
    {
        return ($this->value() > $indicator->value() && $this->last(1) < $indicator->last(1));
    }

    public function crossBelow(Indicator $indicator): bool
    {
        return ($this->value() < $indicator->value() && $this->last(1) > $indicator->last(1));
    }

    public function over(Indicator $indicator):bool
    {
        return ($this->value() > $indicator->value());
    }

    public function overSeries( ArrayIndicator $indicator ): ArrayIndicator
    {
        $i = 0;

        $result = [];

        while( isset( $this[ $i ] ) && isset( $indicator[ $i ] ) ) {
            array_unshift( $result, $this[ $i ] > $indicator[ $i ] );
            ++$i;
        }

        return new ArrayIndicator( $result );
    }

    public function below(Indicator $indicator):bool
    {
        return ($this->last() < $indicator->last());
    }

    public function belowSeries( ArrayIndicator $indicator ): ArrayIndicator
    {
        $i = 0;

        $result = [];

        while( isset( $this[ $i ] ) && isset( $indicator[ $i ] ) ) {
            array_unshift( $result, $this[ $i ] < $indicator[ $i ] );
            ++$i;
        }

        return new ArrayIndicator( $result );
    }

    public function isGrowth(int $position = 0)
    {
        return $this->last($position) > $this->last($position + 1);
    }

    public function isGrowthOrFlat(int $position = 0)
    {
        return $this->last($position) >= $this->last($position + 1);
    }

    public function isFall(int $position = 0)
    {
        return $this->last($position) < $this->last($position + 1);
    }

    public function isFallOrFlat(int $position = 0)
    {
        return $this->last($position) <= $this->last($position + 1);
    }

    public function turnDown()
    {
        return $this->last(2) < $this->last(1) && $this->last() < $this->last(1);
    }

    public function turnUp()
    {
        return $this->last(2) > $this->last(1) && $this->last() > $this->last(1);
    }

    public function lowest(int $interval = 2)
    {
        $slice = array_slice( $this->indicator, count( $this->indicator ) - $interval );
        return min( $slice );
    }

    public function highest(int $interval = 2)
    {
        $slice = array_slice( $this->indicator, count( $this->indicator ) - $interval );
        return max( $slice );
    }

    public function toArray(): array
    {
        return $this->indicator;
    }

    public function toReversedArray(): array
    {
        return array_reverse($this->indicator);
    }

    public function push($element)
    {
        array_push($this->indicator, $element);
    }

    public function count(): int
    {
        return count($this->indicator);
    }

    public function slice($count): ArrayIndicator
    {
        return new ArrayIndicator(array_slice($this->indicator, -$count));
    }

    public function part($start, $end = null)
    {
        return new ArrayIndicator(array_slice($this->indicator, $start, $end));
    }

    public function ago(int $count)
    {
        $count = count($this->indicator) - $count;
        return new ArrayIndicator(array_slice($this->indicator, 0, $count));
    }

    /**
     * @param int $count
     * @param bool $safe
     * @return bool
     * @throws CantCompareLessTwoElementsException
     */
    public function equals(int $count = 2, bool $safe = true): bool
    {
        if( $count < 2 && !$safe ) {
            throw new CantCompareLessTwoElementsException("You cant compare less 2 elements.");
        }

        if ( $count == 1 && $safe ) {
            return true;
        }

        $equals = true;

        $slice = array_slice($this->indicator, -$count);

        for($i = 1; $i < count($slice) - 1; ++$i ) {
            if ( $slice[$i] != $slice[$i - 1] ) {
                $equals = false;
            }
        }

        return $equals;
    }

    public function map( callable $callback )
    {
        return new ArrayIndicator( array_map( $callback, $this->indicator ) );
    }

    public function rma( int $period ): ArrayIndicator
    {
        return $this->indicators->rma( $this, $period );
    }

    public function sma( int $period ): ArrayIndicator
    {
        return $this->indicators->sma( $this, $period );
    }

    public function ema( int $period ): ArrayIndicator
    {
        return $this->indicators->ema( $this, $period );
    }

    /**
     * Whether a offset exists
     * @link https://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists( $offset )
    {
        return $this->isExists( $offset );
    }

    /**
     * Offset to retrieve
     * @link https://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet( $offset )
    {
        return $this->last( $offset );
    }

    /**
     * Offset to set
     * @link https://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     * @throws Exception
     */
    public function offsetSet( $offset, $value )
    {
        throw new Exception("You cant set element by one.");
    }

    /**
     * Offset to unset
     * @link https://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     * @throws Exception
     */
    public function offsetUnset( $offset )
    {
        throw new Exception("You cant unset element by one.");
    }

    /**
     * @param int $period
     * @throws PeriodCantBeNegativeException
     */
    public function assertPeriodNegative( int $period ): void
    {
        if ( $period < 0 ) {
            throw new PeriodCantBeNegativeException();
        }
    }

    /**
     * @param int $period
     * @param int $number
     * @throws PeriodCantBeLessNumberException
     */
    public function assertPeriodLess( int $period, int $number ): void
    {
        if ( $period < $number ) {
            throw new PeriodCantBeLessNumberException();
        }
    }
}
