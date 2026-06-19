<?php

namespace Alyani\Subsystem\Http\Resources;

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
            'name' => $this->name,
            'family' => $this->family,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'avatarSID' => $this->avatarSID,
            'last_activity' => toJalaliDate($this->last_activity, 'Y/m/d H:i:s'),
            'email_is_verified' => (bool) $this->email_verified_at,
            'mobile_is_verified' => (bool) $this->mobile_verified_at,
        ];
    }
}
