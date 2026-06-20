<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    /** @var class-string<Company> */
    protected $model = Company::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'user_id' => User::factory()->company(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 99999),
            'description' => fake()->paragraph(),
            'website' => fake()->url(),
            'industry' => fake()->randomElement(['Technology', 'Finance', 'Healthcare', 'Retail']),
            'company_size' => fake()->randomElement(['1-10', '11-50', '51-200', '201-500']),
            'headquarters_location' => fake()->city().', '.fake()->countryCode(),
            'is_verified' => false,
        ];
    }
}
