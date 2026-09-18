<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0;
            size: A4;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 13px;
            color: #222;
            line-height: 1.9;
        }
        .membrete {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
        }
        .membrete img {
            width: 100%;
        }
        .contenido {
            position: relative;
            padding: 200px 85px 190px 85px;
            text-align: justify;
        }
        .gestor-nombre {
            margin-bottom: 0;
        }
        .gestor-cargo {
            font-weight: bold;
            margin-bottom: 0;
        }
        .peticion {
            margin-bottom: 25px;
        }
        .certifica {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 35px 0;
        }
        .cuerpo strong {
            font-weight: bold;
        }
        .fecha {
            text-align: right;
            margin-top: 40px;
        }
        .firma {
            margin-top: 110px;
            text-align: center;
        }
        .firma .nombre {
            margin-bottom: 0;
        }
        .firma .cargo {
            font-weight: bold;
        }
        .numero-certificado {
            position: fixed;
            bottom: 60px;
            right: 85px;
            font-size: 8px;
            color: #999;
        }
    </style>
</head>
<body>

    <div class="membrete">
        <img src="{{ public_path('images/membrete-istam.png') }}">
    </div>

    <div class="contenido">
        <p class="gestor-nombre">{{ $gestorNombre }}</p>
        <p class="gestor-cargo">GESTOR DE VINCULACIÓN CON LA SOCIEDAD DEL INSTITUTO SUPERIOR TECNOLÓGICO AMAZÓNICO</p>
        <p class="peticion">A petición escrita de parte interesado(a).-</p>

        <div class="certifica">CERTIFICA</div>

        <p class="cuerpo">
            Que el(la) Sr(ta). <strong>{{ strtoupper($estudiante->name) }}</strong>, con cédula de identidad
            <strong>{{ $estudiante->cedula }}</strong>, ha participado en el Proyecto de Vinculación con la
            Sociedad denominado <strong>{{ $proyecto->nombre }}</strong>, en la cual ha cumplido un total de
            <strong>{{ $horas }} horas</strong> de <strong>Vinculación con la Sociedad</strong>, según consta
            en el sistema académico de la Institución.
        </p>

        <p class="fecha">Yantzaza, {{ $fecha->translatedFormat('d \\d\\e F \\d\\e\\l Y') }}</p>

        <div class="firma">
            <p class="nombre">{{ $gestorNombre }}</p>
            <p class="cargo">GESTOR DE VINCULACIÓN DEL ISTAM</p>
        </div>
    </div>

    <div class="numero-certificado">N° {{ $numeroCertificado }}</div>

</body>
</html>