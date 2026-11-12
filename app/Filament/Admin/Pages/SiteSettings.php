<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Página única onde o gestor edita textos, imagens, contatos e SEO do site.
 */
class SiteSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaintBrush;

    protected static string|UnitEnum|null $navigationGroup = 'Conteúdo do site';

    protected static ?string $navigationLabel = 'Contatos, marca e SEO';

    protected static ?string $title = 'Contatos, marca e SEO';

    protected static ?int $navigationSort = 6;

    protected static ?string $slug = 'configuracoes-do-site';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Setting::allValues());
    }

    public function form(Schema $schema): Schema
    {
        $upload = fn (string $name, string $label) => FileUpload::make($name)->label($label)
            ->image()->imageEditor()->disk('public')->directory('site')->maxSize(6144);

        return $schema->statePath('data')->components([
            Tabs::make('abas')->persistTabInQueryString()->tabs([
                Tab::make('Contato e WhatsApp')->icon(Heroicon::OutlinedPhone)->schema([
                    Section::make('WhatsApp')->columns(2)->schema([
                        TextInput::make('whatsapp_number')->label('Número do WhatsApp')->required()
                            ->helperText('Com DDI e DDD, só números. Ex.: 5524998602998')
                            ->regex('/^\d{12,13}$/')->validationMessages(['regex' => 'Use 55 + DDD + número, só dígitos.']),
                        TextInput::make('phone')->label('Telefone exibido no site')->maxLength(30),
                        Textarea::make('whatsapp_greeting')->label('Saudação na janelinha do WhatsApp')->rows(2)->maxLength(200),
                        Textarea::make('whatsapp_message')->label('Mensagem que já vem escrita')->rows(2)->maxLength(300),
                        Repeater::make('whatsapp_quick_replies')->label('Botões de resposta rápida')
                            ->schema([TextInput::make('text')->label('Texto')->required()->maxLength(50)])
                            ->addActionLabel('Adicionar botão')->grid(2)->defaultItems(0)->columnSpanFull(),
                    ]),
                    Section::make('E-mail')->columns(2)->schema([
                        TextInput::make('email')->label('E-mail exibido no site')->email(),
                        TextInput::make('lead_notify_email')->label('E-mail que recebe os pedidos de orçamento')->email()
                            ->helperText('Para onde o site envia cada formulário preenchido.'),
                    ]),
                    Section::make('Endereço, Waze e mapa')->columns(2)->schema([
                        TextInput::make('address')->label('Endereço')->required()->maxLength(200)->columnSpanFull()
                            ->helperText('Rua, número, bairro, cidade - UF. É usado no mapa e no Waze.'),
                        TextInput::make('latitude')->label('Latitude (opcional)')->numeric()
                            ->helperText('Mais precisão no Waze: no Google Maps, clique com o botão direito no local e copie os números.'),
                        TextInput::make('longitude')->label('Longitude (opcional)')->numeric(),
                        TextInput::make('service_area')->label('Área de atendimento')->maxLength(160)
                            ->helperText('Ex.: Resende, Itatiaia, Porto Real e região'),
                        TextInput::make('business_hours')->label('Horário de atendimento')->maxLength(80)
                            ->placeholder('Seg a Sáb, 7h às 18h'),
                        Toggle::make('show_map')->label('Mostrar mapa no site'),
                    ]),
                    Section::make('Empresa e redes sociais')->columns(3)->schema([
                        TextInput::make('cnpj')->label('CNPJ')->mask('99.999.999/9999-99'),
                        TextInput::make('instagram')->label('Link do Instagram')->url(),
                        TextInput::make('facebook')->label('Link do Facebook')->url(),
                    ]),
                ]),

                Tab::make('Marca')->icon(Heroicon::OutlinedSwatch)->schema([
                    Grid::make(2)->schema([
                        TextInput::make('company_name')->label('Nome da empresa')->required()->maxLength(60),
                        Grid::make(2)->schema([
                            ColorPicker::make('primary_color')->label('Cor principal')->required(),
                            ColorPicker::make('accent_color')->label('Cor de destaque (botões)')->required(),
                        ]),
                        $upload('logo', 'Logotipo')->helperText('PNG com fundo transparente, de preferência.'),
                        $upload('favicon', 'Ícone da aba do navegador')->helperText('Imagem quadrada, 512×512.'),
                    ]),
                ]),

                Tab::make('Google / SEO')->icon(Heroicon::OutlinedMagnifyingGlass)->schema([
                    TextInput::make('seo_title')->label('Título no Google')->maxLength(70)
                        ->helperText('Até ~60 caracteres. Cite o serviço e a cidade.'),
                    Textarea::make('seo_description')->label('Descrição no Google')->rows(3)->maxLength(170)
                        ->helperText('Até ~155 caracteres.'),
                    $upload('og_image', 'Imagem ao compartilhar o link (WhatsApp, Facebook)')
                        ->helperText('1200×630. Se vazio, usa a primeira foto do topo.'),
                    TextInput::make('google_analytics_id')->label('ID do Google Analytics (opcional)')->placeholder('G-XXXXXXXXXX'),
                ]),
            ]),
        ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')->label('Salvar alterações')->submit('save')->keyBindings(['mod+s']),
                        Action::make('view')->label('Ver site')->color('gray')->url(url('/'))->openUrlInNewTab(),
                    ])->sticky(),
                ]),
        ]);
    }

    public function save(): void
    {
        Setting::setMany($this->form->getState());

        Notification::make()->title('Alterações salvas!')->body('O site já está atualizado.')->success()->send();
    }
}
