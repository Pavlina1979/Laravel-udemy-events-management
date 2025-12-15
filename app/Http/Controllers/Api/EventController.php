<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{

  use CanLoadRelationships;
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $relations = ['user', 'attendees', 'attendees.user'];
    $query = $this->loadRelationship(Event::query());


    return EventResource::collection($query->latest()->paginate());
  }



  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $data = $this->validate($request);

    $event = Event::create([...$data, 'user_id' => 1]);

    return new EventResource($event);
  }


  /**
   * Display the specified resource.
   */
  public function show(int $id)
  {
    return new EventResource(Event::find($id)->load('user', 'attendees'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    $event = Event::find($id);
    $data = $this->validate($request, 'sometimes');

    $event->update($data);

    return new EventResource($event);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(int $id)
  {
    $event = Event::find($id);
    $eventName = $event->name;
    $event->delete();

    return response(status: 204);
  }

  private function validate($request, $rule = 'required')
  {
    return $request->validate([
      'name' => $rule . '|string|max:255',
      'description' => 'nullable|string',
      'start_time' => $rule . '|date',
      'end_time' => $rule . '|date|after:start_time'
    ]);
  }
}
