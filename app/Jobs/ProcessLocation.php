<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserLocation;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use App\Http\Services\Gis\GisService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Http\Services\Glific\GlificService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ProcessLocation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $locationId;
    protected $flowId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $locationId, $flowId)
    {
        $this->userId = $userId;
        $this->locationId = $locationId;
        $this->flowId = $flowId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("It ran after response");
        $user = User::find($this->userId);
        $userLocation = UserLocation::find($this->locationId);
        $data =  $userLocation->getLocationMeta()['address_components'];
        $gisData =  app(GisService::class)->getGisBoundary($userLocation->latitude, $userLocation->longitude);
        $data = array_merge($data, $gisData);
        $service  =  app(GlificService::class);

        $contact = $service->getContactByPhone($user->phone);
        if (!$contact) {
           return false;
        }

        $service->updateContactField($contact, $data);
        if ($this->flowId) {
            $service->resumeFlowForContact($contact, $this->flowId);
        }
    }
}
