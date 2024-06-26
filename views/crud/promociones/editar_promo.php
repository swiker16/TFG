<?php
include_once '../../../includes/config.php';
$pdo = ConnectDatabase::conectar();

$id = $titulo = $descripcion = $fecha = $imagen = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {

    $id = $_GET['id'];

    $statement = $pdo->prepare("SELECT * FROM promociones WHERE promocion_id = ?");
    $statement->execute([$id]);
    $promociones = $statement->fetch(PDO::FETCH_ASSOC);

    $titulo = $promociones['titulo'];
    $descripcion = $promociones['descripcion'];
    $clasificacion = $promociones['clasificacion'];
    $fecha = $promociones['fecha'];
    $imagen = base64_encode($promociones['imagen']);

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

<link rel="icon" type="image/png" href="../../../assets/icon/icono.png" sizes="32x32">


<meta name="description" content="">
<meta name="keywords" content="">
<title>Magic Cinema - Editar promociones</title>

</head>
    <body>
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
                                    <a href="administrador_promo.php" class="header__nav-link">Promociones</a>
                                </li>

                                <li class="header__nav-item">
                                    <a href="" class="header__nav-link">Horarios</a>
                                </li>

                                <li class="header__nav-item">
                                    <a href="../bar/administrador_bar.php" class="header__nav-link">Bar</a>
                                </li>

                                <a href="administrador_promo.php" class="header__sign-in">
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
    
    <div class="container mt-5 text-white">
        <h2 class="mt-5">Editar Promociones</h2>
        <form action="update_promo.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <div class="form-row">
                <div class="form-group mb-3 mt-5">
                    <label for="titulo">Título:</label>
                    <input type="text" class="form-control" name="titulo" value="<?php echo $titulo; ?>" required>
                </div>

                <div class="form-group mb-3">
                    <label for="descripcion">Descripción:</label>
                    <textarea class="form-control" name="descripcion" required rows="5"><?php echo $descripcion; ?></textarea>
                </div>

                <div class="form-group mb-3">
                    <label for="fecha">Fecha:</label>
                    <input type="date" class="form-control" name="fecha" value="<?php echo $fecha; ?>">
                </div>

                <div class="form-group mb-3">
                    <label for="imagen">Imagen:</label>
                    <input type="file" class="form-control" name="imagen" accept="image/*">
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3 mb-3">Guardar Cambios</button>
        </form>
    </div>

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

<?php
} else {

    header('Location: administrador_promo.php');
    exit();
}
?>
