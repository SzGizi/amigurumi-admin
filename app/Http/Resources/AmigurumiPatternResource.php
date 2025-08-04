<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AmigurumiPatternResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        
         return [
            'id'                => $this->id,
            'title'             => $this->title,
            'image_path'        => $this->image_path,
            'yarn_description'  => $this->yarn_description,
            'tools_description' => $this->tools_description,
            'final_size'        => $this->final_size,
            'difficulty'        => $this->difficulty,
            'introduction'      => $this->introduction,
            'abbreviations'     => $this->abbreviations,

            'sections'          => AmigurumiSectionResource::collection($this->whenLoaded('amigurumiSections')),
            'created_at'        => $this->created_at->toISOString(),
            'updated_at'        => $this->updated_at->toISOString(),
        ];
    
    }
}
