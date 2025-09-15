<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'school_id' => $this->school_id,
            'email_verified_at' => $this->email_verified_at ? $this->email_verified_at->toIso8601String() : null,

            //null safe
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
             // Add Spatie roles and permissions
             'roles' => $this->getRoleNames(), // Always get role names
             'permissions' => $this->getAllPermissions()->pluck('name'), // Always get permission names
            // DO NOT include password or remember_token
        ];
    }
}