<!DOCTYPE html>
<html>
<head>
    <title>Mostrar Response</title>
</head>
<body>
    <h1>Contingut del Response</h1>

    @if (is_array($data) || is_object($data))
        <ul>
            @foreach ($data as $key => $value)
                <li><strong>{{ $key }}:</strong> {{ is_array($value) || is_object($value) ? json_encode($value) : $value }}</li>
            @endforeach
        </ul>
    @else
        <p>No hi ha dades disponibles.</p>
    @endif

</body>
</html>
