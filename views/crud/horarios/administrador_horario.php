<?php
include_once '../../../includes/config.php';
$pdo = ConnectDatabase::conectar();

// Obtener los resultados de la tabla horarios
$statement = $pdo->prepare("SELECT * FROM horarios");
$statement->execute();
$resultados = $statement->fetchAll(PDO::FETCH_ASSOC);

// Obtener el total de asientos por sala
$statementAsientos = $pdo->prepare("
    SELECT s.sala_id, s.nombre, s.capacidad, COUNT(a.asiento_id) AS Total_Asientos
    FROM salas s
    LEFT JOIN asientos a ON s.sala_id = a.sala_id
    GROUP BY s.Sala_ID
");
$statementAsientos->execute();
$totalAsientosPorSala = $statementAsientos->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- Font -->
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600%7CUbuntu:300,400,500,700" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

<!-- CSS -->
<link rel="stylesheet" href="../../../assets/css/bootstrap-reboot.min.css">
<link rel="stylesheet" href="../../../assets/css/bootstrap-grid.min.css">
<link rel="stylesheet" href="../../../assets/css/owl.carousel.min.css">
<link rel="stylesheet" href="../../../assets/css/jquery.mCustomScrollbar.min.css">
<link rel="stylesheet" href="../../../assets/css/nouislider.min.css">
<link rel="stylesheet" href="../../../assets/css/ionicons.min.css">
<link rel="stylesheet" href="../../../assets/css/plyr.css">
<link rel="stylesheet" href="../../../assets/css/photoswipe.css">
<link rel="stylesheet" href="../../../assets/css/default-skin.css">
<link rel="stylesheet" href="../../../assets/css/main.css">

<!-- Favicons -->
<link rel="icon" type="image/png" href="../icon/favicon-32x32.png" sizes="32x32">
<link rel="apple-touch-icon" href="../icon/favicon-32x32.png">
<link rel="apple-touch-icon" sizes="72x72" href="../icon/apple-touch-icon-72x72.png">
<link rel="apple-touch-icon" sizes="114x114" href="../icon/apple-touch-icon-114x114.png">
<link rel="apple-touch-icon" sizes="144x144" href="../icon/apple-touch-icon-144x144.png">

<meta name="description" content="">
<meta name="keywords" content="">
<title>Magic Cinema - Administrador</title>

</head>

<body class="body">

    <header class="header">
        <div class="header__wrap">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="header__content">
                            <a href="../../../index.php" class="header__logo">
                                <img src="../../../assets/img/Magic_Cinema-removebg-preview.png" alt="">
                            </a>

                            <ul class="header__nav">
                                <li class="header__nav-item">
                                    <a href="../peliculas/administrador_pelicula.php" class="header__nav-link">Peliculas</a>
                                </li>

                                <li class="header__nav-item">
                                    <a href="../promociones/administrador_promo.php" class="header__nav-link">Promociones</a>
                                </li>

                                <li class="header__nav-item">
                                    <a href="administrador_horario.php" class="header__nav-link">Horarios</a>
                                </li>

                                <li class="header__nav-item">
                                    <a href="../bar/administrador_bar.php" class="header__nav-link">Bar</a>
                                </li>

                                <a href="../../../index.php" class="header__sign-in">
                                    <i class="icon ion-ios-log-in"></i>
                                    <span>Volver</span>
                                </a>
                            </ul>

                            <button class="header__btn" type="button">
                                <span></span>
                                <span></span>
                                <span></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

		<section class="section section--first section--bg">
			<div class="owl-carousel home__bg">
				<div class="item home__cover" data-bg="../../../assets/img/home/home__bg.jpg"></div>
				<div class="item home__cover" data-bg="../../../assets/img/home/home__bg2.jpg"></div>
				<div class="item home__cover" data-bg="../../../assets/img/home/home__bg3.jpg"></div>
				<div class="item home__cover" data-bg="../../../assets/img/home/home__bg4.jpg"></div>
			</div>
		</section>

    <div class="contain mt-5 mx-5">
    <a href="form_añadir_horario.php?id=' . $horario['Horario_ID'] . '" class="btn btn-primary btn-sm mt-3">Añadir nuevo Horario</a>
    <table class="table table-striped table-bordered mt-4 w-100">
        <thead class="thead-dark">
            <tr>
                <th>Horario</th>
                <th>Sala</th>  
                <th>Pelicula</th>  
                <th>Fecha</th>
                <th>Asientos</th>
                <th>Acciones</th>

            </tr>
        </thead>
            <tbody>
                <?php
                foreach ($resultados as $horario) {
                    echo '<tr>';
                    echo '<td>' . $horario['Horario_ID'] . '</td>';
                    echo '<td>' . $horario['Sala_ID'] . '</td>';
                    echo '<td>' . $horario['Pelicula_ID'] . '</td>';
                    echo '<td>' . $horario['Fecha_hora_inicio'] . '</td>';

                    // Buscar el total de asientos por sala
                    $totalAsientos = 0;
                    foreach ($totalAsientosPorSala as $asientos) {
                        if ($asientos['Sala_ID'] == $horario['Sala_ID']) {
                            $totalAsientos = $asientos['Total_Asientos'];
                            break;
                        }
                    }

                    echo '<td>' . $totalAsientos . '</td>';
                    echo '<td>
                            <a href="delete_horario.php?id=' . $horario['Horario_ID'] . '" class="btn btn-danger btn-sm mt-3">Eliminar</a>
                          </td>';
                    echo '</tr>';
                }
                                            //<a href="editar_horario.php?id=' . $horario['Horario_ID'] . '" class="btn btn-warning btn-sm mt-3">Editar</a>

                ?>
                
            </tbody>
    </table>
    </div>

    <footer class=" footer">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-6 col-sm-4 col-md-3">
                    <h6 class="footer__title">Sobre nosotros</h6>
                    <ul class="footer__list">
                        <li><a href="../../html/QuienesSomos.html">Quienés somos</a></li>
                        <li><a href="#">Trabaja con nosotros</a></li>
                    </ul>
                </div>

                <div class="col-6 col-sm-4 col-md-3">
                    <h6 class="footer__title">Legal</h6>
                    <ul class="footer__list">
                        <li><a href="../../html/AvisLegal.html">Aviso Legal</a></li>
                        <li><a href="../../html/CondicionesCompra.html">Condiciones de compra</a></li>
                        <li><a href="../../html/politicas.html">Políticas de privacidad</a></li>
                    </ul>
                </div>

                <div class="col-12 col-sm-4 col-md-3">
                    <h6 class="footer__title">Contacto</h6>
                    <ul class="footer__list">
                        <li><a href="tel:+34624233403">+34 624 23 34 03</a></li>
                        <li><a href="mailto:atencionalclient@cinemmagic.com">atencionalclient@magiccinema.com</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!-- JS -->
    <script src="../../../assets/js/jquery-3.3.1.min.js"></script>
    <script src="../../../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../../../assets/js/owl.carousel.min.js"></script>
    <script src="../../../assets/assets/js/jquery.mousewheel.min.js"></script>
    <script src="../../../assets/js/jquery.mCustomScrollbar.min.js"></script>
    <script src="../../../assets/js/wNumb.js"></script>
    <script src="../../../assets/js/nouislider.min.js"></script>
    <script src="../../../assets/js/plyr.min.js"></script>
    <script src="../../../assets/js/jquery.morelines.min.js"></script>
    <script src="../../../assets/js/photoswipe.min.js"></script>
    <script src="../../../assets/js/photoswipe-ui-default.min.js"></script>
    <script src="../../../assets/js/main.js"></script>

</body>

</html>