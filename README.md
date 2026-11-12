# Soares Mudanças — site + painel administrativo

Laravel 12 + Filament 4. Site público editável pelo gestor, formulário de orçamento que envia e-mail
(sem abrir o app de e-mail do cliente), janela de WhatsApp na página, botões Waze/Google Maps e
painel administrativo em `/admin`.

## O que tem

| Área | Onde |
|---|---|
| Site público | `resources/views/site/home.blade.php`, `public/css/site.css`, `public/js/site.js` (sem build/Vite) |
| Textos, imagens, contatos, WhatsApp, endereço, SEO | Admin → **Textos, imagens e contatos** (`app/Filament/Admin/Pages/SiteSettings.php`) |
| Serviços / Depoimentos / Galeria | Admin → Conteúdo do site |
| Pedidos de orçamento (status, valor, anotações, botão WhatsApp) | Admin → **Orçamentos** |
| Painel com indicadores (pedidos, cliques no WhatsApp, fechados) | Admin → Painel de Controle |
| Usuários do painel | Admin → Sistema → Usuários (não existe cadastro público) |

Valores padrão das configurações: `config/site.php` (o que o gestor salva no admin tem prioridade).

### Segurança
- Admin só com login; senha mínima com letras e números; recuperação de senha por e-mail.
- Formulário: CSRF, validação no servidor, campo-isca anti-robô, limite de 3 envios/min e 10/dia por IP,
  Cloudflare Turnstile opcional (`TURNSTILE_*` no `.env`).
- O pedido é salvo no banco **antes** do e-mail: se o SMTP falhar, nada se perde.
- Cabeçalhos de segurança (`app/Http/Middleware/SecurityHeaders.php`), HTTPS forçado em produção.
- Cliques são registrados com IP em hash (sem dado pessoal).

## Rodar no seu computador

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link
php artisan make:filament-user      # cria o login do gestor
php artisan serve                   # http://127.0.0.1:8000  e  /admin
php artisan test                    # 8 testes
```

## Publicar na Hostinger (hospedagem compartilhada)

1. **hPanel → Sites → Adicionar site** com `soaresmudancas.com` (o domínio já está na Hostinger, o DNS fica automático).
2. **Avançado → Acesso SSH**: ative. **Avançado → Configuração do PHP**: PHP 8.3.
3. **Bancos de dados → MySQL**: crie banco + usuário (anote os dados).
4. **E-mails**: crie `contato@soaresmudancas.com` (veja seção abaixo).
5. Pelo SSH (mesmo esquema do projeto dízimo: repositório inteiro no `public_html`, o `.htaccess` da raiz manda tudo para `public/`):
   ```bash
   cd ~/domains/soaresmudancas.com
   mv public_html public_html_old
   git clone https://github.com/Andre-1845/soares_mudancas.git public_html
   cd public_html
   composer install --no-dev --optimize-autoloader
   cp .env.hostinger.example .env && nano .env                       # preencha banco e senha do e-mail
   php artisan key:generate
   php artisan migrate --force --seed
   php artisan storage:link
   php artisan make:filament-user
   php artisan optimize && php artisan filament:optimize
   ```
6. **Segurança → SSL**: instale o SSL grátis e ative "forçar HTTPS".
7. Acesse `https://soaresmudancas.com/admin` e preencha endereço, fotos, CNPJ etc.

### Atualizar depois
```bash
cd ~/domains/soaresmudancas.com/public_html
git pull && composer install --no-dev --optimize-autoloader
php artisan migrate --force && php artisan optimize:clear && php artisan optimize && php artisan filament:optimize
```

## E-mail da empresa (Hostinger)
- hPanel → E-mails → criar conta `contato@soaresmudancas.com`.
- Ler no navegador: webmail da Hostinger. No celular: app Gmail/Outlook com IMAP `imap.hostinger.com:993` e SMTP `smtp.hostinger.com:465` (SSL).
- O site usa essa mesma conta (SMTP) para enviar os pedidos de orçamento.
- Para não cair no spam: em E-mails → Configurações de DNS, confirme que **SPF, DKIM e DMARC** estão ativos.

## Próximas fases (sugestão)
- **Agenda de mudanças**: calendário (data, equipe, caminhão, endereços) ligado ao orçamento fechado.
- **Clientes**: cadastro único a partir dos orçamentos.
- **Financeiro**: receitas/despesas por mudança (combustível, pedágio, ajudantes, material) e margem.
- **Calculadora de orçamento**: m³ por cômodo/itens + distância → faixa de preço, regras editáveis no admin.
