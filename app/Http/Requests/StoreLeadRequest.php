<?php

namespace App\Http\Requests;

use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim(strip_tags((string) $this->name)),
            'phone' => trim(strip_tags((string) $this->phone)),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[\d\s()+\-.]{8,30}$/'],
            'email' => ['nullable', 'email:filter', 'max:150'],
            'service_type' => ['nullable', Rule::in(array_keys(Lead::SERVICE_TYPES))],
            'origin' => ['nullable', 'string', 'max:150'],
            'destination' => ['nullable', 'string', 'max:150'],
            'move_date' => ['nullable', 'date', 'after_or_equal:today'],
            'property_size' => ['nullable', Rule::in(array_keys(Lead::PROPERTY_SIZES))],
            'message' => ['nullable', 'string', 'max:2000'],
            'website' => ['nullable', 'max:0'], // honeypot: humanos não preenchem
            'consent' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Informe seu nome.',
            'name.min' => 'Informe seu nome.',
            'phone.required' => 'Informe um telefone ou WhatsApp para contato.',
            'phone.regex' => 'Telefone inválido.',
            'email.email' => 'E-mail inválido.',
            'move_date.after_or_equal' => 'A data da mudança não pode ser no passado.',
            'move_date.date' => 'Data inválida.',
            'website.max' => 'Envio bloqueado.',
            'consent.accepted' => 'É preciso autorizar o contato para enviarmos o orçamento.',
            '*.max' => 'Texto muito longo.',
            '*.in' => 'Opção inválida.',
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            $secret = config('site.turnstile.secret_key');
            if (! $secret) {
                return;
            }
            try {
                $ok = Http::asForm()->timeout(8)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => $secret,
                    'response' => $this->input('cf-turnstile-response'),
                    'remoteip' => $this->ip(),
                ])->json('success');
            } catch (\Throwable) {
                $ok = false;
            }
            if (! $ok) {
                $validator->errors()->add('captcha', 'Não foi possível confirmar que você não é um robô. Tente novamente.');
            }
        }];
    }
}
