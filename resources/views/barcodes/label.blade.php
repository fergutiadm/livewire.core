<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Etiqueta {{ $code }}</title>

<style>
@page { size: 58mm 40mm; margin: 0; }

body {
    margin: 0;
    display: flex;
    justify-content: center;
}

.label {
    width: 58mm;
    height: 40mm;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.name {
    font-size: 10pt;
    text-align: center;
}

.code {
    font-family: monospace;
    font-size: 11pt;
}

img {
    width: 52mm;
    height: 16mm;
    object-fit: contain;
}

@media print {
    button { display: none; }
}
</style>
</head>

<body>

<button onclick="window.print()" style="position:fixed;top:8px;right:8px">
Imprimir
</button>

<div class="label">

@if($name)
<div class="name">{{ $name }}</div>
@endif

<img src="{{ route('barcode.preview', $code) }}" alt="barcode">

<div class="code">{{ $code }}</div>

</div>

<script>
window.addEventListener('load', () =>
    setTimeout(() => window.print(), 150)
);
</script>

</body>
</html>
