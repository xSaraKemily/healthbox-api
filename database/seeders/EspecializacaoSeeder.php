<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EspecializacaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('especializacoes')->insert([
            ['nome' => 'Acupuntura'],
            ['nome' => 'Alergia e imunologia'],
            ['nome' => 'Anestesiologia'],
            ['nome' => 'Angiologia'],
            ['nome' => 'Cardiologia'],
            ['nome' => 'Cirurgia cardiovascular'],
            ['nome' => 'Cirurgia da mão'],
            ['nome' => 'Cirurgia de cabeça e pescoço'],
            ['nome' => 'Cirurgia do aparelho digestivo'],
            ['nome' => 'Cirurgia geral'],
            ['nome' => 'Cirurgia oncológica'],
            ['nome' => 'Cirurgia pediátrica'],
            ['nome' => 'Cirurgia plástica'],
            ['nome' => 'Cirurgia torácica'],
            ['nome' => 'Cirurgia vascular'],
            ['nome' => 'Clínica médica'],
            ['nome' => 'Coloproctologia'],
            ['nome' => 'Dermatologia'],
            ['nome' => 'Endocrinologia e metabologia'],
            ['nome' => 'Endoscopia'],
            ['nome' => 'Gastroenterologia'],
            ['nome' => 'Genética médica'],
            ['nome' => 'Geriatria'],
            ['nome' => 'Ginecologia e obstetrícia'],
            ['nome' => 'Hematologia e hemoterapia'],
            ['nome' => 'Homeopatia'],
            ['nome' => 'Infectologia'],
            ['nome' => 'Mastologia'],
            ['nome' => 'Medicina de emergência'],
            ['nome' => 'Medicina de família e comunidade'],
            ['nome' => 'Medicina do trabalho'],
            ['nome' => 'Medicina de tráfego'],
            ['nome' => 'Medicina esportiva'],
            ['nome' => 'Medicina física e reabilitação'],
            ['nome' => 'Medicina intensiva'],
            ['nome' => 'Medicina legal e perícia médica'],
            ['nome' => 'Medicina nuclear'],
            ['nome' => 'Medicina preventiva e social'],
            ['nome' => 'Nefrologia'],
            ['nome' => 'Neurocirurgia'],
            ['nome' => 'Neurologia'],
            ['nome' => 'Nutrologia'],
            ['nome' => 'Oftalmologia'],
            ['nome' => 'Oncologia clínica'],
            ['nome' => 'Ortopedia e traumatologia'],
            ['nome' => 'Otorrinolaringologia'],
            ['nome' => 'Patologia'],
            ['nome' => 'Patologia clínica/medicina laboratorial'],
            ['nome' => 'Pediatria'],
            ['nome' => 'Pneumologia'],
            ['nome' => 'Psiquiatria'],
            ['nome' => 'Radiologia e diagnóstico por imagem'],
            ['nome' => 'Radioterapia'],
            ['nome' => 'Reumatologia'],
            ['nome' => 'Urologia'],
        ]);
    }
}
