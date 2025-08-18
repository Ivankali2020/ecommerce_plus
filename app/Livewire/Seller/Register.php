<?php

namespace App\Livewire\Seller;

use App\Models\Seller;
use App\Models\Tenant;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Stancl\Tenancy\Database\Models\Domain;

#[Layout('layouts.guest')]
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $domain = '';

    public string $cfTurnstileResponse = '';

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:sellers'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'domain' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                function ($attribute, $value, $fail) {
                    // Check if domain already exists
                    $existsDomain = Domain::where('domain', $value.'.'.config('tenancy.central_domains')[0])->exists();
                    if ($existsDomain) {
                        $fail('This domain is already taken.');
                    }
                },
            ],
            'cfTurnstileResponse' => ['required', 'turnstile'],
        ];
    }

    public function register()
    {
        $validated = $this->validate();

        // Hash password
        $validated['password'] = Hash::make($validated['password']);

        // Remove domain from user data and password confirmation
        $domain = $validated['domain'];
        unset($validated['domain']);
        unset($validated['password_confirmation']);

        // Remove cfTurnstileResponse from the validated data
        unset($validated['cfTurnstileResponse']);

        // Create tenant
        $tenant = Tenant::create([
            'data' => [
                'domain' => $domain,
            ],
        ]);

        // Set tenant ID for the seller
        $validated['tenant_id'] = $tenant->id;

        // Create seller
        $seller = Seller::create($validated);

        event(new Registered($seller));

        // Create domain record
        Domain::create([
            'domain' => $domain.'.'.config('tenancy.central_domains')[0],
            'tenant_id' => $tenant->id,
        ]);

        // Attach tenant to seller
        DB::table('tenant_seller')->insert([
            'tenant_id' => $tenant->id,
            'seller_id' => $seller->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // session(['registration_domain' => $domain]);

        // Redirect to tenant domain
        return redirect()->away('http://'.$domain.'.'.config('tenancy.central_domains')[0].'/seller/login');
    }

    public function render()
    {
        return view('livewire.seller.register');
    }
}
