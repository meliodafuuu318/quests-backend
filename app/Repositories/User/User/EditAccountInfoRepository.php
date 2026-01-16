<?php

namespace App\Repositories\User\User;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class EditAccountInfoRepository extends BaseRepository
{
    public function execute($request){
        DB::beginTransaction();
        try {
            $user = User::find(auth()->user()->id);

            if (!$user) {
                return $this->error('User not found', 404);
            }

            $user->update([
                'username' => $request->username ?? $user->username,
                'first_name' => $request->firstName ?? $user->first_name,
                'last_name' => $request->lastName ?? $user->last_name,
                'birthdate' => $request->birthdate ?? $user->birthdate,
                'gender' => $request->gender ?? $user->gender,
                'city' => $request->city ?? $user->city,
                'province' => $request->province ?? $user->province,
                'country' => $request->country ?? $user->country,
                'contact_number' => $request->contactNumber ?? $user->contact_number,
                'bio' => $request->bio ?? $user->bio,
            ]);

            DB::commit();
            return $this->success('User info updated successfully', $user, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error('User info update failed', 500, $e);
        }
    }
}
