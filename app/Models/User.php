<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\{
    UserAchievement,
    UserItem,
    Friend,
    QuestParticipant,
    ParticipantTask,
    SocialActivity
};
use Climactic\Credits\Traits\HasCredits;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, hasCredits, hasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'exp',
        'level',
        'first_name',
        'last_name',
        // 'avatar_id',
        // 'avatar_frame_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'id',
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function userAchievement() {
        return $this->hasMany(UserAchievement::class);
    }

    public function userItem() {
        return $this->hasMany(UserItem::class);
    }

    public function questParticipant() {
        return $this->hasMany(QuestParticipant::class);
    }

    public function participantTask() {
        return $this->hasMany(ParticipantTask::class);
    }

    public function socialActivity() {
        return $this->hasMany(SocialActivity::class);
    }

    public function friend() {
        return $this->hasMany(Friend::class);
    }
}
