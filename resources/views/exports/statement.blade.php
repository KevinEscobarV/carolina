<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Estado de cuenta | {{ $promise->number }}</title>

    <style>
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        a {
            color: #F97316;
            text-decoration: none;
        }

        body {
            position: relative;
            width: 18cm;
            height: 29.7cm;
            margin: 0 auto;
            color: #555555;
            background: #FFFFFF;
            font-size: 14px;
            font-family: "Gill Sans", sans-serif;
        }

        header {
            padding: 10px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #AAAAAA;
        }

        #logo {
            float: left;
        }

        #logo img {
            height: 100px;
        }

        #company {
            text-align: right;
        }


        #details {
            margin-bottom: 50px;
        }

        #client {
            padding-left: 6px;
            border-left: 6px solid #F97316;
            float: left;
        }

        #client .to {
            color: #777777;
        }

        h2.name {
            font-size: 1.2em;
            font-weight: normal;
            margin: 0;
        }

        #invoice {
            text-align: right;
        }

        #invoice h1 {
            color: #F97316;
            font-size: 1.6em;
            line-height: 1em;
            font-weight: normal;
            margin: 0 0 10px 0;
        }

        #invoice .date {
            font-size: 1.1em;
            color: #777777;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 20px;
        }

        table th,
        table td {
            padding: 8px;
            background: #EEEEEE;
            text-align: center;
            border-bottom: 1px solid #FFFFFF;
        }

        table th {
            font-weight: normal;
        }

        table td {
            text-align: right;
        }

        table td h3 {
            color: #943f07;
            font-size: 1.2em;
            font-weight: normal;
            margin: 0 0 0.2em 0;
        }

        table .no {
            color: #FFFFFF;
            background: #943f07;
        }

        table .desc {
            text-align: left;
        }

        table .unit {
            background: #DDDDDD;
        }

        table .qty {}
        table .total {
            background: #943f07;
            color: #FFFFFF;
        }

        table td.unit {
            text-align: right;
        }
        table td.qty,
        table td.concept {
            text-align: left;
            font-size: 0.8em;
            white-space: normal;
        }
        table td.no {
            text-align: left;
            font-size: 0.8em;
            white-space: normal;
        }
        table td.total {
            font-size: 1.2em;
            color: #FFFFFF;
        }

        table tbody tr:last-child td {
            border: none;
        }

        table tfoot td {
            padding: 7px 20px;
            background: #FFFFFF;
            border-bottom: none;
            font-size: 1.2em;
            white-space: nowrap;
            border-top: 1px solid #AAAAAA;
        }

        table tfoot tr:first-child td {
            border-top: none;
        }

        table tfoot tr:last-child td {
            color: #77381b;
            font-size: 1.4em;
            border-top: 1px solid #803900;

        }

        table tfoot tr td:first-child {
            border: none;
        }

        #thanks {
            margin-top: 50px;
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        #notices {
            padding-left: 6px;
            border-left: 6px solid #F97316;
        }

        #notices .notice {
            font-size: 0.8em;
        }

        footer {
            color: #777777;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #AAAAAA;
            padding: 8px 0;
            text-align: center;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <header class="clearfix">
        <div id="logo" style="margin-left: -18px">
            <img src="{{ asset('img/logo.png') }}">
          </div>
        <div id="company">
            <h2 class="name">Martha Carolina Riveros Vargas - {{$promise->category->name}}</h2>
            <div>Yopal - Casanare</div>
            <div>Tel: 312 885 4721</div>
            <div>NIT: 1.098.656.012-1</div>
            <div><a href="mailto:carolinarv.ciudadelarivarca@gmail.com">carolinarv.ciudadelarivarca@gmail.com</a></div>
        </div>
        </div>
    </header>
    <main>
        <div id="details" class="clearfix">
            <div id="client">
                <h2 class="name">{{ $buyer->names }} {{ $buyer->surnames }}</h2>
                <div>{{ $buyer->document_type->label() }}: {{ $buyer->document_number }}</div>
                <div>{{ $buyer->address }}</div>
                <div class="address">Tel: {{ $buyer->phone_one ?? 'No definido' }}</div>
                <div class="email"><a href="mailto:{{ $buyer->email }}">{{ $buyer->email }}</a></div>

            </div>
            <div id="invoice">
                <h1>{{ $promise->number }}</h1>
                <div class="date">Fecha de promesa: {{ $promise->signature_date ? $promise->signature_date->translatedFormat("F j/Y") : 'Sin definir' }}</div>
                <div>Metodo de Pago: {{ $promise->payment_method->label() }}</div>
                <div>Cuotas: {{ $promise->number_of_fees }}</div>
                <div>Valor Promesa: {{ $promise->value_formatted }} COP</div>
            </div>
        </div>
        <table border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th class="no" style="text-align: left">Numero Recibo</th>
                    <th class="desc" style="text-align: center">Fecha Pactada</th>
                    <th class="unit" style="text-align: right">Valor Acordado</th>
                    <th class="qty">Fecha Pago</th>
                    <th class="concept">Concepto</th>
                    <th class="total" style="text-align: right">Valor Pagado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($promise->payments->sortBy('payment_date') as $payment)
                    <tr>
                        <td class="no" style="text-align: left">{{ $payment->bill_number }}</td>
                        <td class="desc" style="text-align: center">
                            {{ $payment->agreement_date ? $payment->agreement_date->translatedFormat("d/m/Y") : 'Sin definir' }}
                        </td>
                        <td class="unit">${{ $payment->agreement_amount_formatted }}</td>
                        <td class="desc" style="text-align: center">
                            {{ $payment->payment_date ? $payment->payment_date->translatedFormat("d/m/Y") : 'Sin definir' }}
                        </td>
                        <td class="concept">{{ $payment->observations }}</td>
                        <td class="total">${{ $payment->paid_amount_formatted }} COP</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2">Total Valor Acordado</td>
                    <td>$ {{ $total_agreement_amount }} COP</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2">Cuotas Pagadas</td>
                    <td>{{ $promise->number_of_paid_fees }}</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2">Frecuencia de Pago</td>
                    <td>{{ $promise->payment_frequency->label() }}</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2">TOTAL PAGADO</td>
                    <td>$ {{ $total_paid_amount }} COP</td>
                </tr>
            </tfoot>
        </table>
        {{-- <div id="thanks">¡Gracias por su Compra!</div> --}}
        <div id="notices">
            <div>NOTA:</div>
            <div class="notice">La siguiente cuenta como comprobante de pago de sus productos.</div>
        </div>
    </main>
    <footer>
        Estado de cuenta generado automáticamente por el sistema de LOTEOS M.C.R.V
    </footer>
</body>

</html>
