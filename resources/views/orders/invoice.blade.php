<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture - Commande #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }

        .invoice-container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
        }

        .company-info img {
            max-width: 150px;
            margin-bottom: 10px;
        }

        .company-info h1 {
            font-size: 24px;
            margin: 0;
            color: #3b82f6;
        }

        .invoice-details {
            text-align: right;
        }

        .invoice-details h2 {
            font-size: 32px;
            color: #3b82f6;
            margin: 0;
        }

        .invoice-details p {
            margin: 5px 0;
        }

        .customer-info {
            margin-bottom: 40px;
            border-top: 2px solid #3b82f6;
            padding-top: 20px;
        }

        .customer-info h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #555;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .invoice-table th, .invoice-table td {
            border-bottom: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        .invoice-table th {
            background-color: #f7f7f7;
            color: #666;
            font-weight: bold;
        }

        .invoice-table tr:last-child td {
            border-bottom: none;
        }

        .invoice-totals {
            width: 100%;
            max-width: 300px;
            margin-left: auto;
        }

        .invoice-totals .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }

        .invoice-totals .total-row.grand-total {
            border-top: 2px solid #3b82f6;
            font-size: 20px;
            font-weight: bold;
            color: #3b82f6;
            margin-top: 10px;
            padding-top: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            border-top: 1px dashed #ccc;
            padding-top: 20px;
            color: #888;
            font-size: 14px;
        }

        .footer p {
            margin: 5px 0;
        }

        .print-button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .print-button:hover {
            background-color: #2563eb;
        }

        /* Styles d'impression */
        @media print {
            body {
                background-color: #fff;
            }
            .invoice-container {
                box-shadow: none;
                border: none;
                padding: 0;
            }
            .print-button, .invoice-container::before {
                display: none;
            }
            .invoice-header, .customer-info, .invoice-table, .invoice-totals, .footer {
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>

<button onclick="window.print()" class="print-button">
    Imprimer la facture
</button>

<div class="invoice-container">
    <div class="invoice-header">
        <div class="company-info">
            <img src="{{ asset('images/nova-tech-logo-white.png') }}" alt="Nova Tech Logo">
            <h1>Nova Tech Bénin</h1>
            <p>123 Rue de la Liberté</p>
            <p>01 BP 1234, Cotonou, Bénin</p>
            <p>Tél : +229 12 34 56 78</p>
            <p>Email : contact@novatechbenin.com</p>
        </div>
        <div class="invoice-details">
            <h2>FACTURE</h2>
            <p><strong>Facture :</strong> {{ str_pad($order->order_group, 6, '0', STR_PAD_LEFT) }}</p>
            <p><strong>Date :</strong> {{ $order->created_at->format('d/m/Y') }}</p>

            @php
                $statusText = [
                    'pending' => 'En attente',
                    'processing' => 'En cours de traitement',
                    'shipped' => 'Expédiée',
                    'delivered' => 'Livrée',
                    'cancelled' => 'Annulée'
                ];
            @endphp
            <p><strong>Statut :</strong> {{ $statusText[$order->status] ?? 'Inconnu' }}</p>
        </div>
    </div>

    <div class="customer-info">
        <h3>Facturé à :</h3>
        <p><strong>{{ $order->customer_name }}</strong></p>
        <p>{{ $order->customer_address }}</p>
        <p>Tél : {{ $order->customer_phone }}</p>
        <p>Email : {{ $order->customer_email }}</p>
    </div>

    <table class="invoice-table">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orderItems as $item)
                <tr>
                    <td>{{ $item['product']->name }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>{{ number_format($item['product']->price, 0, ',', ' ') }} CFA</td>
                    <td>{{ number_format($item['subtotal'], 0, ',', ' ') }} CFA</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="invoice-totals">
        <div class="total-row">
            <span>Sous-total :</span>
            <span>{{ number_format($totalAmount, 0, ',', ' ') }} CFA</span>
        </div>
        <div class="total-row">
            <span>Frais de livraison :</span>
            <span>{{ number_format($order->shipping_fee, 0, ',', ' ') }}CFA</span>
        </div>
        <div class="total-row grand-total">
            <span>TOTAL À PAYER :</span>
            <span>{{ number_format($totalAmount, 0, ',', ' ') }} CFA</span>
        </div>
    </div>

    <div class="footer">
        <p>Merci pour votre confiance. Votre paiement est attendu à la livraison.</p>
        <p>Nova Tech Bénin | Tél : +229 12 34 56 78 | Email : contact@novatechbenin.com</p>
    </div>
</div>

</body>
</html>
