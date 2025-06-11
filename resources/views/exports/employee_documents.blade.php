<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Documenti</title>
</head>
<body>
    <h2>Documenti di {{ $employee->name }}</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Documento</th>
                <th>Data Scadenza</th>
            </tr>
        </thead>
        <tbody>
            @foreach($documents as $doc)
                <tr>
                    <td>{{ $doc->document->name }}</td>
                    <td>{{ $doc->expiration_date }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>