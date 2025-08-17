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
            // Map to a simple role label for frontend compatibility: only 'admin' else null
            'role' => (method_exists($this->resource, 'getRoleNames') && $this->resource->getRoleNames()->contains('admin')) ? 'admin' : null,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'email_verified_at' => $this->email_verified_at ? $this->email_verified_at->toIso8601String() : null,

            //null safe
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
             // Add Spatie roles and permissions
             'roles' => $this->whenLoaded('roles', fn() => $this->resource->getRoleNames()), // Collection of role names
             'permissions' => $this->whenLoaded('permissions', fn() => $this->resource->getAllPermissions()->pluck('name')), // Collection of permission names
            // DO NOT include password or remember_token
        ];
    }
}