<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
        $data['url'] = [];
        $data['url']['all'] = route('api.review.index');
        $data['url']['details'] = route('api.review.show', $data['id']);
        if (array_key_exists('food', $data)) {
            $data['food'] = new FoodResource($data['food']);
        }
        if (array_key_exists('user', $data)) {
            $data['user'] = new UserResource($data['user']);
        }
        unset($data['id']);
        unset($data['uid']);
        unset($data['user_id']);
        unset($data['food_id']);
        return $data;
    }
}
