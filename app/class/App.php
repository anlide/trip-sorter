<?php

class App {
    /** @var Deal[] $deals */
    private $deals;

    /**
     * App constructor.
     */
    public function __construct()
    {
        // Use leading data from file.
        // Downloading and parse all data here.
        $this->initDataFromFileJson();
    }

    /**
     * @throws Exception
     */
    private function initDataFromFileJson()
    {
        $this->deals = [];
        $content = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/../database/data.json');
        $json = json_decode($content);
        if (!isset($json->deals)) {
            throw new Exception('Wrong format: no deals');
        }
        foreach ($json->deals as $sourceDeal) {
            $this->deals[] = new Deal(
                $sourceDeal->transport,
                $sourceDeal->departure,
                $sourceDeal->arrival,
                new Duration($sourceDeal->duration->h, $sourceDeal->duration->m),
                $sourceDeal->cost,
                $sourceDeal->discount,
                $sourceDeal->reference
            );
        }
    }

    /**
     * @return string[]
     */
    public function getCities()
    {
        $citiesBase = [];
        foreach ($this->deals as $deal) {
            $citiesBase[$deal->arrival] = true;
            $citiesBase[$deal->departure] = true;
        }
        $cities = [];
        foreach ($citiesBase as $city => $void) {
            $cities[] = $city;
        }
        return $cities;
    }

    /**
     * @return string[]
     */
    public function getTransports()
    {
        $transportsBase = [];
        foreach ($this->deals as $deal) {
            $transportsBase[$deal->transport] = true;
        }
        $transports = [];
        foreach ($transportsBase as $transport => $void) {
            $transports[] = $transport;
        }
        return $transports;
    }

    /**
     * @param string $departure
     * @param string $arrival
     * @param string $algorithm
     *
     * @throws Exception
     *
     * @return Deal
     */
    public function findPath(string $departure, string $arrival, string $algorithm)
    {
        if ($departure == $arrival) {
            throw new Exception('Departure and arrival are identical');
        }

        switch ($algorithm) {
            case 'cheapest':
                $algorithmCompare = function (Deal $a, Deal $b) {
                    if ($a->costSum < $b->costSum) return -1;
                    if ($a->costSum > $b->costSum) return 1;
                    return 0;
                };
                break;
            case 'fastest':
                $algorithmCompare = function (Deal $a, Deal $b) {
                    if ($a->durationSum < $b->durationSum) return -1;
                    if ($a->durationSum > $b->durationSum) return 1;
                    return 0;
                };
                break;
            default:
                throw new Exception('Wrong algorithm [' . $algorithm . ']');
                break;
        }

        /** @var Deal[] $leftDeals */
        $leftDeals = [];

        // Init ways by first deals.
        // There are only one place where we use &
        // All next places we will operate with link to deal.
        foreach ($this->deals as &$deal) {
            $deal->flushSum();
            $deal->previousDeal = null;
            $deal->checked = false;
            if ($deal->departure != $departure) continue;
            $deal->costSum += $deal->cost * ((100 - $deal->discount) / 100);
            $deal->durationSum += $deal->duration->getMinutes();
            $deal->citiesWas[$deal->departure] = $deal->departure;
            $leftDeals[] = $deal;
        }
        unset($deal); // We will use this variable later

        /** @var float $minimalRequestedValue */
        $minimalRequestedValue = null;
        /** @var Deal $minimalRequestedDeal */
        $minimalRequestedDeal = null;

        // Main loop of searching.
        // Idea is going over all nearest-requested deals.
        do {
            // Sorting - mostly wonted deal should be first.
            usort($leftDeals, $algorithmCompare);
            $requestedDeal = $leftDeals[0];
            if ($requestedDeal->arrival == $arrival) {
                switch ($algorithm) {
                    case 'cheapest':
                        $minimalNewValue = $requestedDeal->costSum;
                        break;
                    case 'fastest':
                        $minimalNewValue = $requestedDeal->durationSum;
                        break;
                    default:
                        throw new Exception('Wrong algorithm [' . $algorithm . ']');
                        break;
                }
                // Bugfix 10-10-30 < 20-10-10
                // Without $minimalRequestedValue we will choice wrong case
                if (($minimalRequestedValue === null) ||
                    (($minimalRequestedValue !== null) && ($minimalRequestedValue > $minimalNewValue))) {
                    $minimalRequestedValue = $minimalNewValue;
                    $minimalRequestedDeal = $requestedDeal;
                }
            }
            // Mark as checked and remove from active-search-array
            $requestedDeal->checked = true;
            unset($leftDeals[0]);
            $nextDeals = $this->getAllNextDeals($requestedDeal);
            foreach ($nextDeals as $deal) {
                $tripCost = $deal->cost * ((100 - $deal->discount) / 100);
                $newDeal = ($deal->costSum == 0);
                switch ($algorithm) {
                    case 'cheapest':
                        $betterWay = ($deal->costSum >= $requestedDeal->costSum + $tripCost);
                        break;
                    case 'fastest':
                        $betterWay = ($deal->durationSum >= $requestedDeal->durationSum + $deal->duration);
                        break;
                    default:
                        throw new Exception('Wrong algorithm [' . $algorithm . ']');
                        break;
                }
                // If we find better way to the specific city - apply it
                if ((!$newDeal) && (!$betterWay)) continue;
                $deal->checked = false;
                $deal->costSum = $requestedDeal->costSum + $tripCost;
                $deal->durationSum = $requestedDeal->durationSum + $deal->duration->getMinutes();
                $deal->citiesWas = $requestedDeal->citiesWas;
                $deal->citiesWas[$deal->departure] = $deal->departure;
                $deal->previousDeal = $requestedDeal;
                if ($newDeal) {
                    switch ($algorithm) {
                        case 'cheapest':
                            $willAddToLeft = ($minimalRequestedValue === null) ||
                                ($minimalRequestedValue < $requestedDeal->costSum);
                            break;
                        case 'fastest':
                            $willAddToLeft = ($minimalRequestedValue === null) ||
                                ($minimalRequestedValue < $requestedDeal->durationSum);
                            break;
                        default:
                            throw new Exception('Wrong algorithm [' . $algorithm . ']');
                            break;
                    }
                    if ($willAddToLeft) {
                        $leftDeals[] = $deal;
                    }
                }
            }
            unset($requestedDeal); // Free memory - link
        } while (count($leftDeals) > 0);
        if ($minimalRequestedDeal === null) {
            throw new Exception('Do not find way');
        }
        return $minimalRequestedDeal;
    }

    /**
     * @param Deal $requestedDeal
     *
     * @return Deal[]
     */
    private function getAllNextDeals(Deal &$requestedDeal)
    {
        $newDeals = [];

        $wasCity = $requestedDeal->getCitiesWasArrival();
        foreach ($this->deals as &$deal) {
            if ($deal->departure != $requestedDeal->arrival) continue;
            if (in_array($deal->arrival, $wasCity)) continue;
            $newDeals[] = $deal;
        }

        return $newDeals;
    }
}