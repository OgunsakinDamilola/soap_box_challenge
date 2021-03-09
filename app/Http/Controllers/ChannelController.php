<?php

namespace App\Http\Controllers;

use App\Handlers\ResponseHandler;
use App\Http\Requests\CreateNewChannelRequest;
use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChannelController extends Controller
{
    use ResponseHandler;

    public function index(Request $request)
    {
        try {
            $workspace = Workspace::find($request->workspaceId);
            if (!$workspace) return $this->errorResponse('Invalid workspace Id provided', $workspace);
            $workspaceChannels = Channel::wheere('workspace_id', $workspace->id)->orderBy('id', 'desc')->get();
            return $this->successResponse($workspace->name . ' workspace channels retrieved', [
                'channels' => $workspaceChannels
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e);
        }
    }

    private function handleChannelUsers($workspace, $channel, $emails)
    {
        foreach ($emails as $email) {
            $user = User::where('email', $email)->first();
            if (!$user) return $this->errorResponse('A user with email ' . $email . ' does not exist on this system', []);

            $workspaceUser = WorkspaceUser::where('user_id', $user->id)->first();
            if (!$workspaceUser) return $this->errorResponse('A user with email ' . $email . ' does not exist in the ' . $workspace->name . ' workspace', []);

            $channel->users()->updateOrCreate(
                [
                    'user_id' => $user->id,
                ],
                [
                    'channel_id' => $channel->id
                ]);
            return true;
        }
    }

    public function create(CreateNewChannelRequest $request)
    {
        try {
            $workspace = Workspace::find($request->workspaceId);
            if (!$workspace) return $this->errorResponse('Invalid workspace Id provided', $workspace);
            if ($workspace->channels()->where('name', $request->name)->count() > 0):
                return $this->errorResponse('A channel with the same name exists for this workspace', []);
            endif;
            $channel = $workspace->channels()->create([
                'name' => $request->name,
                'slug' => Str::slug($request->slug),
                'description' => $request->description
            ]);
            $handleUsers = $this->handleChannelUsers($workspace, $channel, $request->users);
            if (!$handleUsers) return $handleUsers;
            return $this->successResponse('Channel created successfully', [
                'workspace' => $workspace,
                'channel' => $channel,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e);
        }
    }
}
