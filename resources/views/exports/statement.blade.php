@php
    /** @var \App\Services\Reports\AccountStatement $statement */
    $promise = $statement->promise;
    $standing = $statement->standing;
    $money = fn ($value) => '$'.number_format((float) $value, 0, ',', '.');
    $quotaCount = $promise->quotas->count();
    $paidShare = $quotaCount > 0 ? min(100, round($standing->paidQuotas() / $quotaCount * 100)) : 0;
    $next = $statement->pendingQuotas[0] ?? null;

    [$statusLabel, $statusDetail] = match ($statement->status) {
        \App\Enums\PortfolioStatus::UP_TO_DATE => ['Al día', $next
            ? 'Próxima cuota: N° '.$next['number'].', vence el '.$next['due_date']->format('d/m/Y').' · '.$money($next['pending'])
                .($next['pending'] < $next['amount'] ? ' (de '.$money($next['amount']).')' : '')
            : 'No tiene cuotas pendientes.'],
        \App\Enums\PortfolioStatus::UP_TO_30, \App\Enums\PortfolioStatus::UP_TO_90, \App\Enums\PortfolioStatus::OVER_90 => ['En mora',
            'Valor vencido: '.$money($standing->overdueAmount).' · '.$standing->daysOverdue.' '.($standing->daysOverdue === 1 ? 'día' : 'días').' de atraso, desde el '
            .\Carbon\CarbonImmutable::parse($standing->currentQuota['due_date'])->format('d/m/Y').'.'],
        \App\Enums\PortfolioStatus::PAID => ['Pagada', 'La promesa está pagada en su totalidad.'],
        \App\Enums\PortfolioStatus::CHECK_BALANCE => ['Cuotas pagadas', 'Todas las cuotas están cubiertas y queda un saldo de '.$money($statement->balance).'. Comuníquese con la oficina para confirmarlo.'],
        \App\Enums\PortfolioStatus::NO_SCHEDULE => ['Saldo pendiente', 'Queda un saldo de '.$money($statement->balance).' por pagar.'],
    };
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Estado de cuenta | {{ $promise->number }}</title>

    <style>
        @page {
            margin: 1.3cm 1.5cm 2cm 1.5cm;
        }

        body {
            margin: 0;
            color: #1f2937;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10.5px;
            line-height: 1.4;
        }

        .muted { color: #6b7280; }
        .right { text-align: right; }
        .center { text-align: center; }
        .strong { font-weight: bold; }
        .accent { color: #c2410c; }

        footer {
            position: fixed;
            bottom: -1.3cm;
            left: 0;
            right: 0;
            height: 0.8cm;
            border-top: 1px solid #e5e7eb;
            padding-top: 6px;
            color: #6b7280;
            font-size: 9px;
        }


        table { width: 100%; border-collapse: collapse; }

        .header td { vertical-align: top; }
        .header img { height: 64px; }
        .company { text-align: right; font-size: 10px; color: #4b5563; }
        .company .name { font-size: 12px; font-weight: bold; color: #1f2937; }

        .title {
            margin-top: 14px;
            padding: 10px 14px;
            border-left: 5px solid #f97316;
            background: #fff7ed;
        }
        .title td { vertical-align: middle; }
        .title .label { font-size: 9px; letter-spacing: 1px; color: #9a3412; text-transform: uppercase; }
        .title .number { font-size: 20px; font-weight: bold; color: #1f2937; }

        h2 {
            margin: 18px 0 6px 0;
            font-size: 12px;
            color: #9a3412;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .details td { vertical-align: top; padding: 2px 0; }
        .details .key { width: 34%; color: #6b7280; }

        .figures td {
            width: 25%;
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            vertical-align: top;
        }
        .figures .key { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
        .figures .value { font-size: 15px; font-weight: bold; margin-top: 3px; }

        .status { margin-top: 8px; border: 1px solid #e5e7eb; }
        .status td { padding: 9px 12px; vertical-align: middle; }
        .badge { padding: 4px 10px; font-weight: bold; font-size: 11px; }

        .bar { height: 6px; background: #e5e7eb; margin-top: 4px; }
        .bar div { height: 6px; background: #f97316; }

        .list th {
            padding: 6px 8px;
            background: #f3f4f6;
            color: #374151;
            font-size: 9.5px;
            text-align: left;
            border-bottom: 1px solid #d1d5db;
        }
        .list td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        .list th.right, .list td.right { text-align: right; }
        .total td { padding: 6px 8px; border-top: 1.5px solid #9ca3af; font-weight: bold; }
        .list .late { color: #b91c1c; font-weight: bold; }

        .note {
            margin-top: 18px;
            padding: 8px 12px;
            border-left: 4px solid #d1d5db;
            color: #4b5563;
            font-size: 9.5px;
        }
    </style>
</head>

<body>
    <footer>
        {{-- The page numbers are drawn by the controller once the PDF is laid out --}}
        LOTEOS M.C.R.V · Estado de cuenta {{ $promise->number }} · generado el {{ $statement->today->format('d/m/Y') }}
    </footer>

    <table class="header">
        <tr>
            <td><img src="{{ public_path('img/logo.png') }}" alt="LOTEOS M.C.R.V"></td>
            <td class="company">
                <div class="name">Martha Carolina Riveros Vargas</div>
                <div>NIT 1.098.656.012-1 · Yopal, Casanare</div>
                <div>Tel. 312 885 4721 · carolinarv.ciudadelarivarca@gmail.com</div>
            </td>
        </tr>
    </table>

    <table class="title">
        <tr>
            <td>
                <div class="label">Estado de cuenta</div>
                <div class="number">{{ $promise->number }}</div>
            </td>
            <td class="right">
                <div class="strong">{{ $promise->category?->name ? 'Proyecto '.$promise->category->name : '' }}</div>
                <div class="muted">Generado el {{ $statement->today->translatedFormat('j \d\e F \d\e Y') }}</div>
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 14px;">
                <h2>{{ $promise->buyers->count() === 1 ? 'Comprador' : 'Compradores' }}</h2>
                <table class="details">
                    @foreach ($promise->buyers as $buyer)
                        <tr>
                            <td>
                                <span class="strong">{{ trim(preg_replace('/\s+/', ' ', $buyer->names.' '.$buyer->surnames)) }}</span><br>
                                <span class="muted">{{ $buyer->document_type?->label() ?? 'Documento' }} {{ $buyer->document_number_masked }}</span>
                            </td>
                        </tr>
                    @endforeach
                    @if ($statement->buyer->hiddenPhone() || $statement->buyer->hiddenEmail())
                        <tr>
                            <td class="muted" style="padding-top: 6px;">
                                Contacto registrado: {{ collect([$statement->buyer->hiddenPhone(), $statement->buyer->hiddenEmail()])->filter()->implode(' · ') }}
                            </td>
                        </tr>
                    @endif
                </table>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <h2>Promesa</h2>
                <table class="details">
                    <tr><td class="key">Fecha de firma</td><td>{{ $promise->signature_date ? $promise->signature_date->translatedFormat('j \d\e F \d\e Y') : 'Sin definir' }}</td></tr>
                    <tr><td class="key">{{ $promise->parcels->count() === 1 ? 'Lote' : 'Lotes' }}</td><td>{{ $statement->lots() ?: 'Sin definir' }}</td></tr>
                    <tr><td class="key">Forma de pago</td><td>{{ $promise->payment_method?->label() ?? 'Sin definir' }}{{ $quotaCount ? ', '.$quotaCount.' '.($quotaCount === 1 ? 'cuota' : 'cuotas') : '' }}</td></tr>
                    @if ($promise->payment_frequency)
                        <tr><td class="key">Frecuencia</td><td>{{ $promise->payment_frequency->label() }}</td></tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <h2>Resumen</h2>
    <table class="figures">
        <tr>
            <td>
                <div class="key">Valor de la promesa</div>
                <div class="value">{{ $money($promise->value) }}</div>
            </td>
            <td>
                <div class="key">Cuota inicial</div>
                <div class="value">{{ $money($promise->initial_fee) }}</div>
            </td>
            <td>
                <div class="key">Total pagado</div>
                <div class="value">{{ $money($statement->paid) }}</div>
            </td>
            <td style="background: #fff7ed; border-color: #fed7aa;">
                <div class="key">Saldo pendiente</div>
                <div class="value accent">{{ $money($statement->balance) }}</div>
            </td>
        </tr>
    </table>

    <table class="status">
        <tr>
            <td style="width: 21%; white-space: nowrap;">
                <span class="badge" style="background: #{{ $statement->status->fill() }};">{{ $statusLabel }}</span>
            </td>
            <td>{{ $statusDetail }}</td>
            @if ($quotaCount)
                <td style="width: 26%;" class="right">
                    <span class="strong">{{ $standing->paidQuotas() }} de {{ $quotaCount }}</span> cuotas pagadas
                    <div class="bar"><div style="width: {{ $paidShare }}%;"></div></div>
                </td>
            @endif
        </tr>
    </table>

    <h2>Pagos realizados</h2>
    @if ($statement->payments->isEmpty())
        <p class="muted">Todavía no hay pagos registrados.</p>
    @else
        <table class="list">
            <thead>
                <tr>
                    <th style="width: 13%;">Fecha</th>
                    <th style="width: 14%;">Recibo</th>
                    <th style="width: 17%;">Medio de pago</th>
                    <th>Concepto</th>
                    <th style="width: 16%;" class="right">Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($statement->payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_date?->format('d/m/Y') ?? 'Sin fecha' }}</td>
                        <td>{{ $payment->bill_number }}</td>
                        <td>{{ $payment->payment_method?->label() }}{{ $payment->bank ? ' · '.$payment->bank : '' }}</td>
                        <td>{{ $payment->is_initial_fee ? 'Cuota inicial. ' : '' }}{{ trim(preg_replace('/\s+/', ' ', (string) $payment->observations)) }}</td>
                        <td class="right">{{ $money($payment->paid_amount) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{-- Apart from the table, so a page break before it does not repeat the table header alone --}}
        <table class="total">
            <tr>
                <td class="right">Total pagado ({{ $statement->payments->count() }} {{ $statement->payments->count() === 1 ? 'pago' : 'pagos' }})</td>
                <td class="right" style="width: 16%;">{{ $money($statement->paid) }}</td>
            </tr>
        </table>
    @endif

    @if ($statement->pendingQuotas)
        <h2>Cuotas por pagar</h2>
        <table class="list">
            <thead>
                <tr>
                    <th style="width: 10%;">Cuota</th>
                    <th style="width: 22%;">Fecha de vencimiento</th>
                    <th style="width: 22%;" class="right">Valor de la cuota</th>
                    <th style="width: 22%;" class="right">Por pagar</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($statement->pendingQuotas as $quota)
                    <tr>
                        <td>N° {{ $quota['number'] }}</td>
                        <td>{{ $quota['due_date']->format('d/m/Y') }}</td>
                        <td class="right">{{ $money($quota['amount']) }}</td>
                        <td class="right">{{ $money($quota['pending']) }}</td>
                        <td class="{{ $quota['overdue'] ? 'late' : '' }}">
                            {{ $quota['overdue'] ? 'Vencida' : 'Por vencer' }}{{ $quota['pending'] < $quota['amount'] ? ' · con abono' : '' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="note">
        Este estado de cuenta es informativo y refleja los pagos registrados hasta el {{ $statement->today->format('d/m/Y') }}.
        Si encuentra alguna diferencia o tiene un pago que no aparece, comuníquese con nosotros al 312 885 4721
        o a carolinarv.ciudadelarivarca@gmail.com.
    </div>
</body>

</html>
