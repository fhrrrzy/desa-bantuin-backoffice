<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $attachments = [];
        if ($this->attachment && is_array($this->attachment)) {
            foreach ($this->attachment as $attachment) {
                $attachments[] = [
                    'filename' => basename($attachment),
                    'url' => asset('storage/' . $attachment),
                    'path' => $attachment,
                ];
            }
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'laporan_type' => [
                'id' => $this->laporanType->id,
                'name' => $this->laporanType->name,
            ],
            'attachment' => $attachments,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
