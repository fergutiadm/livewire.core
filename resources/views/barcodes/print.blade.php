<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Imprimir etiquetas</title>

<style>
.label {
    width: 58mm;
    height: 40mm;
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin: 0;
    page-break-after: always;
}

img {
    width: 52mm;
    height: 16mm;
    object-fit: contain;
}

.code {
    font-family: monospace;
}

@page { size: 58mm 40mm; margin: 0; }

@media print {
    .no-print { display:none; }
}
</style>
</head>

<body>

<div class="no-print" style="padding:10px">
    <form>
        <input name="q" value="{{ $q }}" placeholder="Buscar">
        <button>Buscar</button>
    </form>

    <button onclick="window.print()">Imprimir</button>
</div>

@foreach($productos as $p)
<div class="label">
    <div>{{ $p->nombre }}</div>

    <img src="{{ route('barcode.preview', $p->codigo) }}">

    <div class="code">{{ $p->codigo }}</div>
</div>
@endforeach

<div class="no-print">
    {{ $productos->links() }}
</div>

</body>
</html>
