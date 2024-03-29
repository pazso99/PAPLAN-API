<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'token' => $this->createToken(
                substr(str_shuffle('abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRTUVWXYZ2346789'), 0, 6)
            )->plainTextToken
        ];
    }
}
