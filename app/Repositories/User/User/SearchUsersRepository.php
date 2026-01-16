<?php

namespace App\Repositories\User\User;

use App\Repositories\BaseRepository;
use App\Models\User;

class SearchUsersRepository extends BaseRepository
{
    public function execute($request){
        if (!$request->filled('name')) {
            return $this->success('Users fetched successfully', [], 200);
        }

        $keyword = $request->name;

        $meiliResults = User::search($keyword)->get();

        $soundexResults = User::whereRaw("SOUNDEX(username) = SOUNDEX(?)", [$keyword])
            ->orWhereRaw("SOUNDEX(first_name) = SOUNDEX(?)", [$keyword])
            ->orWhereRaw("SOUNDEX(last_name) = SOUNDEX(?)", [$keyword])
            ->get();


        $combined = $meiliResults
            ->merge($soundexResults)
            ->unique('id')
            ->values();

        $users = $combined->map(function ($user) {
            return [
                'username'  => $user->username,
                'firstName' => $user->first_name,
                'lastName'  => $user->last_name,
            ];
        });

        return $this->success('Users fetched successfully', $users, 200);
    }
}
