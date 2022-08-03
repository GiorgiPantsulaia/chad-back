<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuoteResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		return [
			'id'        => $this->id,
			'user_id'   => $this->user_id,
			'movie_id'  => $this->movie_id,
			'body'      => $this->getTranslations('body'),
			'thumbnail' => $this->thumbnail,
			'author'    => new UserResource($this->author),
			'movie'     => new MovieResource($this->movie),
			'likes'     => LikeResource::collection($this->likes),
			'comments'  => CommentResource::collection($this->comments),
			'created_at'=> $this->created_at,
			'updated_at'=> $this->updated_at,
		];
	}
}
