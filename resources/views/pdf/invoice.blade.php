<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $transaction->id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
            line-height: 24px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 16px;
            line-height: 24px;
            color: #555;
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }
        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }
        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }
        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }
        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.item td{
            border-bottom: 1px solid #eee;
        }
        .invoice-box table tr.item.last td {
            border-bottom: none;
        }
        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                TheTrader.id
                            </td>
                            <td>
                                Invoice #: {{ $transaction->id }}<br>
                                Created: {{ $transaction->created_at->format('F d, Y') }}<br>
                                Status: {{ ucfirst($transaction->status) }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                TheTrader.id, Inc.<br>
                                Jakarta, Indonesia
                            </td>
                            <td>
                                {{ $transaction->user->name }}<br>
                                {{ $transaction->user->email }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="heading">
                <td>
                    Payment Method
                </td>
                <td>
                    Amount
                </td>
            </tr>
            <tr class="details">
                <td>
                    {{ ucfirst($transaction->payment_type ?? 'Online Payment') }}
                </td>
                <td>
                    Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                </td>
            </tr>
            <tr class="heading">
                <td>
                    Item
                </td>
                <td>
                    Price
                </td>
            </tr>
            <tr class="item">
                <td>
                    {{ $transaction->package->name ?? 'Subscription' }}
                </td>
                <td>
                    Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                </td>
            </tr>
            <tr class="total">
                <td></td>
                <td>
                    Total: Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
