<?php

namespace App\Http\Controllers;

use Exception;
use App\Person;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\PersonResource;
use App\Http\Resources\PersonResourceCollection;
use Illuminate\Support\Facades\Cache;

class PersonController extends Controller
{

    public $ttl = 1;

    /**
     * @param Person $person
     * @return PersonResource
     */
    public function show(Person $person)
    {
        try {
            // ttl check
            $returnPerson = $this->ttl('show', $person);
            if (!empty($returnPerson)) {
                return (new PersonResource($returnPerson))->response()
                    ->setStatusCode(200);
            } else {
                return response()->json([
                    'message' => 'No Data found'
                ], 404);
            }
        } catch (Exception $ex) {
            abort(500, $ex->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return PersonResourceCollection
     */
    public function multi(Request $request)
    {
        $parsonList = [];
        $ids = $request->ids;
        if (isset($request->ids)) {
            $idList = explode(',', $ids);
            foreach ($idList as $k => $id) {
                if ($singlePerson = Person::find($id)) {
                    $parsonList[] = $singlePerson;
                }
            }
            if (!empty($parsonList)) {
                // ttl check
                $returnPerson = $this->ttl('multi', $parsonList);
                if (!empty($returnPerson)) {
                    return (new PersonResourceCollection($returnPerson))
                        ->response()
                        ->setStatusCode(200);
                } else {
                    return response()->json([
                        'message' => 'No Data found'
                    ], 404);
                }
            } else {
                return response()->json([
                    'message' => 'No data found. In those Id\'s'
                ], 404);
            }
        } else {
            return response()->json([
                'message' => 'Invalid Id Formate.'
            ], 404);
        }
    }

    /**
     * @return PersonResourceCollection
     */
    public function index()
    {
        try {
            $person = Person::get();
            if (!$person->isEmpty()) {
                $returnPerson = $this->ttl('index', $person);
                if (!empty($returnPerson)) {
                    return (new PersonResourceCollection($returnPerson))
                        ->response()
                        ->setStatusCode(200);
                } else {
                    return response()->json([
                        'message' => 'No Data found'
                    ], 404);
                }
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
            $person->update($request->all());
            $returnPerson = $this->ttl('updatePerson_' . $person->id, $person, true);
            if (!empty($returnPerson)) {
                return (new PersonResource($returnPerson))
                    ->response()
                    ->setStatusCode(202); // 202 Accepted
            } else {
                return response()->json([
                    'message' => 'No Data found'
                ], 404);
            }
        } catch (Exception $ex) {
            abort(500, $ex->getMessage());
        }
    }

    /**
     * @param $cacheId
     * @param $person
     * @param $update
     * @return Cache
     */
    public function ttl($cacheId = '', $person = [], $update = false)
    {
        if (!empty($cacheId) && !empty($person)) {
            if (!Cache::has($cacheId)) {
                Cache::remember($cacheId, now()->addMinutes($this->ttl), function () use ($person) {
                    return $person;
                });
                return Cache::get($cacheId);
            } else {
                if ($update) {
                    // update cache
                    Cache::forget($cacheId);
                    Cache::remember($cacheId, now()->addMinutes($this->ttl), function () use ($person) {
                        return $person;
                    });
                    return Cache::get($cacheId);
                } else {
                    return Cache::get($cacheId);
                }
            }
        } else {
            return [];
        }
    }
}
