<?php
session_start();
include_once '../includes/config.php';

// Obtén el id_horario de la URL
$id_horario = $_GET['id'];

// Realiza una consulta para obtener la información de los asientos disponibles
$conexion = ConnectDatabase::conectar();

$sql = "
    SELECT a.asiento_id, a.numero_fila, a.numero_columna, a.estado_asiento
    FROM horarios h
    JOIN salas s ON h.sala_id = s.sala_id
    JOIN asientos a ON s.sala_id = a.sala_id
    WHERE h.horario_id = :id_horario
";

$stmt = $conexion->prepare($sql);
$stmt->bindParam(':id_horario', $id_horario, PDO::PARAM_INT);
$stmt->execute();

// Almacena los asientos en un array asociativo para facilitar la manipulación
$asientos = [];
$idButacas = [];
while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $asientos[$fila['numero_fila']][$fila['numero_columna']] = $fila['estado_asiento'];
    $idButacas[$fila['numero_fila']][$fila['numero_columna']] = $fila['asiento_id'];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Magic Cinema - Butacas</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600%7CUbuntu:300,400,500,700" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/bootstrap-reboot.min.css">
    <link rel="stylesheet" href="../assets/css/bootstrap-grid.min.css">
    <link rel="stylesheet" href="../assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="../assets/css/jquery.mCustomScrollbar.min.css">
    <link rel="stylesheet" href="../assets/css/nouislider.min.css">
    <link rel="stylesheet" href="../assets/css/ionicons.min.css">
    <link rel="stylesheet" href="../assets/css/plyr.css">
    <link rel="stylesheet" href="../assets/css/photoswipe.css">
    <link rel="stylesheet" href="../assets/css/default-skin.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css" />
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/semantic.min.css" />
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css" />
    <link rel="icon" type="image/png" href="../assets/icon/icono.png" sizes="32x32">
    <style>
        .seating-plan {
            display: grid;
            grid-template-columns: repeat(<?php echo count($asientos[1]); ?>, 40px);
            gap: 5px;
        }

        .occupied {
            color: #808080;
        }

        .available {
            color: #1d8f03;
        }

        .selected {
            color: #9cf7a7;
        }

        .enlarge-icon {
            font-size: 20px;
        }
    </style>
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <?php
    include_once "../includes/Navbar.php";

    if (isset($_SESSION["email"])) {
        Navbar::renderAuthenticatedNavbar($_SESSION["email"]);
    } else {
        Navbar::renderUnauthenticatedNavbar();
    }
    ?>
    <section class="home">
        <div class="owl-carousel home__bg">
            <div class="item home__cover" data-bg="../assets/img/home/home__bg3.jpg"></div>
            <div class="item home__cover" data-bg="../assets/img/home/home__bg4.jpg"></div>
        </div>
    </section>
    <section class="content">
        <div class="content__head">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h2 class="content__title">Elige tu asiento</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <?php
            $sql = "SELECT h.fecha, s.nombre AS sala_nombre, p.titulo AS nombre_pelicula
