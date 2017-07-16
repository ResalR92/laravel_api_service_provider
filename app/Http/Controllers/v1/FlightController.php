<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Services\v1\FlightsService;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class FlightController extends Controller
{
    protected $flights;
    //dependency injection
    public function __construct(FlightsService $service)
    {
        $this->flights = $service;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get another information about arrival, departure, etc
        $parameters = request()->input();

        $data = $this->flights->getFlights($parameters);

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $flight = $this->flights->createFlight($request);
            return response()->json($flight, 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //query searches
        $parameters = request()->input();
        $parameters['flightNumber'] = $id;
        $data = $this->flights->getFlights($parameters);

        return response()->json($data);
    }
    //hasil query searches
    // localhost/laravel/latihan/API/airview_api/public/api/v1/flights/1yn69572?include=arrival,departure&status=delayed
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $flight = $this->flights->updateFlight($request,$id);
            return response()->json($flight, 200);
        } 

        catch (ModelNotFoundException $ex) {
            throw $ex;
        }

        catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
