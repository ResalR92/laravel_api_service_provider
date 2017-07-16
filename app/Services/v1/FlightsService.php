<?php

namespace App\Services\v1;

use App\Flight;

class FlightsService
{
	protected $supportedIncludes = [
		'arrivalAirport' => 'arrival',
		'departureAirport' => 'departure'
	];

	public function getFlights($parameters)
	{
		if(empty($parameters)) {
			return $this->filterFlights(Flight::all());
		}
		//query string parameter by "include"
		$withKeys = [];
		if(isset($parameters['include'])) {
			$includeParams = explode(',', $parameters['include']);
			//intersection array
			$includes = array_intersect($this->supportedIncludes, $includeParams);

			$withKeys = array_keys($includes);
		}
		return $this->filterFlights(Flight::with($withKeys)->get(), $withKeys);
	}

	//flight.show
	public function getFlight($flightNumber)
	{
		return $this->filterFlights(Flight::where('flightNumber',$flightNumber)->get());
	}
	//hasil
	// {
 //        "flightNumber": "1yn69572",
 //        "status": "delayed",
 //        "href": "http://localhost/laravel/latihan/API/airview_api/public/api/v1/flights/1yn69572"
 //    }

	//filtering flight
	protected function filterFlights($flights, $keys = [])
	{
		$data = [];

		foreach ($flights as $flight) {
			$entry = [
				'flightNumber' => $flight->flightNumber,
				'status' => $flight->status,
				'href' => route('flights.show',['id'=>$flight->flightNumber])
			];

			if(in_array('arrivalAirport',$keys)) {
				$entry['arrival'] = [
					'datetime' => $flight->arrivalDateTime,
					'iataCode' => $flight->arrivalAirport->iataCode,
					'city' => $flight->arrivalAirport->city,
					'state' => $flight->arrivalAirport->state
				];
			}

			if(in_array('departureAirport',$keys)) {
				$entry['departure'] = [
					'datetime' => $flight->departureDateTime,
					'iataCode' => $flight->departureAirport->iataCode,
					'city' => $flight->departureAirport->city,
					'state' => $flight->departureAirport->state
				];
			}

			$data[] = $entry;
		}

		return $data;
	}
	//hasil
	// {
    //     "flightNumber": "1yn69572",
    //     "status": "delayed",
    //     "href": "http://localhost/laravel/latihan/API/airview_api/public/api/v1/flights/1yn69572"
    // }

    //query string parameter by "include"
	// localhost/laravel/latihan/API/airview_api/public/api/v1/flights?include=arrival,departure
    // {
    //     "flightNumber": "1yn69572",
    //     "status": "delayed",
    //     "href": "http://localhost/laravel/latihan/API/airview_api/public/api/v1/flights/1yn69572",
    //     "arrival": {
    //         "datetime": "1981-04-02 14:30:31",
    //         "iataCode": "wuV",
    //         "city": "Lake Delia",
    //         "state": "TX"
    //     },
    //     "departure": {
    //         "datetime": "1981-04-02 09:30:31",
    //         "iataCode": "aJM",
    //         "city": "East Lillian",
    //         "state": "WY"
    //     }
    // }
}