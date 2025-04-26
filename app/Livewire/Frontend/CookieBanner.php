<?php

namespace App\Livewire\Frontend;

use Livewire\Component;

class CookieBanner extends Component
{
    public $show = false;
    public $settings = false;
    public $cookies = [
        'essential' => true,
        'analytics' => false,
        'marketing' => false,
    ];

    public function mount()
    {
        // Prüfen, ob schon per LocalStorage zugestimmt wurde
        $this->dispatch('check-cookie-consent');
    }

    public function acceptAll()
    {
        $this->cookies['analytics'] = true;
        $this->cookies['marketing'] = true;
        $this->saveSettings();
    }

    public function declineAll()
    {
        $this->cookies['analytics'] = false;
        $this->cookies['marketing'] = false;
        $this->saveSettings();
    }

    public function openSettings()
    {
        $this->settings = true;
    }

    public function saveSettings()
    {
        $this->dispatch('cookie-consent-saved', cookies: $this->cookies);
        $this->show = false;
        $this->settings = false;
    }

    public function render()
    {
        return view('livewire.frontend.cookie-banner');
    }
}
