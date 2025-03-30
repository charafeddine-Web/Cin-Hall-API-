<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Réservation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
            text-align: left;
        }
        .ticket {
            border: 2px solid #333;
            padding: 25px;
            width: 70%;
            margin: 30px auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .ticket h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            color: #333;
        }
        .ticket .details {
            font-size: 1.2em;
            margin-bottom: 20px;
            color: #555;
        }
        .ticket .footer {
            font-size: 1.1em;
            color: #777;
            margin-top: 20px;
        }
        .ticket .details strong {
            font-weight: bold;
        }
        .ticket .icon {
            font-size: 60px;
            color: #f5a623;
            margin-bottom: 20px;
        }
        .ticket .qrcode {
            margin-top: 20px;
            text-align: left;
        }
        .ticket .qrcode img {
            width: 140px;
            height: 140px;
        }
    </style>
</head>
<body>
<div class="ticket">
    <div class="icon">
        <i class="fas fa-film"></i> <!-- Icone de cinéma -->
    </div>
    <h1>Ticket de Réservation</h1>
    <p class="details">
        <strong>Client :</strong> {{ $customer_name }}<br>
        <strong>Film :</strong> {{ $Film }}<br>
        <strong>Séance :</strong> {{ $Seance }}<br>
        <strong>Siège :</strong> {{ $Siege }}<br>
        <strong>Prix :</strong> {{ $Prix }}<br>
{{--        <strong>Code de Réservation :</strong> {{ $reservation_code }}<br>--}}
    </p>
    <div class="footer">
        Merci d'avoir réservé chez nous !<br>
        Veuillez conserver ce ticket pour l'entrée.
    </div>
    <div class="qrcode">
{{--        <img src="data:image/png;base64,{{ $qr_code }}" alt="QR Code">--}}
    </div>
</div>

<!-- Importation de l'icône FontAwesome -->
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>
