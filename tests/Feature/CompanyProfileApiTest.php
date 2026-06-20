<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyProfileApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_public_company_profile(): void
    {
        $company = Company::factory()->create();

        $this->getJson('/api/v1/companies/'.$company->slug)
            ->assertOk()
            ->assertJsonPath('data.name', $company->name);
    }

    public function test_company_user_can_create_and_update_profile(): void
    {
        $employer = User::factory()->company()->create();
        $token = auth('api')->login($employer);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/companies', [
                'name' => 'Acme GmbH',
                'description' => 'Demo employer profile.',
                'website' => 'https://acme.example',
                'industry' => 'Software',
                'company_size' => '51-200',
                'headquarters_location' => 'Berlin, DE',
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Acme GmbH');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/companies/me/profile')
            ->assertOk()
            ->assertJsonPath('data.name', 'Acme GmbH');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/companies/me/profile', [
                'name' => 'Acme AG',
                'description' => 'Updated description.',
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Acme AG');
    }

    public function test_job_seeker_cannot_create_company_profile(): void
    {
        $seeker = User::factory()->jobSeeker()->create();
        $token = auth('api')->login($seeker);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/companies', [
                'name' => 'Should Fail',
                'description' => 'No access.',
            ])
            ->assertForbidden();
    }
}
