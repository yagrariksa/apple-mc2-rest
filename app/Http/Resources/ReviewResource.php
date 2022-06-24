<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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

        if (!is_int($data['rating'])) {
            $data['rating'] = intval($data['rating']);
        }
        if (!is_int($data['price'])) {
            $data['price'] = intval($data['price']);
        }

        $data['url'] = [];
        $data['url']['all'] = route('api.review.index');
        $data['url']['details'] = route('api.review.show', $data['id']);
        if (array_key_exists('food', $data)) {
            $data['food'] = new FoodResource($data['food'], 'without_id');
        }
        if (array_key_exists('user', $data)) {
            $data['user'] = new UserResource($data['user']);
        }

        $images = [];
        foreach ($data['images'] as $img) {
            array_push($images, $img['filename']);
        }

        $data['images'] = $images;
        unset($data['uid']);
        unset($data['user_id']);
        unset($data['food_id']);

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
