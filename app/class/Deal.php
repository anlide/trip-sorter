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
}