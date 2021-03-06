<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $data['image'] = 'https://i.pravatar.cc/150?u=' . $data['email'];
        // if (!str_contains($data['image'], 'https')) {
        //     $data['image'] = url('/storage') . '/' . $data['image'];
        // }
        unset($data['id']);
        unset($data['email_verified_at']);
        unset($data['api_token']);
        unset($data['created_at']);
        unset($data['updated_at']);
        return $data;
    }
}
