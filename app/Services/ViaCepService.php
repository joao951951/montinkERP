<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ViaCepService
{
    protected const BASE_URL = 'https://viacep.com.br/ws/';

    public function getAddresByCEP(string $cep): ?array
    {
        $cep = preg_replace('/[^0-9]/', '', $cep);
        
        if (strlen($cep) != 8) {
            return ['erro' => 'CEP inválido'];
        }

        $url = "https://viacep.com.br/ws/{$cep}/json/";
        
        try {
            $response = file_get_contents($url);
            $data = json_decode($response, true);
            if (isset($data['erro'])) {
                return ['erro' => 'CEP não encontrado'];
            }

            return $data;
        } catch (Exception $e) {
            return ['erro' => 'Erro ao consultar CEP: ' . $e->getMessage()];
        }
    }
}