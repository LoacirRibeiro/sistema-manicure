<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function mostrarLogin() {
        return view('auth.login');
    }

    public function efetuarLogin(Request $request) {
        $credenciais = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credenciais)) {
            $request->session()->regenerate();
            return redirect()->route('home.index');
        }

        return back()->withErrors(['email' => 'E-mail ou senha incorretos.']);
    }

    public function mostrarCadastro() {
        return view('auth.cadastro');
    }

    public function efetuarCadastro(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'telefone' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'telefone' => $request->telefone,
            'password' => $request->password,
        ]);

        Auth::login($user);

        return redirect()->route('home.index');
    }

    public function mostrarEsqueceuSenha() {
    return view('auth.esqueceu-senha');
    }

    public function enviarLinkRecuperacao(Request $request) {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Este e-mail não está cadastrado no nosso sistema.'
        ]);

        // Aqui dispararíamos o e-mail real. Para este estágio do projeto,
        // vamos apenas simular o sucesso e retornar uma mensagem na tela.
        return back()->with('status', 'Se o e-mail estiver correto, você receberá um link de recuperação em instantes!');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home.index');
    }
}
