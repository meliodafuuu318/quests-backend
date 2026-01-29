<?php

namespace App\Repositories\Quest;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    CompletionVerification
};

class UpdateVerification extends BaseRepository
{
    public function execute($request){
        $verification = CompletionVerification::find($request->verificationId)
            ->first();

        if ($verification->user_id === auth()->id()) {
            $verification->update([
                'type' => $request->type ?? $verification->type,
            ]);
        }

        return $this->success('Verification updated', $verification, 200);
    }
}
