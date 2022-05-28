<?php

namespace Database\Seeders;

use App\Models\Tag;
use Exception;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            $tags = [
                'Processo Seletivo', 
                'Indisponibilidade',
                'Manutenção',
                'Consulta Pública',
                'Comprasnet',
                'Pesquisa',
                'Instrução Normativa',
                'Ministério da Economia',
                'Área de Trabalho',
                'Comunicado'
            ];
            
            $this->command->withProgressBar($tags, function($tag){
                Tag::firstOrCreate([
                    'tag' => $tag
                ], [
                    'tag' => $tag
                ]);
            });
    
        } catch (Exception $exception) {
            $this->command->error($exception->getMessage());
        }
    }
}
