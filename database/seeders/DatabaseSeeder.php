<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (Service::count() === 0) {
            $services = [
                ['🏠', 'Mudanças residenciais', 'Transporte seguro de móveis e objetos pessoais, com desmontagem e montagem.'],
                ['🏢', 'Mudanças comerciais', 'Planejamento para minimizar o impacto nas atividades da sua empresa.'],
                ['📦', 'Embalagem', 'Materiais de proteção para garantir a integridade dos seus bens.'],
                ['🚛', 'Transporte interestadual', 'Atendimento para diversas cidades e estados com segurança.'],
                ['🔒', 'Guarda-móveis', 'Espaço seguro para armazenagem temporária dos seus bens.'],
                ['📋', 'Orçamento grátis', 'Avaliação sem compromisso, pelo WhatsApp ou formulário.'],
            ];
            foreach ($services as $i => [$icon, $title, $desc]) {
                Service::create(['icon' => $icon, 'title' => $title, 'description' => $desc, 'sort' => $i]);
            }
        }

        // Exemplos DESATIVADOS: substitua por depoimentos reais (nome, cidade) antes de ativar.
        if (Testimonial::count() === 0) {
            foreach ([
                'Equipe extremamente cuidadosa e pontual. Recomendo!',
                'Minha mudança ocorreu sem nenhum problema. Excelente atendimento.',
                'Preço justo e profissionais muito educados.',
            ] as $i => $text) {
                Testimonial::create(['name' => 'Nome do cliente', 'city' => 'Resende', 'text' => $text, 'sort' => $i, 'active' => false]);
            }
        }

        // Usuário gestor inicial (defina ADMIN_EMAIL e ADMIN_PASSWORD no .env antes de rodar o seed)
        if (env('ADMIN_EMAIL') && env('ADMIN_PASSWORD') && ! User::where('email', env('ADMIN_EMAIL'))->exists()) {
            User::create([
                'name' => env('ADMIN_NAME', 'Gestor'),
                'email' => env('ADMIN_EMAIL'),
                'password' => Hash::make(env('ADMIN_PASSWORD')),
            ]);
        }
    }
}
