<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
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
			'id'              => $this->id,
			'title'           => $this->getTranslations('title'),
			'description'     => $this->getTranslations('description'),
			'director'        => $this->getTranslations('director'),
			'slug'            => $this->slug,
			'thumbnail'       => $this->thumbnail,
			'income'          => $this->income,
			'release_date'    => (int)$this->release_date,
			'user_id'         => $this->user_id,
			'created_at'      => $this->created_at,
			'updated_at'      => $this->updated_at,
			'thumbnail'       => $this->thumbnail,
			'author'          => new UserResource($this->author),
			'quotes'          => QuoteResource::collection($this->whenLoaded('quotes')),
			'genres'          => GenreResource::collection($this->whenLoaded('genres')),
		];
	}
}
