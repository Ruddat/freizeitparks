<?php

namespace App\Livewire\Backend\Marketing;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\NewsletterSignup;

class NewsletterSignupManager extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $signups = NewsletterSignup::query()
            ->when($this->search, fn($q) => $q->where('email', 'like', "%{$this->search}%"))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.backend.marketing.newsletter-signup-manager', [
            'signups' => $signups,
        ])->layout('backend.layouts.backend');
    }
}
