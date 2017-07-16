<?php

namespace App\Services\v1;

use App\Flight;

class FlightsService
{
	public function getFlights()
	{
		// return Flight::all();
		return $this->filterFlights(Flight::all());
	}

	//filtering flight
	protected function filterFlights($flights)
	{
		$data = [];

		foreach ($flights as $flight) {
			$entry = [
				'flightNumber' => $flight->flightNumber,
				'status' => $flight->status,
				'href' => route('flights.show',['id'=>$flight->flightNumber])
			];

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
}