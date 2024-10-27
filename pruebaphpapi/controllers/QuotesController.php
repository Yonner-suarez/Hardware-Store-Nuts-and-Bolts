<?php
require_once(__DIR__ . "/../utils/Variable.php");
require_once(__DIR__ . "/../infraestructure/middleware.php");

class QuotesController
{
      public function Quote()
      {
        $headers = getallheaders();

        // Verificar si el encabezado de autorización está presente
        $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

        if (is_null($token)) {
            throw new UnauthorizedResponse('Token de autorización no proporcionado.');
        }
        $total_cotizacion = 0;
        try{
      $method_request = $_SERVER['REQUEST_METHOD'];
        if($method_request == "POST")
        {
          global $host, $dbUser, $dbPassword, $dbName, $dbPort;
          $conn = new mysqli($host, $dbUser, $dbPassword, $dbName, $dbPort);

          if ($conn->connect_error) {
            Middleware::jsonMiddleware(['error' => 'Error de conexión a la base de datos: ' . $conn->connect_error], 500);
        }

        $data = json_decode(file_get_contents('php://input'), true);

        // Verificamos que el array de productos no esté vacío
        if (empty($data['productos']) || !isset($data['idusuario'])) {
          throw new BadRequestResponse('Debes ingresar al menos un producto');
        }
      // Obtener el id_quote más alto para el user_id actual
      $queryMaxId = "SELECT MAX(id_quote) AS max_id_quote FROM quotations WHERE user_id = ?";
      $stmt = $conn->prepare($queryMaxId);
      $stmt->bind_param('i', $data['idusuario']);
      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result->fetch_assoc();

      // Si no hay resultados (es la primera cotización para ese usuario), comienza en 1, si no, suma 1 al valor obtenido
      $id_quote = $row['max_id_quote'] ? $row['max_id_quote'] + 1 : 1;


      // Inicializar el array de valores
      $values = [];

      // Suponiendo que $data y $productos ya están definidos y contienen los datos necesarios
      foreach ($data['productos'] as $producto) {
          if (isset($producto['idproducto']) && isset($producto['total'])) {
              // Escapamos los valores para prevenir inyecciones SQL
              $idUsuario = $conn->real_escape_string($data['idusuario']);
              $idProducto = $conn->real_escape_string($producto['idproducto']);
              $total = $conn->real_escape_string(number_format($producto['total'], 2, '.', '')); // Formateamos el total
              $cantidad = $conn->real_escape_string($producto['cantidad']);

              // Asegúrate de que el id_quote se incluya aquí
              $values[] = "($id_quote, {$idUsuario}, {$idProducto}, {$total}, {$cantidad}, NOW())"; // Usamos NOW() para el timestamp

              $total_producto = $total * $cantidad;
              $total_cotizacion += $total_producto;
          }
      }

      // Verificamos que tengamos valores para insertar
      if (count($values) > 0) {
          $query = "INSERT INTO quotations (id_quote, user_id, product_id, total, cantidad, created_at) VALUES " . implode(', ', $values);

          // Ejecutar la consulta y verificar si tuvo éxito
          if ($conn->query($query) === TRUE) {
              return ["total" => $total_cotizacion, "id_quote" => $id_quote, "message" => "Cotizacion creada correctamente"];
          } else {
              throw new InternalServerErrorResponse('Error en la inserción: ' . $conn->error);
          }
      } else {
          throw new BadRequestResponse('No hay productos para insertar');
      }

      $conn->close();
        
      }
      else
      {
        throw new BadRequestResponse('Método no permitido');
      }
        }
        catch (Exception $e)
        {
          throw $e;
        }
        
    }
     public function ToolsSaleDiscount()
    {
      global $host, $dbUser, $dbPassword, $dbName, $dbPort;
      try {
         $headers = getallheaders();

        // Verificar si el encabezado de autorización está presente
        $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

        if (is_null($token)) {
            throw new UnauthorizedResponse('Token de autorización no proporcionado.');
        }

        $decoded = Middleware::validateToken($token);
        $userId = $decoded->aud;

        
        $conn = new mysqli($host, $dbUser, $dbPassword, $dbName, $dbPort);

        
        if ($conn->connect_error) {
            Middleware::jsonMiddleware(['error' => 'Error de conexión a la base de datos: ' . $conn->connect_error], 500);
        }

        
        $stmt = $conn->prepare("SELECT tipo_persona, created_at FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($tipoCliente, $createdAt);
        $stmt->fetch();
        $stmt->close();

        
        $fechaRegistro = new DateTime($createdAt);
        $fechaActual = new DateTime();
        $diferencia = $fechaRegistro->diff($fechaActual);
        $mesesRegistro = $diferencia->m + ($diferencia->y * 12);

        
        if ($mesesRegistro >= 12) {
            $tipoCliente = 'Permanente'; 
        } elseif ($mesesRegistro >= 6) {
            $tipoCliente = 'Periódico'; 
        } elseif ($mesesRegistro >= 1) {
            $tipoCliente = 'Casual'; 
        } else {
            $tipoCliente = 'Nuevo'; 
        }

        $descuento = 0;

        switch ($tipoCliente) {
            case 'Permanente':
                $descuento = 0.10; 
                break;
            case 'Periódico':
                $descuento = 0.05; 
                break;
            case 'Casual':
                $descuento = 0.02; 
                break;
            case 'Nuevo':
                $descuento = 0.00; 
                break;
            default:
                break;
        }

        return $descuento;

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}