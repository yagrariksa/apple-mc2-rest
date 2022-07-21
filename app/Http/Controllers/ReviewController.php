<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReviewResource;
use App\Http\Resources\UserResource;
use App\Models\Review;
use App\Models\ReviewImage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Uuid;

use function PHPSTORM_META\type;

class ReviewController extends Controller
{
    public function test(Request $request)
    {
        $u = Auth::user();
        return response()->json([
            'message' => 'success login',
            'data' => [
                'api_token' => $u->api_token,
                'user' => new UserResource($u)
            ]
        ], 201);
        // return true;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Review::with(['food', 'food.restaurant', 'user', 'images']);

        if ($request->query('FDA')) {
            $fda = $request->query('FDA');
            $fda = explode(',', $fda);

            $data->where(function ($query) use ($fda) {
                foreach ($fda as $f) {
                    $query->orWhere('FDA', $f);
                }
            });
        }

        if ($request->query('rating')) {
            $data->where('rating', '>=', $request->query('rating'));
        }

        if ($request->query('price')) {
            $price = $request->query('price');
            $price = explode(',', $price);
            $data->where(function ($query) use ($price) {
                foreach ($price as $p) {
                    switch ($p) {
                        case 1:
                            $query->orWhere(function ($q) {
                                $q->where('price', '>=', '0')->where('price', '<=', '16000');
                            });
                            break;

                        case 2:
                            $query->orWhere(function ($q) {
                                $q->where('price', '>=', '16001')->where('price', '<=', '40000');
                            });
                            break;

                        case 3:
                            $query->orWhere(function ($q) {
                                $q->where('price', '>=', '40001')->where('price', '<=', '100000');
                            });
                            break;

                        case 4:
                            $query->orWhere(function ($q) {
                                $q->where('price', '>', '100000');
                            });
                            break;

                        default:
                            # code...
                            break;
                    }
                }
            });
        }

        $data->orderBy('created_at', 'desc');
        // dump($data->toSql());

        $data = $data->get();
        if (sizeof($data) < 1) {
            return response()->json([
                'message' => 'not found data',
                'data' => []
            ], 404);
        }
        return response()->json([
            'message' => 'retrieve ' . sizeof($data) . ' reviews data',
            'data' => ReviewResource::collection($data)
        ]);
    }

    public function my(Request $request)
    {
        $user = Auth::user();
        $user = User::with('reviews', 'reviews.user', 'reviews.food', 'reviews.food.restaurant', 'reviews.images')->find($user->id);
        $reviews = $user->reviews;
        return response()->json([
            'message' => 'retrieve my reviews data',
            'data' => ReviewResource::collection($reviews)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rule = [
            'food_id' => 'required',
            'desc' => 'required',
            'rating' => 'required|integer',
            'price' => 'required|integer',
            'FDA' => 'required',
            'porsi' => 'required',
            'images' => Rule::requiredIf(!$request->hasFile('images[]')),
            'images[]' => Rule::requiredIf(!$request->hasFile('images')),
        ];

        $validator = Validator::make($request->all(), $rule);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'complete the field that required',
                'data' => $validator->errors()
            ], 422);
        }

        $review = Review::create([
            'uid' => Uuid::uuid4(),
            'user_id' => Auth::user()->id,
            'food_id' => $request->food_id,
            'desc' => $request->desc,
            'rating' => $request->rating,
            'price' => $request->price,
            'FDA' => $request->FDA,
            'porsi' => $request->porsi
        ]);


        if ($request->hasFile('images') && gettype($request->images) == 'array') {
            foreach ($request['images'] as $image) {
                $nameimg = time() . "_" . $image->getClientOriginalName();
                $image->storeAs('public', $nameimg);

                ReviewImage::create([
                    'review_id' => $review->id,
                    'filename' => $nameimg
                ]);
            }
        } else
        if ($request->hasFile('images[]') && gettype($request['images[]']) == 'array') {
            foreach ($request['images[]'] as $image) {
                $nameimg = time() . "_" . $image->getClientOriginalName();
                $image->storeAs('public', $nameimg);

                ReviewImage::create([
                    'review_id' => $review->id,
                    'filename' => $nameimg
                ]);
            }
        } else
        if ($request->hasFile('images')) {
            $nameimg = time() . "_" . $request->images->getClientOriginalName();
            $request->images->storeAs('public', $nameimg);

            ReviewImage::create([
                'review_id' => $review->id,
                'filename' => $nameimg
            ]);
        } else
        if ($request->hasFile('images[]')) {
            $nameimg = time() . "_" . $request['images[]']->getClientOriginalName();
            $request['images[]']->storeAs('public', $nameimg);

            ReviewImage::create([
                'review_id' => $review->id,
                'filename' => $nameimg
            ]);
        }

        $review = Review::with(['images', 'user', 'food', 'food.restaurant'])->find($review->id);

        return response()->json([
            'message' => 'success post new review',
            'data' => new ReviewResource($review)
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $review)
    {
        $review = Review::with(['food', 'food.restaurant', 'user', 'images'])->find($review);

        if (!$review) {
            return response()->json([
                'message' => 'data not found',
                'data' => []
            ], 404);
        }

        return response()->json([
            'message' => 'retrieve spesific review',
            'data' => new ReviewResource($review)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function edit(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Review $review)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function destroy(Review $review)
    {
        //
    }
}
