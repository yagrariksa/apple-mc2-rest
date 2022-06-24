<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FoodResource extends JsonResource
{
    private $own_rule;

    public function __construct($resource, $rule = "nothing")
    {
        parent::__construct($resource);
        $this->own_rule = $rule;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        if (array_key_exists('restaurant', $data)) {
            $data['restaurant'] = new RestaurantResource($data['restaurant'], 'without_id');
        }
        if (array_key_exists('reviews', $data)) {
            $data['reviews'] = ReviewResource::collection($data['reviews']);
        }
        unset($data['uid']);
        unset($data['restaurant_id']);
        unset($data['created_at']);
        unset($data['updated_at']);

        try {
            if ($this->own_rule != "nothing") {
                $data = $this->{$this->own_rule}($data);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        return $data;
    }

    private function without_id($request)
    {
        unset($request['id']);
        return $request;
    }
}
