<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{

  use CanLoadRelationships;

  private array $relations = ['user', 'attendees', 'attendees.user'];


  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    // Gate::authorize('viewAny', Event::class);
    $query = $this->loadRelationship(Event::query());


    return EventResource::collection($query->latest()->paginate());
  }



  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $data = $this->validate($request);

    $event = Event::create([...$data, 'user_id' => $request->user()->id]);

    return new EventResource($this->loadRelationship($event));
  }


  /**
   * Display the specified resource.
   */
  public function show(int $id)
  {
    return new EventResource($this->loadRelationship(Event::find($id)));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    $event = Event::find($id);
    if (Gate::denies('update-event', $event)) {
      abort(403, 'You are not authorized to update this Event');
    }

    $data = $this->validate($request, 'sometimes');

    $event->update($data);

    return new EventResource($this->loadRelationship($event));
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
