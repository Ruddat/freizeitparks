<?php

namespace App\Livewire\Frontend\ApiRegister;

use Livewire\Component;
use App\Models\ApiInterestSignup;

class ApiSignupForm extends Component
{
    public $email;
    public $park_name;
    public $agreed_to_privacy = false;
    public $success = false;

    protected $rules = [
        'email' => 'required|email|unique:api_interest_signups,email',
        'park_name' => 'nullable|string|max:255',
        'agreed_to_privacy' => 'accepted',
    ];

    public function submit()
    {
        $this->validate();

        ApiInterestSignup::create([
            'email' => $this->email,
            'park_name' => $this->park_name,
            'agreed_to_privacy' => $this->agreed_to_privacy,
        ]);

        $this->reset(['email', 'park_name', 'agreed_to_privacy']);
        $this->success = true;
    }

    public function render()
    {
        return view('livewire.frontend.api-register.api-signup-form');
    }
}
