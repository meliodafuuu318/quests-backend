<?php

namespace App\Repositories\SocialActivity\Media;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Media;

class UploadMediaRepository extends BaseRepository
{
    public function execute($request){
        $user = auth()->user();

        if ($request->has('file')) {
            $file = $request->file;
            $filePath = $file->storeAs(
                'media/' . Carbon::now()->format('Y/m/d'),
                'upload-' . $user->username . '-' . uniqid() . '.' . $file->extension(),
                'public'
            );

            $media = Media::create([
                'filepath' => '/storage/' . $filePath,
                'user_id' => $user->id,
                'social_activity_id' => $request->socialActivityId
            ]);
        }

        return $this->success('Media uploaded', $media, 200);
    }
}
