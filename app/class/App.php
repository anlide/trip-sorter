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
        switch ($algorithm) {
            case 'cheapest':
                return $this->findPathCheapest($departure, $arrival);
                break;
            case 'fastest':
                return $this->findPathFastest($departure, $arrival);
                break;
            default:
                throw new Exception('Wrong algorithm [' . $algorithm . ']');
                break;
        }
    }

    /**
     * @param string $departure
     * @param string $arrival
     *
     * @throws Exception
     *
     * @return Deal
     */
    private function findPathCheapest(string $departure, string $arrival)
    {
        /** @var Deal[] $leftDeals */
        $leftDeals = [];

        // Init ways by first deal.
        foreach ($this->deals as &$deal) {
            $deal->flushSum();
            if ($deal->departure != $departure) continue;
            $deal->costSum += $deal->cost * ((100 - $deal->discount) / 100);
            $deal->durationSum += $deal->duration->getMinutes();
            $deal->citiesWas[$deal->departure] = $deal->departure;
            $leftDeals[] = $deal;
        }
        do {
            usort($leftDeals, function (Deal $a, Deal $b) {
                if ($a->costSum < $b->costSum) return -1;
                if ($a->costSum > $b->costSum) return 1;
                return 0;
            });
            $cheapestDeal = $leftDeals[0];
            if ($cheapestDeal->arrival == $arrival) {
                return $cheapestDeal;
            }
            $cheapestDeal->checked = true;
            unset($leftDeals[0]);
            $nextDeals = $this->getAllNextDeals($cheapestDeal);
            foreach ($nextDeals as $deal) { // Here link without &
                $tripCost = $deal->cost * ((100 - $deal->discount) / 100);
                $newDeal = ($deal->costSum == 0);
                if ((!$newDeal) && ($deal->costSum < $cheapestDeal->costSum + $tripCost)) continue;
                $deal->checked = false;
                $deal->costSum = $cheapestDeal->costSum + $tripCost;
                $deal->durationSum = $cheapestDeal->durationSum + $deal->duration->getMinutes();
                $deal->citiesWas = $cheapestDeal->citiesWas;
                $deal->citiesWas[$deal->departure] = $deal->departure;
                $deal->previousDeal = $cheapestDeal;
                if ($newDeal) {
                    $leftDeals[] = $deal;
                }
            }
        } while (count($leftDeals) > 0);
        throw new Exception('Do not find way');
    }

    /**
     * @param string $departure
     * @param string $arrival
     *
     * @return Deal
     */
    private function findPathFastest(string $departure, string $arrival)
    {
        return null;
    }

    /**
     * @param Deal $cheapestDeal
     *
     * @return Deal[]
     */
    private function getAllNextDeals(Deal &$cheapestDeal)
    {
        $newDeals = [];

        $wasCity = $cheapestDeal->getCitiesWasArrival();
        foreach ($this->deals as &$deal) {
            if ($deal->departure != $cheapestDeal->arrival) continue;
            if (in_array($deal->arrival, $wasCity)) continue;
            $newDeals[] = $deal;
        }

        return $newDeals;
    }
}