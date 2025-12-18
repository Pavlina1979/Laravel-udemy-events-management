<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SendEventReminders extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:send-event-reminders';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Sends notifications to all event attendees that event is near';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $events = Event::with('attendees.user')->whereBetween('start_time', [now(), now()->addDay()])->get();

    $eventCount = $events->count();
    $eventLabe = Str::plural('event', $eventCount);

    $this->info("Found {$eventCount} {$eventLabe}");

    $events->each(fn($event) => $event->attendees->each(fn($attendee) => $this->info("Notifying the user {$attendee->user->id}")));

    $this->info('Hello');
  }
}
