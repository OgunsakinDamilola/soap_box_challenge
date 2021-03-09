<?php

namespace App\Observers;

use App\Models\WorkspaceUser;
use App\Notifications\WorkspaceInviteNotification;

class WorkspaceUserObserver
{
    /**
     * Handle the WorkspaceUser "created" event.
     *
     * @param WorkspaceUser $workspaceUser
     * @return void
     */
    public function created(WorkspaceUser $workspaceUser)
    {
        if(is_null($workspaceUser->accepted_invite)){
            $user = $workspaceUser->user;
            $user->notify(new WorkspaceInviteNotification($workspaceUser));
        }
    }

    /**
     * Handle the WorkspaceUser "updated" event.
     *
     * @param WorkspaceUser $workspaceUser
     * @return void
     */
    public function updated(WorkspaceUser $workspaceUser)
    {
        //
    }

    /**
     * Handle the WorkspaceUser "deleted" event.
     *
     * @param WorkspaceUser $workspaceUser
     * @return void
     */
    public function deleted(WorkspaceUser $workspaceUser)
    {
        //
    }

    /**
     * Handle the WorkspaceUser "restored" event.
     *
     * @param WorkspaceUser $workspaceUser
     * @return void
     */
    public function restored(WorkspaceUser $workspaceUser)
    {
        //
    }

    /**
     * Handle the WorkspaceUser "force deleted" event.
     *
     * @param WorkspaceUser $workspaceUser
     * @return void
     */
    public function forceDeleted(WorkspaceUser $workspaceUser)
    {
        //
    }
}
