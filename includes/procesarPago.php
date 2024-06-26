<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

class ProcesarPago
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function actualizarButacas($idsButacas)
    {
        $idsButacasArray = explode(',', $idsButacas);

        // Escapar y formatear los IDs para la consulta SQL
        $idsButacasArray = array_map(function ($id) {
            return intval($id);
        }, $idsButacasArray);

        $idsButacasStr = implode(',', $idsButacasArray);
        // Actualizar la tabla de asientos (suponiendo que existe una columna llamada 'estado' que representa si está ocupado o no)
        $sql = "UPDATE asientos SET estado_asiento = 'Ocupado' WHERE asiento_id IN ($idsButacasStr)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
    }

    public function realizarReserva($idsButacas, $usuarioId, $correoUsuario, $horarioId)
    {

        $idsButacasArray = explode(',', $idsButacas);
        foreach ($idsButacasArray as $asientoId) {
            // Obtener información sobre el asiento desde la base de datos (ajusta según tu esquema)
            $infoAsiento = $this->obtenerInfoAsiento($asientoId);
            // Realizar inserción en la tabla de reservas
            $sql = "INSERT INTO reservas (usuario_id, horario_id, asiento_id) VALUES (:usuario_id, :horario_id, :asiento_id)";
            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stmt->bindParam(':horario_id', $horarioId, PDO::PARAM_INT);
            $stmt->bindParam(':asiento_id', $asientoId, PDO::PARAM_INT);
            $stmt->execute();

            ProcesarPago::enviarCorreo($correoUsuario, $infoAsiento);

        }
        
    }

    private function obtenerInfoAsiento($asientoId)
    {
        // Consulta para obtener información del asiento
        $sql = "SELECT 
                    p.titulo AS titulo_pelicula,
                    a.numero_fila,
                    a.numero_columna,
                    s.nombre AS sala_nombre,
                    h.fecha AS horario_fecha
                FROM 
                    asientos a
                LEFT JOIN 
                    reservas r ON a.asiento_id = r.asiento_id
                LEFT JOIN 
                    horarios h ON r.horario_id = h.horario_id
                LEFT JOIN 
                    peliculas p ON h.pelicula_id = p.pelicula_id
                INNER JOIN 
                    salas s ON a.sala_id = s.sala_id
                WHERE 
                    a.asiento_id = :asiento_id";
    
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':asiento_id', $asientoId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

public function enviarCorreo($email, $infoAsiento)
{
    // Configuración de PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.hostinger.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'no-reply@magiccinema.es';
        $mail->Password = 'MagicCinema2023*';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('no-reply@magiccinema.es', 'no-reply@magiccinema.es');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Reserva Confirmada';
        
        $body = "Gracias por tu reserva. Aquí está la información detallada:<br><br>" .
            "Película: {$infoAsiento['titulo_pelicula']}<br>" .
            "Sala: {$infoAsiento['nombre_sala']}<br>" .
            "Asiento: Fila {$infoAsiento['numero_fila']}, Columna {$infoAsiento['numero_columna']}<br>";

        $mail->Body = $body;

        $mail->send();
    } catch (Exception $e) {
        echo "Error al enviar el correo de confirmación: {$mail->ErrorInfo}";
    }
}
}
