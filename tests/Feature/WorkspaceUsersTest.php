<?php

namespace Tests\Feature;


use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class WorkspaceUsersTest extends TestCase
{
    private function create_work_space_request_data()
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

    public function sample_workspace()
    {
        $requestData = $this->create_work_space_request_data();
        $workspace = $this->json('post', '/api/workspaces/create', $requestData);
        return $workspace;
    }

    public function test_invite_work_space_users()
    {
        Notification::fake();
        $workspace = $this->sample_workspace();
        $fakeUser1 = \App\Models\User::factory()->make();
        $fakeUser2 = \App\Models\User::factory()->make();
        $response = $this->json('post', '/api/workspaces/users/invite?workspaceId=' . $workspace['data']['workspace']['id'],
            [
                'emails' => [$fakeUser1->email, $fakeUser2->email]
            ],
            [
                'Authorization' => 'Bearer ' . $workspace['data']['access_token']
            ]);
        $response->assertStatus(200);
        $this->assertTrue($response['status']);
        $this->assertArrayHasKey('data', $response);
        Notification::assertSentTo(\App\Models\User::where('email', $fakeUser1->email)->first(), \App\Notifications\WorkspaceInviteNotification::class);
        Notification::assertSentTo(\App\Models\User::where('email', $fakeUser2->email)->first(), \App\Notifications\WorkspaceInviteNotification::class);
    }

    public function test_create_work_space_user(){
        $workspace = $this->sample_workspace();
        $fakeUser = \App\Models\User::factory()->make();
        $response = $this->json('post', '/api/workspaces/users/create?workspaceId=' . $workspace['data']['workspace']['id'],
            [
                'users' => [
                    [
                        'email' => $fakeUser->email,
                        'first_name' => $fakeUser->first_name,
                        'last_name' => $fakeUser->last_name,
                        'password' => 'password',
                        'password_confirmation' => 'password'
                    ]
                ]
            ],
            [
                'Authorization' => 'Bearer ' . $workspace['data']['access_token']
            ]);
        $response->assertStatus(200);
        $this->assertTrue($response['status']);
        $this->assertArrayHasKey('data', $response);
        $this->assertEquals($workspace['data']['workspace']['id'], $response['data'][0]['workspace_id']);
    }


}
