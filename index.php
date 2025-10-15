<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convertitore di Temperatura</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h1 {
            color: #333;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .result {
            margin-top: 30px;
            padding: 20px;
            background-color: #e8f5e9;
            border-left: 4px solid #4CAF50;
            border-radius: 4px;
            display: none;
        }

        .result.show {
            display: block;
        }

        .result-text {
            font-size: 18px;
            color: #2e7d32;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Convertitore di Temperatura</h1>
        
        <form method="POST">
            <div class="form-group">
                <label for="temperatura">Temperatura:</label>
                <input type="number" id="temperatura" name="temperatura" step="0.01" placeholder="Inserisci il valore" required>
            </div>

            <div class="form-group">
                <label for="da">Da:</label>
                <select id="da" name="da" required>
                    <option value="">Seleziona scala di partenza</option>
                    <option value="celsius">Celsius (°C)</option>
                    <option value="fahrenheit">Fahrenheit (°F)</option>
                    <option value="kelvin">Kelvin (K)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="a">A:</label>
                <select id="a" name="a" required>
                    <option value="">Seleziona scala di arrivo</option>
                    <option value="celsius">Celsius (°C)</option>
                    <option value="fahrenheit">Fahrenheit (°F)</option>
                    <option value="kelvin">Kelvin (K)</option>
                </select>
            </div>

            <button type="submit">Converti</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $temperatura = floatval($_POST['temperatura']);
            $da = $_POST['da'];
            $a = $_POST['a'];

            function convertiTemperatura($valore, $da, $a) {
                // converte tutto in centigradi
                if ($da === 'fahrenheit') {
                    $celsius = ($valore - 32) * 5 / 9;
                } elseif ($da === 'kelvin') {
                    $celsius = $valore - 273.15;
                } else {
                    $celsius = $valore;
                }

                // poi converte alla scala desiderata)
                if ($a === 'fahrenheit') {
                    return $celsius * 9 / 5 + 32;
                } elseif ($a === 'kelvin') {
                    return $celsius + 273.15;
                } else {
                    return $celsius;
                }
            }

            $scaleLabels = [
                'celsius' => '°C',
                'fahrenheit' => '°F',
                'kelvin' => 'K'
            ];

            $risultato = convertiTemperatura($temperatura, $da, $a);
            $risultato = round($risultato, 2);

            date_default_timezone_set('Europe/Rome');
            $logPath = __DIR__ . '/data/log.txt';

            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $timestamp = date('Y/m/d - H:i:s');  // formato: YYYY/MM/DD - HH:MM:SS


            $content = "[" . $timestamp . "]" . " - " . $ip . " - " . $da . " " . $a . " - " . $temperatura . " " . $scaleLabels[$da] . " = " . $risultato . " " . $scaleLabels[$a] . PHP_EOL;
            
            $fh = fopen($logPath, 'a');
            fwrite($fh, $content);
            fclose($fh);

            $handler = fopen("data/log.txt", "r");
            $content = fread($handler, filesize("data/log.txt"));
            echo '<div class="result show">';
            echo '<div class="result-text">';
            echo  nl2br(nl2br($content));
            echo '</div>';
            echo '</div>';
            fclose($handler);
        }
        ?>
    </div>
</body>
</html>