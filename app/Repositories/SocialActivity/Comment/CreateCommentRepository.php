<?php

namespace App\Repositories\SocialActivity\Comment;

use App\Repositories\BaseRepository;
use App\Models\{
    User,
    SocialActivity,
    Media
};
use App\Events\CommentEvent;
use Illuminate\Support\Facades\DB;

class CreateCommentRepository extends BaseRepository
{
    public function execute($request)
    {
        $user = User::find(auth()->user()->id);

        DB::beginTransaction();

        if ($request->type === 'comment') {
            try {
                $post = SocialActivity::where('id', $request->commentTarget)
                    ->where('type', 'post')
                    ->first();

                if (!$post) {
                    return $this->error('Post not found', 404);
                }

                // content is nullable — a media-only comment is allowed.
                $comment = SocialActivity::create([
                    'user_id'        => $user->id,
                    'visibility'     => 'public',
                    'type'           => 'comment',
                    'comment_target' => $request->commentTarget,
                    'content'        => $request->content ?? null,
                ]);

                // ── Media uploads ─────────────────────────────────────────────
                // Normalise to array so foreach works whether Flutter sent
                // 'media[]' (array) or the field arrives as a single file.
                if ($request->hasFile('media')) {
                    $files = $request->file('media');
                    if (!is_array($files)) {
                        $files = [$files];
                    }

                    foreach ($files as $file) {
                        $filePath = $file->storeAs(
                            'media/' . now()->format('Y/m/d'),
                            'upload-' . $user->username . '-' . uniqid() . '.' . $file->extension(),
                            'public'
                        );
                        Media::create([
                            'filepath'           => '/storage/' . $filePath,
                            'user_id'            => $user->id,
                            // Link to THIS comment's social_activity id so
                            // ShowPostCommentsRepository can fetch them back.
                            'social_activity_id' => $comment->id,
                        ]);
                    }
                }

                DB::commit();

                // ── Broadcast ─────────────────────────────────────────────────
                $commentCount = SocialActivity::where('type', 'comment')
                    ->where('comment_target', $post->id)
                    ->count();

                event(new CommentEvent([
                    'post_id'       => $post->id,
                    'post_owner_id' => $post->user_id,
                    'commenter_id'  => $user->id,
                    'username'      => $user->username,
                    'content'       => $comment->content,
                    'comment_count' => $commentCount,
                    'created_at'    => $comment->created_at->toIso8601String(),
                    'has_media'     => $request->hasFile('media'),
                ]));

                return $this->success('Comment created successfully', $comment, 200);

            } catch (\Exception $e) {
                DB::rollback();
                return $this->error('Something went wrong', 500, $e->getMessage());
            }
        }
    }
}