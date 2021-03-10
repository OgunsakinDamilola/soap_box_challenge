<?php

namespace Tests\Feature;

use App\Models\Channel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChannelMessagesTest extends TestCase
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

    public function test_send_message()
    {
        $workspace = $this->sample_workspace();
        $requestData = $this->create_channel_request_data($workspace);
        $header = ['Authorization' => 'Bearer ' . $workspace['data']['access_token']];
        $url = '/api/workspaces/channels/create?workspaceId=' . $workspace['data']['workspace']['id'];
        $channel = $this->json('post', $url, $requestData, $header);
        $channel->assertStatus(200);
        $sendMessageUrl = '/api/workspaces/channels/messages/create?workspaceId=' . $workspace['data']['workspace']['id'] . '&channelId=' . $channel['data']['channel']['id'];
        $sendMessageRequestData = ['message' => 'Lots of lorem ipsum values'];
        $response = $this->json('post', $sendMessageUrl, $sendMessageRequestData, $header);
        $response->assertStatus(200);
        $this->assertTrue($response['status']);
        $this->assertArrayHasKey('data', $response);
    }

    public function test_get_channel_messages_success(){
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
        $getMessageUrl = '/api/workspaces/channels/messages?workspaceId=' . $workspace['data']['workspace']['id'] . '&channelId=' . $channel['data']['channel']['id'];
        $response = $this->json('get', $getMessageUrl, [], $header);
        $response->assertStatus(200);
        $this->assertTrue($response['status']);
        $this->assertArrayHasKey('data', $response);
    }

    public function test_get_channel_message_invalid_channel_user(){
        $workspace = $this->sample_workspace();
        $requestData = $this->create_channel_request_data($workspace);
        $header = ['Authorization' => 'Bearer ' . $workspace['data']['access_token']];
        $url = '/api/workspaces/channels/create?workspaceId=' . $workspace['data']['workspace']['id'];
        $channel = $this->json('post', $url, $requestData, $header);
        $channel->assertStatus(200);
        $getMessageUrl = '/api/workspaces/channels/messages?workspaceId=' . $workspace['data']['workspace']['id'] . '&channelId=' . $channel['data']['channel']['id'];
        $response = $this->json('get', $getMessageUrl, [], $header);
        $response->assertStatus(422);
        $this->assertFalse($response['status']);
        $this->assertArrayHasKey('errors', $response);
    }
}
