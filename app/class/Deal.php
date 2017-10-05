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
}