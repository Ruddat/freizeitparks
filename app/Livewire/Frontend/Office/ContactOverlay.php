<?php

namespace App\Livewire\Frontend\Office;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ContactMessage;

class ContactOverlay extends Component
{
    use WithFileUploads;

    public $name;
    public $email;
    public $message;
    public $attachment;
    public $success = false;
    public $isOpen = false;

    protected $listeners = ['openContactOverlay' => 'open'];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // max 10MB
        ];
    }

    public function submit()
    {
        $this->validate();

        $path = null;
        if ($this->attachment) {
            $path = $this->attachment->store('contact_uploads');
        }

        ContactMessage::create([
            'name' => $this->name,
            'email' => $this->email,
            'message' => $this->message,
            'file_path' => $path,
        ]);

        $this->reset(['name', 'email', 'message', 'attachment']);
        $this->success = true;
    }

    public function open()
    {
        \Log::info('Overlay open function called'); // Loggt in storage/logs/laravel.log
        $this->resetValidation();
        $this->reset(['name', 'email', 'message', 'attachment', 'success']);
        $this->isOpen = true;
    }

    public function close()
    {
        $this->isOpen = false;
    }


    public function render()
    {
        return view('livewire.frontend.office.contact-overlay');
    }
}
