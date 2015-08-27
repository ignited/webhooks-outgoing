<?php
namespace Ignited\Webhooks\Outgoing\Jobs;

use Ignited\Webhooks\Outgoing\Facades\Webhooks;
use Ignited\Webhooks\Outgoing\Models\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class WebhookJob implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        $response = Webhooks::fire($this->request);

        // Logic here to handle the response...

        // Implement Backoff Strategy...

        // But for now just consider everything went to plan... :)
        $this->delete();
    }
}
