<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\State;
use App\Models\User;
use App\Models\UserRegisterToken;
use App\Models\UserDetails;
use App\Models\Municipality;

class OperatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $states = State::all();

        foreach($states as $state){

            if($state->id !== 25)
                $email = Str::slug($state->name).'patriagrande@gmail.com';
            else 
                $email = 'dfpatriagrande@gmail.com';

            $password = 'patriagrande2024';
            $user = User::create([
                'name' => $state->name,
                'last_name' => 'OPERADOR',
                'username' => Str::slug($state->name . ' ' . rand(1,100)),
                'email' => str_replace('-', '', $email),
                'password' => Hash::make($password),
                'created_at' => now()->toDateString(),
                'updated_at' => now()->toDateString()
            ]);
        
            $user->assignRole('Operador');

            $municipality_id = Municipality::where('state_id', $state->id)->InRandomOrder()->first()->id;
            
            UserDetails::create([
                'user_id' => $user->id,
                'parish_id' => 1126,
                'created_at' => now()->toDateString(),
                'updated_at' => now()->toDateString()
            ]);

            $registerConfirm = UserRegisterToken::updateOrCreate(
                ['user_id' => $user->id],
                ['token' => Str::random(60)]
            );

            $info = [
                'title' => 'Cuenta creada satisfactoriamente!!!',
                'user' => $user->name . ' ' . $user->last_name,
                'username' => $user->email,
                'email' => 'emails.auth.user_created',
                'password' => $password,
                'text' => 'Tu cuenta no está verificada. Confirma tu cuenta con los pasos a seguir para verificarla.',
                'buttonLink' =>  env('APP_DOMAIN').'/register-confirm?token=' . $registerConfirm['token'],
                'buttonText' => 'Confirmar',
                'subject' => 'Bienvenido a '.env('APP_TITLE'),
            ];

            $this->sendMail($user->id, $info);
        }

    }

    private function sendMail($id, $info ){

        $user = User::find($id);
        $response = [];

        $data = [
            'title' => $info['title']?? null,
            'user' => $user->name . ' ' . $user->last_name,
            'text' => $info['text'] ?? null,
            'username' => $info['username'] ?? null,
            'password' => $info['password'] ?? null,
            'buttonLink' =>  $info['buttonLink'] ?? null,
            'buttonText' =>  $info['buttonText'] ?? null
        ];

        $email = $user->email;
        $subject = $info['subject'];
        
        try {
            \Mail::send($info['email'], $data, function ($message) use ($email, $subject) {
                    $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                    $message->to($email)->subject($subject);
            });

            $response['success'] = true;
            $response['message'] = "Tu solicitud se ha procesado satisfactoriamente.";
        } catch (\Exception $e){
            $response['success'] = false;
            $response['message'] = "Ocurrió un error, no se pudo enviar el correo electrónico. ".$e;
        }        

        return $response;

    } 
}