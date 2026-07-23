<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Envia uma mensagem para a API de WhatsApp (Ex: Evolution API, Z-API, etc.)
     */
    public static function enviarMensagem($numero, $mensagem)
    {
        // Limpa o número para deixar apenas dígitos
        $numeroLimpo = preg_replace('/[^0-9]/', '', $numero);

        if (empty($numeroLimpo)) {
            return false;
        }

        // Adiciona DDI 55 (Brasil) se o número não tiver
        if (strlen($numeroLimpo) <= 11) {
            $numeroLimpo = '55' . $numeroLimpo;
        }

        try {
            // Exemplo genérico usando HTTP Client do Laravel
            // Substitua as variáveis de ambiente no seu .env
            $endpoint = env('WHATSAPP_API_URL');
            $token    = env('WHATSAPP_API_TOKEN');

            if (!$endpoint) {
                Log::info("WhatsApp Simulação -> Para: {$numeroLimpo} | Texto: {$mensagem}");
                return true;
            }

            Http::withHeaders([
                'apikey' => $token,
                'Content-Type' => 'application/json',
            ])->post($endpoint, [
                'number' => $numeroLimpo,
                'text'   => $mensagem,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Erro ao enviar mensagem de WhatsApp: " . $e->getMessage());
            return false;
        }
    }

    public static function enviarMensagemAniversario($numero, $nome)
    {
        $mensagem = "Olá, *{$nome}*! 🎉🎂💖\n\n"
        . "Hoje é um dia muito especial, e nós do *Nails Studio* queremos desejar a você um *Feliz Aniversário*! 🥳\n\n"
        . "Que seu novo ciclo seja repleto de saúde, felicidade, conquistas e muitos momentos inesquecíveis. Que você continue brilhando e realizando todos os seus sonhos! ✨💐\n\n"
        . "Será um prazer receber você em nosso espaço para celebrar essa nova fase e deixá-la ainda mais linda! 💅💕\n\n"
        . "Com carinho,\n"
        . "*Equipe Nails Studio* 💖";

        return self::enviarMensagem($numero, $mensagem);
    }

    public static function enviarLembreteAgendamento($numero, $nome, $servico, $hora, $manicure)
    {
        $mensagem = "Olá, *{$nome}*! 👋✨\n\n"
            . "Passando para lembrar do seu atendimento *amanhã*!\n\n"
            . "💅 *Serviço:* {$servico}\n"
            . "⏰ *Horário:* {$hora}h\n"
            . "👩‍🎨 *Profissional:* {$manicure}\n\n"
            . "Caso precise remarcar, avise-nos com antecedência.\n"
            . "Aguardamos você! 🥰";

        return self::enviarMensagem($numero, $mensagem);
    }
}