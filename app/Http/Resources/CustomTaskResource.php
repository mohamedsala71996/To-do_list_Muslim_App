<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CustomTaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'task_name' => $this->task_name,
            'percentage' => $this->percentage,
        ];

        // Conditionally add fields if they are not null
        if (!is_null($this->description)) {
            $data['description'] = $this->description;
        }

        if (!is_null($this->photo)) {
            $data['photo'] = $this->photo;
        }

        // if (!is_null($this->time_id)) {
        //     $data['time'] = $this->time->time; // Assuming 'time' is a related model
        // }

        return $data;
    }
}
