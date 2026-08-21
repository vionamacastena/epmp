<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'subdomain' => $this->subdomain,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'logo' => $this->logo,
            'website' => $this->website,
            'industry' => $this->industry,
            'plan' => $this->plan,
            'status' => $this->status,
            'settings' => $this->settings,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'creator' => $this->creator ? [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
                'email' => $this->creator->email,
            ] : null,
        ];
    }
}
