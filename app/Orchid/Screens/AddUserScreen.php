<?php

namespace App\Orchid\Screens;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Relation;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;

class AddUserScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): array
    {
        return [];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'AddUserScreen';
    }
    public function description(): ?string
    {
        return "Добавление пользователя.";
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [Button::make('Добавить пользователя')
        ->icon('paper-plane')
        ->method('addUser')];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('user.name')
                    ->type('string')
                    ->max(120)
                    ->required()
                    ->title(__('Имя'))
                    ->placeholder(__('Имя')),

                Input::make('user.email')
                    ->type('email')
                    ->required()
                    ->unique()
                    ->title(__('Email'))
                    ->placeholder(__('Email')),

                Input::make('user.password')
                    ->type('password')
                    ->min(8)
                    ->required()
                    ->title(__('Password'))
                    ->placeholder(__('Password')),
            ])
        ];
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addUser(Request $request)
    {
        $request->validate([
            'user.name' => 'required|min:6|max:120|string',
            'user.email'   => 'required|unique|email',
            'user.password' => 'required|min:8|password'
        ]);

        User::raw($request->get('content'), function ( $message) use ($request) {
            $message->from('sample@email.com');
            $message->subject($request->get('user.name'));

            foreach ($request->get('users') as $email) {
                $message->to($email);
            }
        });


        Alert::info('Пользователь добавлен.');
    }
}
