<?php

namespace ChrisHolland\Projector;

class Projection
{

    /**
     * @var float
     */
    public $total;
    /**
     * @var float
     */
    public $delta;
    /**
     * @var float
     */
    public $deltaIncrementPercentage;

    public function __construct(float $total, float $delta, float $deltaIncrementPercentage)
    {
        $this->total = $total;
        $this->delta = $delta;
        $this->deltaIncrementPercentage = $deltaIncrementPercentage;
    }

    public function __toString()
    {
        return number_format($this->total).': '.number_format($this->delta).': '.number_format($this->deltaIncrementPercentage,4)."\n";
    }
}
