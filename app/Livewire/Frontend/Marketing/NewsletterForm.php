<?php

namespace App\Livewire\Frontend\Marketing;

use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\NewsletterSignup;

class NewsletterForm extends Component
{
    public $email = '';
    public $showModal = false;

    public $name = '';
    public $city = '';
    public $interests = [];
    public $nameHint;

    public function submitEmail()
    {
        $funnyRequired = [
            'Ups! Du hast das wichtigste Feld vergessen 🤦‍♂️',
            'Ohne E-Mail kein Abenteuer 🚀',
            'Gib uns deine Mail – wir verraten auch niemandem dein Lieblingspark-Geheimnis 😉',
            'Da fehlt was… und es ist nicht die Achterbahn 🎢',
            'Hallo? Jemand zuhause? Wir brauchen ’ne Mailadresse! 📮',
        ];

        $funnyInvalid = [
            'Hmm… das sieht nicht wie eine echte Adresse aus 🧐',
            'Wir lieben Fantasie – aber E-Mails müssen echt sein ✉️',
            'Diese Adresse bringt dich leider nicht zum Freizeitpark-Paradies 😬',
        ];

        $this->validate([
            'email' => 'required|email',
        ], [
            'email.required' => $funnyRequired[array_rand($funnyRequired)],
            'email.email' => $funnyInvalid[array_rand($funnyInvalid)],
        ]);

        // Prüfen ob E-Mail schon existiert
        $existing = NewsletterSignup::where('email', $this->email)->first();

        if ($existing) {
            $funnyDuplicate = [
                'Diese E-Mail ist schon auf der Gästeliste 🎟️',
                'Schon registriert! 🎉 Bald gibt’s wieder News für dich.',
                'Du bist bereits dabei – willkommen zurück! 🦥',
                'Achtung, Déjà-vu: Diese Adresse kennen wir schon 😉',
            ];

            $this->addError('email', $funnyDuplicate[array_rand($funnyDuplicate)]);
            return;
        }

        $this->showModal = true;
    }


    public function register()
    {
        // Funny Hinweis, wenn Name leer ist (wird trotzdem geprüft via validate)
        if (empty(trim($this->name))) {
            $funnyNameHints = [
                'Mysterious Stranger? Auch okay… 😎',
                'Ohne Namen? Dann nennen wir dich einfach ParkFan3000 🎡',
                'Kein Name? 😱 Wir wollten dir doch persönlich schreiben!',
                '🕵️‍♂️ Undercover unterwegs? Du kannst uns ruhig deinen Vornamen verraten.',
            ];

            $this->nameHint = $funnyNameHints[array_rand($funnyNameHints)];
        } else {
            $this->nameHint = null;
        }

        $funnyNameRequired = [
            'name.required' => 'Wie sollen wir dich anschreiben? 🤔',
            'name.min' => 'Der Name ist etwas zu geheimnisvoll… gib uns ein bisschen mehr 🕵️‍♀️',
        ];

        $this->validate([
            'email' => 'required|email',
            'name' => 'required|string|min:2|max:50',
            'city' => 'nullable|string|max:50',
            'interests' => 'array',
        ], $funnyNameRequired);

        $existing = NewsletterSignup::where('email', $this->email)->first();

        if ($existing) {
            session()->flash('success', 'Du bist bereits eingetragen. ✉️');
            // Modal bleibt offen → Daten erhalten
            return;
        }

        NewsletterSignup::create([
            'email' => $this->email,
            'name' => $this->name,
            'city' => $this->city,
            'interests' => $this->interests,
            'confirmation_token' => Str::uuid(),
        ]);

        $this->reset(['email', 'name', 'city', 'interests', 'showModal']);

        session()->flash('success', 'Danke für deine Anmeldung! 🎉 Du bekommst bald Post von uns.');
    }


    public function render()
    {
        return view('livewire.frontend.marketing.newsletter-form');
    }
}
