<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FoodResource extends JsonResource
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
        $data['restaurant'] = new RestaurantResource($data['restaurant']);
        unset($data['id']);
        unset($data['uid']);
        unset($data['restaurant_id']);
        unset($data['created_at']);
        unset($data['updated_at']);
        return $data;
    }
}
