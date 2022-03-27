<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'Account_number' => $this->wallet ? $this->wallet->account_number : "-",
            'Amount' => $this->wallet ? $this->wallet->amount." MMK" : "-",
            "img" => asset('img/avator.png'),
        ];
    }
}
