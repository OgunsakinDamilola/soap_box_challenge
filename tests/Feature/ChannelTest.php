<?php

namespace Tests\Feature;

use App\Models\Channel;
use Tests\TestCase;

class ChannelTest extends TestCase
{

    public function create_work_space_request_data()
    {
        $user = \App\Models\User::factory()->make();
        $workspace = \App\Models\Workspace::factory()->make();
        return [
            'name' => $workspace->name,
            'description' => $workspace->description,
            'owner' => [
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'password' => 'password',
                'password_confirmation' => 'password'
            ]
        ];
    }

    public function create_channel_request_data($workspace)
    {
        $fakeChannel = Channel::factory()->make();
        $fakeUser1 = \App\Models\User::factory()->make();
        $fakeUser2 = \App\Models\User::factory()->make();
        $requestData = ['emails' => [$fakeUser1->email, $fakeUser2->email]];
        $url = '/api/workspaces/users/invite?workspaceId=' . $workspace['data']['workspace']['id'];
        $header = ['Authorization' => 'Bearer ' . $workspace['data']['access_token']];
        $response = $this->json('post', $url, $requestData, $header);
        $response->assertStatus(200);
        return [
            'name' => $fakeChannel->name,
            'description' => $fakeChannel->description,
            'users' => [
                $fakeUser1->email, $fakeUser2->email
            ]
        ];
    }

    public function sample_workspace()
    {
        $requestData = $this->create_work_space_request_data();
        $workspace = $this->json('post', '/api/workspaces/create', $requestData);
        return $workspace;
    }

    public function test_create_channel()
    {
        $workspace = $this->sample_workspace();
        $requestData = $this->create_channel_request_data($workspace);
        $header = ['Authorization' => 'Bearer ' . $workspace['data']['access_token']];
        $url = '/api/workspaces/channels/create?workspaceId=' . $workspace['data']['workspace']['id'];
        $response = $this->json('post', $url, $requestData, $header);
        $response->assertStatus(200);
        $this->assertTrue($response['status']);
        $this->assertArrayHasKey('data', $response);
    }

    public function test_create_channel_users()
    {
        $workspace = $this->sample_workspace();
        $requestData = $this->create_channel_request_data($workspace);
        $header = ['Authorization' => 'Bearer ' . $workspace['data']['access_token']];
        $url = '/api/workspaces/channels/create?workspaceId=' . $workspace['data']['workspace']['id'];
        $channel = $this->json('post', $url, $requestData, $header);
        $channel->assertStatus(200);
        $createChannelUsersUrl = '/api/workspaces/channels/users/create?workspaceId=' . $workspace['data']['workspace']['id'] . '&channelId=' . $channel['data']['channel']['id'];
        $createChannelUsersRequestData = ['users' => [$workspace['data']['owner']['email']]];
        $createChannelUsers = $this->json('post', $createChannelUsersUrl, $createChannelUsersRequestData, $header);
        $createChannelUsers->assertStatus(200);
        $this->assertTrue($createChannelUsers ['status']);
        $this->assertArrayHasKey('data', $createChannelUsers);
    }

    public function test_get_channel_users()
    {
        $workspace = $this->sample_workspace();
        $requestData = $this->create_channel_request_data($workspace);
        $header = ['Authorization' => 'Bearer ' . $workspace['data']['access_token']];
        $url = '/api/workspaces/channels/create?workspaceId=' . $workspace['data']['workspace']['id'];
        $channel = $this->json('post', $url, $requestData, $header);
        $channel->assertStatus(200);
        $getChannelUsersUrl = '/api/workspaces/channels/users/get-user-channels?workspaceId=' . $workspace['data']['workspace']['id'];
        $getChannelUsers = $this->json('get', $getChannelUsersUrl, $header);
        $getChannelUsers->assertStatus(200);
        $this->assertTrue($getChannelUsers ['status']);
        $this->assertArrayHasKey('data', $getChannelUsers);
    }
}
