<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkspaceUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id', 'user_id', 'owner', 'accepted_invite'
    ];

    protected $appends = [
        'user'
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class, 'workspace_id', 'id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function getUserAttribute(){
        return User::find($this->user_id);
    }

}
