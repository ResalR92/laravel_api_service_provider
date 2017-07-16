<?php

namespace App\Services\v1;

use App\Flight;

class FlightsService
{
	public function getFlight()
	{
		return Flight::all();
	}
}