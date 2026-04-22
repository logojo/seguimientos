<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();

        session()->invalidate();
        session()->regenerateToken();

        $this->redirect('/welcome', navigate: true);
    }
}
?>

<div>
    <div class="navbar bg-primary shadow-sm mx-auto">
        <div class="px-2 md:px-20 lg:px-15 transition-[padding] duration-300 ease-in-out flex justify-between w-full">
            <div class="navbar-start">
               
                @auth
                    @include('fragments.menu')
                @endauth

                <a href="https://www.gob.mx/">
                <img src="{{ asset('images/logos/logo_gob_mx.png') }}" alt="Gobierno de México" class="w-28" />
                </a>

                      

            </div>

            @guest
                @include('fragments.menu-guest')
            @endguest  

            @auth
            <div class="navbar-end">
                <div class="text-white font-semibold text-xs">{{ Auth::user()->name }}</div>
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-circle text-white hover:text-primary">
                        <span class="material-symbols-outlined">person</span>
                    </div>
                    <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm">                        
                        <li>
                            <a href="{{ route('llave.logout') }}">
                            <span class="material-symbols-outlined">logout</span>
                            Cerrar sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            @endauth
            


        </div>
        

    </div>
</div>
