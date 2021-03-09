<?php

namespace App\Http\Controllers;

use App\Handlers\ResponseHandler;
use App\Http\Requests\CreateNewUserRequest;
use App\Http\Requests\CreateWorkspaceRequest;
use App\Http\Requests\InviteWorkspaceUserRequest;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WorkspaceController extends Controller
{
    use ResponseHandler;

    public function index()
    {
        try {
            $workspaces = Workspace::all();
            return $this->successResponse('Workspaces returned', [
                'workspaces' => $workspaces
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getTrace());
        }
    }

    public function create(CreateWorkspaceRequest $request)
    {
        try {
            $workspace = Workspace::create(
                [
                    'name' => $request->name,
                    'slug' => Str::slug($request->name),
                    'description' => $request->description
                ]);
            $user = User::updateOrCreate(
                [
                    'email' => $request['owner']['email'],
                ],
                [
                    'first_name' => $request['owner']['first_name'],
                    'last_name' => $request['owner']['last_name'],
                    'password' => Hash::make($request['owner']['password']),
                ]);
            $workspace->users()->create([
                'user_id' => $user->id,
                'owner' => true,
                'accepted_invite' => now()
            ]);
            $accessToken = $user->createToken($workspace->name)->accessToken;
            return $this->successResponse('New workspace created successfully', [
                'access_token' => $accessToken,
                'workspace' => $workspace,
                'owner' => $workspace->owner->user
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getTrace());
        }
    }

    public function inviteUsers(InviteWorkspaceUserRequest $request)
    {
        try {
            $workspace = Workspace::find($request->workspaceId);
            if (!$workspace) return $this->errorResponse('Invalid workspace Id provided', $workspace);
            foreach ($request->emails as $email) {
                $user = User::updateOrCreate(
                    [
                        'email' => $email,
                    ],
                    [
                        'first_name' => explode('@', $email)[0],
                        'password' => Hash::make(Str::random(10))
                    ]);
                $workspace->users()->updateOrcreate([
                    'user_id' => $user->id,
                ]);
            }
            return $this->successResponse('Invites sent out successfully', $workspace->users);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getTrace());
        }
    }

    public function login(Request $request)
    {
        try {
            $workspace = Workspace::find($request->workspaceId);
            if (!$workspace) return $this->errorResponse('Invalid workspace Id provided', $workspace);
            $login = Auth::attempt([
                'email' => $request->email,
                'password' => $request->password
            ]);
            if (!$login) return $this->errorResponse('Invalid login credentials provided', $login);
            $user = Auth::user();
            $accessToken = $user->createToken($workspace->name)->accessToken;
            return $this->successResponse('New workspace created successfully', [
                'access_token' => $accessToken,
                'user' => $user,
                'workspace' => $workspace
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getTrace());
        }

    }

    public function acceptInvite(Request $request)
    {
        try {
            $workspace = Workspace::find($request->workspaceId);
            if (!$workspace) return $this->errorResponse('Invalid workspace Id provided', $workspace);
            $workspaceUser = WorkspaceUser::where('workspace_id', $workspace->id)->where('user_id', $request->userId)->first();
            if (!$workspaceUser) return $this->errorResponse('The user with the Id provided was not invited to ' . $workspace->name . ' workspace!!!', $workspace);
            $workspaceUser->update(['accepted_invite' => now()]);
            $user = User::find($request->userId);
            $accessToken = $user->createToken($workspace->name)->accessToken;
            return $this->successResponse('User added to ' . $workspace->name . ' workspace successfully', [
                'access_token' => $accessToken,
                'user' => $user,
                'workspace' => $workspace
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getTrace());
        }
    }

    public function createUsers(CreateNewUserRequest $request)
    {
        try {
            $workspace = Workspace::find($request->workspaceId);
            if (!$workspace) return $this->errorResponse('Invalid workspace Id provided', $workspace);
            $users = $request->users;
            foreach ($users as $user) {
                $newUser = User::create([
                    'email' => $user['email'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'password' => Hash::make($user['password']),
                ]);
                $workspace->users()->create([
                    'user_id' => $newUser->id,
                    'accepted_invite' => now()
                ]);
                return $this->successResponse('New User added to ' . $workspace->name . ' workspace successfully', $workspace->users);
            }
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getTrace());
        }
    }


}
