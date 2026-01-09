<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status->value,
            'due_date' => optional($this->due_date)->toDateString(),
            'created_at' => $this->created_at->toDateTimeString(),
            'deleted_at' => optional($this->deleted_at)->toDateTimeString(),
            'is_trashed' => isset($this->resource) && method_exists($this->resource, 'trashed') ? $this->resource->trashed() : (bool) $this->deleted_at,
        ];
    }
}
