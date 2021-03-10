<?php

namespace Tests\Feature;


use Tests\TestCase;

class WorkspaceTest extends TestCase
{
    public $testWorkSpace, $testWorkSpaceOwner;

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

    public function test_create_workspace_success()
    {
        $requestData = $this->create_work_space_request_data();
        $response = $this->json('post', '/api/workspaces/create', $requestData);
        $response->assertStatus(200);
        $this->assertTrue($response['status']);
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('access_token', $response['data']);
        $this->assertArrayHasKey('workspace', $response['data']);
        $this->testWorkSpace = $response['data']['workspace'];
        $this->assertArrayHasKey('owner', $response['data']);
        $this->testWorkSpaceOwner = $response['data']['owner'];
        $this->assertEquals($requestData['name'], $response['data']['workspace']['name']);
    }

    public function test_create_workspace_failure()
    {
        $requestData = $this->create_work_space_request_data();
        $workspace = \App\Models\Workspace::factory()->create();
        $requestData['name'] = $workspace->name;
        $errorResponse = $this->json('post','/api/workspaces/create', $requestData);
        $errorResponse->assertStatus(422);
        $this->assertFalse($errorResponse['status']);
    }

    public function test_get_all_workspace(){
        $response = $this->json('get','/api/workspaces');
        $response->assertStatus(200);
        $this->assertTrue($response['status']);
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('workspaces', $response['data']);
    }

    public function test_login_to_workspace(){
        $requestData = $this->create_work_space_request_data();
        $workspace = $this->json('post', '/api/workspaces/create', $requestData);
        $workspace->assertStatus(200);
        $response = $this->json('post','/api/workspaces/login?workspaceId='.$workspace['data']['workspace']['id'], [
           'email' => $workspace['data']['owner']['email'],
           'password' => 'password'
        ]);
        $response->assertStatus(200);
        $this->assertTrue($response['status']);
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('access_token', $response['data']);
    }


}
