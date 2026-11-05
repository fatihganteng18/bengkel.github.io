<?php

namespace App\Http\Controllers;

use App\Models\ServiceQueue;
use Illuminate\View\View;

class ServiceQueueDisplayController extends Controller
{
    public function __invoke(): View
    {
        return $this->renderQueueDisplay(false);
    }

    public function admin(): View
    {
        return $this->renderQueueDisplay(true);
    }

    private function renderQueueDisplay(bool $adminLayout): View
    {
        $queues = ServiceQueue::query()
            ->with(['store', 'booking.items.product'])
            ->whereDate('queue_date', now()->toDateString())
            ->whereIn('status', [
                ServiceQueue::STATUS_WAITING,
                ServiceQueue::STATUS_IN_SERVICE,
                ServiceQueue::STATUS_COMPLETED,
            ])
            ->orderByRaw("CASE status WHEN 'in_service' THEN 0 WHEN 'waiting' THEN 1 ELSE 2 END")
            ->orderBy('queue_number')
            ->get()
            ->groupBy(fn (ServiceQueue $queue): string => $queue->store->name);

        return view('queues.display', compact('queues', 'adminLayout'));
    }
}
