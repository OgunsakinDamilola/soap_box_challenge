<?php

namespace App\Http\Controllers;

use App\Handlers\ResponseHandler;
use App\Http\Requests\CreateMessageRequest;
use App\Models\Channel;
use App\Models\Message;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    use ResponseHandler;

    public function index(Request $request)
    {
        try {
            $workspace = Workspace::find($request->workspaceId);
            if (!$workspace) return $this->errorResponse('Invalid workspace Id provided', $workspace);
            $workspaceChannel = Channel::where('workspace_id', $workspace->id)->where('id', $request->channelId)->first();
            if (!$workspaceChannel) return $this->errorResponse('Invalid channel Id provided', $workspaceChannel);
            $isChannelUser = $workspaceChannel->users->where('user_id', Auth::id())->first();
            if (!$isChannelUser) return $this->errorResponse('You are not a member of this channel', []);
            $channelMessages = $workspaceChannel->messages;
            return $this->successResponse('Channel messages retrieved', [
                'messages' => $channelMessages
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e);
        }
    }

    public function create(CreateMessageRequest $request)
    {
        try {
            $workspace = Workspace::find($request->workspaceId);
            if (!$workspace) return $this->errorResponse('Invalid workspace Id provided', $workspace);
            $workspaceChannel = Channel::where('workspace_id', $workspace->id)->where('id', $request->channelId)->first();
            if (!$workspaceChannel) return $this->errorResponse('Invalid channel Id provided', $workspaceChannel);
            $message = Message::create([
                'user_id' => Auth::id(),
                'channel_id' => $request->channelId,
                'workspace_id' => $request->workspaceId,
                'message' => $request->message
            ]);
            return $this->successResponse('Channel message created successfully', [
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e);
        }
    }
}
