<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billet Électronique</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .ticket-header {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .ticket-info {
            margin-bottom: 20px;
        }
        .ticket-info p {
            font-size: 18px;
        }
        .qr-code {
            margin-top: 20px;
            text-align: center;
        }
        .qr-code img {
            width: 150px;
            height: 150px;
        }
    </style>
</head>
<body>
<div class="ticket-header">
    <h1>Billet Électronique</h1>
</div>

<div class="ticket-info">
    <p><strong>Film :</strong> {{ $ticketData['event'] }}</p>
    <p><strong>Date :</strong> {{ $ticketData['date'] }}</p>
    <p><strong>Heure :</strong> {{ $ticketData['time'] }}</p>
    <p><strong>Siège :</strong> {{ $ticketData['seat'] }}</p>
    <p><strong>ID Billet :</strong> {{ $ticketData['ticketId'] }}</p>
</div>

<div class="qr-code">
    <img src="data:image/png;base64, {!! base64_encode($qrCode) !!} " alt="QR Code">
</div>
</body>
</html>
