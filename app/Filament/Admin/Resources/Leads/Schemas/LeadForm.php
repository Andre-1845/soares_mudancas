<?php

namespace App\Filament\Admin\Resources\Leads\Schemas;

use App\Models\Lead;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Cliente e mudança')
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->label('Nome')->required()->maxLength(120),
                        TextInput::make('phone')->label('WhatsApp / telefone')->tel()->required()->maxLength(30),
                        TextInput::make('email')->label('E-mail')->email()->maxLength(150),
                        Select::make('service_type')->label('Serviço')->options(Lead::SERVICE_TYPES),
                        TextInput::make('origin')->label('Origem')->maxLength(150),
                        TextInput::make('destination')->label('Destino')->maxLength(150),
                        DatePicker::make('move_date')->label('Data desejada')->native(false)->displayFormat('d/m/Y'),
                        Select::make('property_size')->label('Tamanho do imóvel')->options(Lead::PROPERTY_SIZES),
                        Textarea::make('message')->label('Mensagem do cliente')->rows(4)->columnSpanFull(),
                    ]),
                Section::make('Atendimento')
                    ->columnSpan(1)
                    ->schema([
                        Select::make('status')->label('Situação')->options(Lead::STATUSES)->default('novo')->required()->native(false),
                        TextInput::make('quoted_value')->label('Valor orçado')->numeric()->prefix('R$')->minValue(0),
                        Textarea::make('notes')->label('Anotações internas')->rows(6)
                            ->helperText('Só aparece aqui no painel.'),
                    ]),
            ]);
    }
}
