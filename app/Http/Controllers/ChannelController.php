<?php

namespace App\Http\Controllers;

use App\Handlers\ResponseHandler;
use App\Http\Requests\CreateChannelUsersRequest;
use App\Http\Requests\CreateNewChannelRequest;
use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChannelController extends Controller
{
    use ResponseHandler;

    public function index(Request $request)
    {
        try {
            $workspace = Workspace::find($request->workspaceId);
            if (!$workspace) return $this->errorResponse('Invalid workspace Id provided', $workspace);
            $workspaceChannels = Channel::where('workspace_id', $workspace->id)->orderBy('id', 'desc')->get();
            return $this->successResponse($workspace->name . ' workspace channels retrieved', [
                'channels' => $workspaceChannels
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e);
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
            foreach ($request->users as $email) {
                $user = User::where('email', $email)->first();
                if (is_null($user) || empty($user)) return $this->errorResponse('A user with email ' . $email . ' does not exist on this system', []);
                $workspaceUser = WorkspaceUser::where('user_id', $user->id)->where('workspace_id', $workspace->id)->first();
                if (is_null($workspaceUser) || empty($workspaceUser)) return $this->errorResponse('A user with email ' . $email . ' does not exist in the ' . $workspace->name . ' workspace', []);
                $channel->users()->updateOrCreate(['user_id' => $user->id,], ['channel_id' => $channel->id]);
            }
            return $this->successResponse('Channel created successfully', [
                'workspace' => $workspace,
                'channel' => $channel,
                'channel_users' => $channel->users
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e);
        }
    }

    public function createUsers(CreateChannelUsersRequest $request)
    {
        try {
            $workspace = Workspace::find($request->workspaceId);
            if (!$workspace) return $this->errorResponse('Invalid workspace Id provided', $workspace);
            $workspaceChannel = Channel::where('workspace_id', $workspace->id)->where('id', $request->channelId)->first();
            if (!$workspaceChannel) return $this->errorResponse('Invalid channel Id provided', $workspaceChannel);
            foreach ($request->users as $email) {
                $user = User::where('email', $email)->first();
                if (is_null($user) || empty($user)) return $this->errorResponse('A user with email ' . $email . ' does not exist on this system', []);
                $workspaceUser = WorkspaceUser::where('user_id', $user->id)->where('workspace_id', $workspace->id)->first();
                if (is_null($workspaceUser) || empty($workspaceUser)) return $this->errorResponse('A user with email ' . $email . ' does not exist in the ' . $workspace->name . ' workspace', []);
                $workspaceChannel->users()->updateOrCreate(['user_id' => $user->id,], ['channel_id' => $workspaceChannel->id]);
            }
            return $this->successResponse('Channel users created successfully', [
                'workspace' => $workspace,
                'channel' => $workspaceChannel,
                'channel_users' => $workspaceChannel->users
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e);
        }
    }

    public function getUserChannels(Request $request)
    {
        try {
            $workspace = Workspace::find($request->workspaceId);
            if (!$workspace) return $this->errorResponse('Invalid workspace Id provided', $workspace);
            $workspaceChannels = $workspace->channels;
            $userChannels = new Collection();
            foreach ($workspaceChannels as $channel) {
                $channelUsers = $channel->users->where('id', Auth::id())->first();
                if (!empty($channelUsers) || is_null($channelUsers)) $userChannels->add($channel);
            }
            if ($userChannels->count() > 0) return $this->successResponse('User channels retrieved', ['channels' => $userChannels]);
            return $this->errorResponse('No channel found for user in ' . $workspace->name . ' workspace', []);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e);
        }
    }
}
