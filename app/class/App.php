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
            $deal = new Deal();
            $deal->transport = $sourceDeal->transport;
            $deal->departure = $sourceDeal->departure;
            $deal->arrival = $sourceDeal->arrival;
            $deal->duration = new Duration($sourceDeal->duration->h, $sourceDeal->duration->m);
            $deal->cost = $sourceDeal->cost;
            $deal->discount = $sourceDeal->discount;
            $deal->reference = $sourceDeal->reference;
            $this->deals[] = $deal;
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
     * @return Deal[]
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
     * @return Deal[]
     */
    private function findPathCheapest(string $departure, string $arrival)
    {
        $deals = [];
        return $deals;
    }

    /**
     * @param string $departure
     * @param string $arrival
     *
     * @return Deal[]
     */
    private function findPathFastest(string $departure, string $arrival)
    {
        $deals = [];
        return $deals;
    }
}