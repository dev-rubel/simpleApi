<?php

namespace App\Http\Controllers;

use Exception;
use App\Person;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\PersonResource;
use App\Http\Resources\PersonResourceCollection;

class PersonController extends Controller
{
    /**
     * @param Person $person
     * @return PersonResource
     */
    public function show(Person $person)
    {
        try {
            // remove person after ttl
            $result = $this->deletePerson($person->id);
            if (!$result) {
                // if person alive :D
                return (new PersonResource($person))->response()
                    ->setStatusCode(200);
            } else {
                return response()->json([
                    'message' => 'Entry for Person not found'
                ], 404);
            }
        } catch (Exception $ex) {
            abort(500, $ex->getMessage());
        }
    }

    /**
     * @return PersonResourceCollection
     */
    public function index()
    {
        try {
            // remove person after ttl
            // it can also done by laravel task schedule (and i think task scheduler are the best method for this situation(Time to live))
            $this->deletePerson();
            $person = Person::get();
            if (!$person->isEmpty()) {
                return (new PersonResourceCollection($person))
                    ->response()
                    ->setStatusCode(200);
            } else {
                return response()->json([
                    'message' => 'No Person found'
                ], 404);
            }
        } catch (Exception $ex) {
            abort(500, $ex->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return PersonResource
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'first_name'    => 'required',
                'last_name'     => 'required',
                'email'         => 'required',
                'phone'         => 'required',
                'city'          => 'required'
            ]);
            $person = Person::create($request->all());
            return (new PersonResource($person))
                ->response()
                ->setStatusCode(200);
        } catch (Exception $ex) {
            abort(500, $ex->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param Person $person
     * @return PersonResource
     */
    public function update(Request $request, Person $person)
    {
        try {
            // remove person after ttl
            $result = $this->deletePerson($person->id);
            if (!$result) {
                // if person alive :D
                $person->update($request->all());
                return (new PersonResource($person))
                    ->response()
                    ->setStatusCode(202); // 202 Accepted
            } else {
                return response()->json([
                    'message' => 'Entry for Person not found'
                ], 404);
            }
        } catch (Exception $ex) {
            abort(500, $ex->getMessage());
        }
    }

    /**
     * @param $id = null
     * @return $result
     */
    public function deletePerson($id = null)
    {
        // ttl(Time to live)
        $ttl = 2;
        $result = Person::where('created_at', '<', Carbon::now()->subMinutes($ttl))
            ->when($id != null, function ($q) use ($id) {
                $q->where('id', $id);
            })->delete();
        return $result;
    }
}