FROM horarios h
INNER JOIN salas s ON h.sala_id = s.sala_id
INNER JOIN peliculas p ON h.pelicula_id = p.pelicula_id
WHERE h.horario_id = :id";



            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':id', $id_horario, PDO::PARAM_INT);
            $stmt->execute();

            while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<div>';
                echo '<h2 style="color: #fff; font-family: \'Open Sans\', sans-serif;">Detalles de la Reserva</h2>';
                echo '<p style="color: #fff; font-family: \'Open Sans\', sans-serif;"><strong>Nombre de la Película:</strong> ' . $fila['nombre_pelicula']  . '</p>';
                echo '<p style="color: #fff; font-family: \'Open Sans\', sans-serif;"><strong>Sala:</strong> ' . $fila['sala_nombre'] . '</p>';
                $fechaFormateada = date('d-m-Y H:i', strtotime($fila['fecha']));
                echo '<p style="color: #fff; font-family: \'Open Sans\', sans-serif;"><strong>Fecha y Hora:</strong> ' . $fechaFormateada . '</p>';
                echo '</div>';
            }
            ?>

            <div class="row">
                <div class="col-12 text-center">
                    <svg width="300" height="100" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 100">
                        <polygon points="10,10 290,10 250,90 50,90" style="fill: #3b475a;" />
                        <text x="150" y="50" text-anchor="middle" fill="#fff" font-size="12" font-family="Arial">Pantalla</text>
                    </svg>
                </div>
                <div class="col-12 text-center mt-3 d-flex justify-content-center">
                    <div class="seating-plan" id="seating-plan">
                        <?php
                        foreach ($asientos as $fila => $columnas) {
                            foreach ($columnas as $columna => $estado) {
                                $clase_asiento = 'seat';
                                if ($estado == 'Disponible') {
                                    $clase_asiento .= ' available';
                                } elseif ($estado == 'Seleccionado') {
                                    $clase_asiento .= ' selected';
                                } elseif ($estado == 'Ocupado') {
                                    $clase_asiento .= ' occupied';
                                }
                                $id = $idButacas[$fila][$columna];
                                echo '<i class="fa-solid fa-chair ' . $clase_asiento . ' enlarge-icon p-2" data-id="' . $id . '" onclick="seleccionarButaca(this, ' . $fila . ', ' . $columna . ', \'' . $estado . '\')"></i>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="col-12 text-center mt-3">
                    <div style="color: #fff; font-family: 'Open Sans', sans-serif;" id="info-butacas-seleccionadas"></div>
                    <div id="comprar-entrada" style="display: none;">
                        <a id="enlace-comprar-entrada" href="tipoEntrada.php" class="btn btn-primary m-3" style="font-family: 'Open Sans', sans-serif; background: linear-gradient(90deg, #ff55a5 0%, #ff5860 100%); border: none; color: #fff; padding: 10px 20px; border-radius: 5px;">Comprar Entrada</a>
                    </div>
                    <p style="color: red; font-family: 'Open Sans', sans-serif;" id="error-message"></p>
                </div>
            </div>
        </div>
    </section>
    <script>
        var butacasSeleccionadas = [];

        function seleccionarButaca(elemento, fila, columna, estado) {
            var mensajeErrorElemento = document.getElementById('mensaje-error');
            var id = elemento.dataset.id;

            if (estado !== 'Disponible') {
                mensajeErrorElemento.innerText = 'Este asiento está ocupado.';
                return;
            }

            var butaca = {
                fila: fila,
                columna: columna,
                id: id // Incluir el ID en el objeto de butaca
            };

            // Verificar si la butaca ya está seleccionada
            var indice = butacasSeleccionadas.findIndex(function(e) {
                return e.fila === fila && e.columna === columna;
            });

            if (indice !== -1) {
                // Deseleccionar la butaca si ya está seleccionada
                butacasSeleccionadas.splice(indice, 1);
                elemento.classList.remove('selected');
            } else {
                // Seleccionar la butaca
                if (butacasSeleccionadas.length < 8) {
                    butacasSeleccionadas.push(butaca);
                    elemento.classList.add('selected');
                } else {
                    mensajeErrorElemento.innerText = 'No puedes seleccionar más de 8 butacas.';
                }
            }

            // Limpiar el mensaje de error después de un tiempo
            setTimeout(function() {
                mensajeErrorElemento.innerText = '';
            }, 30000);

            // Mostrar información de butacas seleccionadas
            actualizarInfoButacas();
        }

        function actualizarInfoButacas() {
            var infoButacas = 'Butacas seleccionadas: ';
            var idsButacas = []; // Crear un array para almacenar los IDs de las butacas seleccionadas

            for (var i = 0; i < butacasSeleccionadas.length; i++) {
                infoButacas += butacasSeleccionadas[i].fila + '-' + butacasSeleccionadas[i].columna + ', ';

                // Obtener el ID de cada butaca seleccionada y agregarlo al array
                idsButacas.push(butacasSeleccionadas[i].id);
            }

            // Mostrar información de butacas seleccionadas
            document.getElementById('info-butacas-seleccionadas').innerText = infoButacas;

            // Mostrar el mensaje "Comprar Entrada" al lado de las butacas seleccionadas
            var comprarEntradaElement = document.getElementById('comprar-entrada');
            if (butacasSeleccionadas.length > 0) {
                comprarEntradaElement.style.display = 'block';

                // Modificar el enlace para incluir los parámetros
                var enlaceComprarEntrada = document.getElementById('enlace-comprar-entrada');
                enlaceComprarEntrada.href = 'tipoEntrada.php?butacas=' + butacasSeleccionadas.length + '&id=' + idsButacas.join(',') + '&idHorario=<?php echo $id_horario ?>';
            } else {
                comprarEntradaElement.style.display = 'none';
            }
        }
    </script>
</body>

</html>