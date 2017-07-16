<?php

namespace App\Services\v1;

use App\Flight;
use App\Airport;

class FlightsService
{
	protected $supportedIncludes = [
		'arrivalAirport' => 'arrival',
		'departureAirport' => 'departure'
	];

	//query searches
	protected $clauseProperties = [
		'status',
		'flightNumber'
	];

	public function getFlights($parameters)
	{
		if(empty($parameters)) {
			return $this->filterFlights(Flight::all());
		}
		//query string parameter by "include"
		$withKeys = $this->getWithKeys($parameters); 
		//query searches
		$whereClauses = $this->getWhereClause($parameters);

		$flights = Flight::with($withKeys)->where($whereClauses)->get();


		return $this->filterFlights($flights, $withKeys);
	}

	public function createFlight($req)
	{
		$arrivalAirport = $req->input('arrival.iataCode');
		$departureAirport = $req->input('departure.iataCode');
		//filter -> make sure that airport is exist in DB
		$airports = Airport::whereIn('iataCode',[$arrivalAirport,$departureAirport])->get();

		$codes = [];

		foreach ($airports as $port) {
			$codes[$port->iataCode] = $port->id;
		}

		$flight = new Flight();
		$flight->flightNumber = $req->input('flightNumber');
		$flight->status = $req->input('status');
		$flight->arrivalAirPort_id = $codes[$arrivalAirport];
		$flight->arrivalDateTime = $req->input('arrival.datetime');
		$flight->departureAirPort_id = $codes[$departureAirport];
		$flight->departureDateTime = $req->input('departure.datetime');

		$flight->save();

		return $this->filterFlights([$flight]);
	}
	//hasil
	// {
 //        "flightNumber": "JWM12345",
 //        "status": "ontime",
 //        "href": "http://localhost/laravel/latihan/API/airview_api/public/api/v1/flights/JWM12345"
 //    }


	//flight.show
	// public function getFlight($flightNumber)
	// {
	// 	return $this->filterFlights(Flight::where('flightNumber',$flightNumber)->get());
	// }
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

    protected function getWithKeys($parameters)
    {
    	$withKeys = [];
    	if(isset($parameters['include'])) {
    		$includeParams = explode(',', $parameters['include']);
    		//intersection array
    		$includes = array_intersect($this->supportedIncludes, $includeParams);

    		$withKeys = array_keys($includes);
    	}

    	return $withKeys;
    }

    protected function getWhereClause($parameters)
    {
    	$clause = [];

    	foreach ($this->clauseProperties as $prop) {
    		if(in_array($prop, array_keys($parameters))) {
    			$clause[$prop] = $parameters[$prop];
    		}
    	}

    	return $clause;
    }
}