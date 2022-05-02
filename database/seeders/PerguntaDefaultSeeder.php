<?php

namespace Database\Seeders;

use App\Models\OpcaoQuestao;
use App\Models\Questao;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerguntaDefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $perguntas = [
            1 => [
                'pergunta' => 'Como os sintomas estão evoluindo?',
                'opcoes' => [
                    'Melhoraram',
                    'Estão iguais',
                    'Pioraram'
                ]
            ],
            2 => [
                'pergunta' => 'Você praticou exercícios físicos?',
                'opcoes' => [
                    'Sim',
                    'Não',
                ]
            ]
        ];

         foreach ($perguntas as $pergunta) {
             $new = new Questao();
             $new->descricao = $pergunta['pergunta'];
             $new->tipo = 'O';
             $new->save();

             foreach ($pergunta['opcoes'] as $opcao) {
                 $op = new OpcaoQuestao;
                 $op->descricao  = $opcao;
                 $op->questao_id = $new->id;
                 $op->save();
             }
         }
    }
}
