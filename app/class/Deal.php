<?php

class Deal {
    /** @var string $transport - (train|bus|car|...) */
    public $transport;

    /** @var string $departure - location title */
    public $departure;

    /** @var string $arrival - location title */
    public $arrival;

    /** @var Duration $duration - object */
    public $duration;

    /** @var integer $cost */
    public $cost;

    /** @var integer $discount */
    public $discount;

    /** @var string $reference */
    public $reference;


    /** @var float $price - sum of deal prices (with discount) */
    public $costSum;

    /** @var integer $minutes - amount of minutes */
    public $durationSum;

    /** @var string[] $citiesWas */
    public $citiesWas = [];

    /** @var Deal $previousDeal - here should be link */
    public $previousDeal;

    /** @var boolean $checked */
    public $checked = false;

    public function __construct($transport, $departure, $arrival, $duration, $cost, $discount, $reference)
    {
        $this->transport = $transport;
        $this->departure = $departure;
        $this->arrival = $arrival;
        $this->duration = $duration;
        $this->cost = $cost;
        $this->discount = $discount;
        $this->reference = $reference;
    }

    public function flushSum()
    {
        $this->costSum = 0;
        $this->durationSum = 0;
        $this->citiesWas = [];
    }

    /**
     * @return string[]
     */
    public function getCitiesWasArrival()
    {
        return $this->citiesWas;
    }

    /**
     * @return string[][]
     */
    public function getJsonData()
    {
        $thisObject = [
            'transport' => $this->transport,
            'departure' => $this->departure,
            'arrival' => $this->arrival,
            'duration' => $this->duration->getMinutes(),
            'cost' => $this->cost,
            'discount' => $this->discount,
            'reference' => $this->reference,
        ];
        if ($this->previousDeal === null) {
            return [$thisObject];
        } else {
            $return = $this->previousDeal->getJsonData();
            $return[] = $thisObject;
            return $return;
        }
    }
}