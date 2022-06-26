<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
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
        if (array_key_exists('foods', $data)) {
            $data['foods'] = FoodResource::collection($data['foods']);
        }
        if (array_key_exists('reviews', $data)) {
            $data['reviews'] = ReviewResource::collection($data['reviews']);
        }
        $data['url'] = [];
        $data['url']['all'] = route('api.restaurant.index');
        $data['url']['details'] = route('api.restaurant.show', $data['id']);
        unset($data['uid']);
        unset($data['created_at']);
        unset($data['updated_at']);

        return $data;
    }
}
