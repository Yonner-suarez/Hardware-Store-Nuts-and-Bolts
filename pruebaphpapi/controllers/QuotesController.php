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
        Middleware::validateToken($token);
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

                  $id_bag_quote = $this->addToBag($id_quote, $data['idusuario']);
                  return [
                              'total' => $total_cotizacion,
                              'quote' => $this->getBag($id_bag_quote, $data['idusuario'], 1),
                              'message' => 'Cotización creada correctamente'
                          ];

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

    public function TrashProduct()
      {

        
        $headers = getallheaders();

        // Verificar si el encabezado de autorización está presente
        $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

        if (is_null($token)) {
            throw new UnauthorizedResponse('Token de autorización no proporcionado.');
        }
        Middleware::validateToken($token);

        $userId = Middleware::validateToken($token)->aud;
        try{
            $method_request = $_SERVER['REQUEST_METHOD'];
              if($method_request == "PUT")
              {
                    global $host, $dbUser, $dbPassword, $dbName, $dbPort;
                    $conn = new mysqli($host, $dbUser, $dbPassword, $dbName, $dbPort);

                    if ($conn->connect_error) {
                      Middleware::jsonMiddleware(['error' => 'Error de conexión a la base de datos: ' . $conn->connect_error], 500);
                  }

                  $data = json_decode(file_get_contents('php://input'), true);

                  // Verificamos que el array de productos no esté vacío
                  $productId = $data['idproducto'];
                  $quoteId = $data['id_quote'];

                  if($quoteId !== null)
                  {
                    $queryEstado = "SELECT estado FROM quotations WHERE user_id = ? AND id_quote = ? AND product_id = ?";
                    $stmt = $conn->prepare($queryEstado);
                    $stmt->bind_param('iii', $userId, $quoteId, $productId);
                    $stmt->execute();
                    $stmt->bind_result($estado);
                    $stmt->fetch();
                    $stmt->close();

                
                  if ($estado == 1) {
                      $query = "UPDATE quotations SET estado = 0 WHERE user_id = ? AND id_quote = ? AND product_id = ?";
                      $stmt = $conn->prepare($query);

                      if (!$stmt) {
                          throw new InternalServerErrorResponse("Error al preparar la consulta de actualización: " . $conn->error);
                      }

                      $stmt->bind_param('iii', $userId, $quoteId, $productId);
                      $stmt->execute();
                  }
                  
                  else {
                    throw new BadRequestResponse('El producto ya se encuentra eliminado');
                  }
                }
                else
                {
                  $queryEstado = "SELECT estado FROM bag WHERE user_id = ? AND product_id = ?";
                    $stmt = $conn->prepare($queryEstado);
                    $stmt->bind_param('ii', $userId, $productId);
                    $stmt->execute();
                    $stmt->bind_result($estado);
                    $stmt->fetch();
                    $stmt->close();

                
                  if ($estado == 1) {
                      $query = "UPDATE bag SET estado = 0 WHERE user_id = ?  AND product_id = ?";
                      $stmt = $conn->prepare($query);

                      if (!$stmt) {
                          throw new InternalServerErrorResponse("Error al preparar la consulta de actualización: " . $conn->error);
                      }

                      $stmt->bind_param('ii', $userId,  $productId);
                      $stmt->execute();
                  }
                  else {
                    throw new BadRequestResponse('El producto ya se encuentra eliminado');
                  }
                // Obtener el id_quote más alto para el user_id actual
                
                }

                return "Producto eliminado correctamente";
                
            }  else
            {
              throw new BadRequestResponse('Método no permitido');
            }

            $conn->close();
          
             
        }
        catch (Exception $e)
        {
          throw $e;
        }
        
    }


     private function getBag($id_bag, $id_user, $quote_id = 0)
      {       
       
        try{
           
            global $host, $dbUser, $dbPassword, $dbName, $dbPort;
            $conn = new mysqli($host, $dbUser, $dbPassword, $dbName, $dbPort);

            if ($conn->connect_error) {
              Middleware::jsonMiddleware(['error' => 'Error de conexión a la base de datos: ' . $conn->connect_error], 500);
              }
             $query = "";
            $results = [];

             if($quote_id == 1)
             {
                $query = "SELECT 
                                u.id as userid,
                                p.id as productid,
                                q.total as total,
                                q.cantidad as cantidad,
                                q.id_quote as quoteid,
                                u.tipo_persona as tipopersona,
                                p.name as productname,
                                p.marca as productmarca,
                                p.code as productcode,
                                p.cantidad as cantidadproduct
                                FROM hardware_nuts_db.bag AS b
                                left join hardware_nuts_db.quotations as q on b.quote_id = q.id_quote
                                LEFT JOIN hardware_nuts_db.users AS u ON u.id = q.user_id
                                LEFT JOIN hardware_nuts_db.products AS p ON p.id = q.product_id 
                                WHERE  b.id = ? AND q.user_id = ? AND q.estado = 1;
                                ";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('ii', $id_bag, $id_user);
                $stmt->execute();
                $stmt->bind_result($userid, $productid, $total, $cantidad, $quoteid, $tipopersona, $productname, $productmarca, $productcode, $cantidadproduct);

                while ($stmt->fetch()) {
                  $results[] = [
                      'userid' => $userid,
                      'productid' => $productid,
                      'total' => $total,
                      'cantidad' => $cantidad,
                      'id_quote' => $quoteid,
                      'tipopersona' => $tipopersona,
                      'productname' => $productname,
                      'productmarca' => $productmarca,
                      'productcode' => $productcode,
                      'cantidadproduct' => $cantidadproduct,
                  ];
                    }
            }
            else
            {
              $query = "SELECT 
                                u.id as userid,
                                p.id as productid,
                                p.price as total,
                                b.cantidad as cantidad,
                                u.tipo_persona as tipopersona,
                                p.name as productname,
                                p.marca as productmarca,
                                p.code as productcode,
                                p.cantidad as cantidadproduct
                                FROM hardware_nuts_db.bag AS b                                
                                LEFT JOIN hardware_nuts_db.users AS u ON u.id = b.user_id
                                LEFT JOIN hardware_nuts_db.products AS p ON p.id = b.product_id 
                                WHERE b.id = ? AND b.user_id = ? AND b.estado = 1;
                              ";
              $stmt = $conn->prepare($query);
              $stmt->bind_param('ii', $id_bag, $id_user);
              $stmt->execute();
              $stmt->bind_result($userid, $productid, $total, $cantidad, $tipopersona, $productname, $productmarca, $productcode, $cantidadproduct);

              while ($stmt->fetch()) {
                $results[] = [
                    'userid' => $userid,
                    'productid' => $productid,
                    'total' => $total,
                    'cantidad' => $cantidad,
                    'id_quote' => 0,
                    'tipopersona' => $tipopersona,
                    'productname' => $productname,
                    'productmarca' => $productmarca,
                    'productcode' => $productcode,
                    'cantidadproduct' => $cantidadproduct,
                ];
            }
            

            
            }

            return $results;

        $conn->close();
             
        }
        catch (Exception $e)
        {
          throw $e;
        }
        
    }

    public function addToBag($id_quote = null, $id_user = null)
    {
        global $host, $dbUser, $dbPassword, $dbName, $dbPort;
        $headers = getallheaders();
      
        $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

        if (is_null($token)) {
            throw new UnauthorizedResponse('Token de autorización no proporcionado.');
        }

        Middleware::validateToken($token);

        try
        {
            if (isset($id_quote) && $id_quote !== null && isset($id_user) && $id_user !== null)
            {
                $conn = new mysqli($host, $dbUser, $dbPassword, $dbName, $dbPort);

                if ($conn->connect_error)
                 {
                  throw new InternalServerErrorResponse('Error de conexión a la base de datos: ' . $conn->connect_error);
                }

                $query = "INSERT INTO bag (quote_id, user_id, created_at) VALUES (?, ?, NOW())";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('ii', $id_quote, $id_user);
                if (!$stmt->execute()) {
                  throw new InternalServerErrorResponse("Error al ejecutar la consulta: " . $stmt->error);
                }
                $inserted_id = $conn->insert_id;

                return $inserted_id;
            }
            else
            {
              if($_SERVER['REQUEST_METHOD'] == "POST")
              {
                $data = json_decode(file_get_contents('php://input'), true);
                if(!isset($data['idproducto']) || !isset($data['idusuario']) || !isset($data["cantidad"]))
                {
                  throw new BadRequestResponse('Debes ingresar información del producto');
                }

                $conn = new mysqli($host, $dbUser, $dbPassword, $dbName, $dbPort);

                if ($conn->connect_error)
                 {
                  throw new InternalServerErrorResponse('Error de conexión a la base de datos: ' . $conn->connect_error);
                }

                $query = "INSERT INTO bag (user_id, product_id, cantidad, created_at) VALUES (?, ?, ?, NOW())";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('iii', $data['idusuario'], $data['idproducto'], $data['cantidad']);
                $stmt->execute();
                $inserted_id = $conn->insert_id;

                return [
                              'total' => 0,
                              'quote' => $this->getBag($inserted_id, $data['idusuario']),
                              'message' => 'Cotización creada correctamente'
                          ];
              }
              else
              {
                throw new BadRequestResponse('Método no permitido');
              }
            }
            
        }
        catch (Exception $e)
        {
          throw $e;
        }

    }


    public function getBagTotal()
    {
      global $host, $dbUser, $dbPassword, $dbName, $dbPort;
      $results = [];

      try{
        if($_SERVER['REQUEST_METHOD'] == "GET"  )
        {

          $headers = getallheaders();

          $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

          Middleware::validateToken($token);

          $decoded = Middleware::validateToken($token);
          $userId = $decoded->aud;

          $conn = new mysqli($host, $dbUser, $dbPassword, $dbName, $dbPort);

          if ($conn->connect_error) {
              throw new InternalServerErrorResponse('Error de conexión a la base de datos: ' . $conn->connect_error);
            }
            
            $query = "SELECT 
                              COALESCE(q.total, p.price * b.cantidad, 0) AS total_cotizacion, 
                              b.id AS bag_id,
                             COALESCE(q.product_id, b.product_id) AS productid,
                              COALESCE(q.cantidad, b.cantidad) AS cantidad,
                              b.user_id,
                              b.estado AS bag_estado,
                              q.id_quote AS quote_id,
                              q.estado AS quote_estado,
                              q.created_at AS quote_created_at,
                              p.name AS productname,
                              p.marca AS productmarca,
                              p.code AS productcode,
                              p.cantidad AS cantidadproduct
                          FROM 
                              bag AS b
                          LEFT JOIN 
                              quotations AS q ON b.quote_id = q.id_quote AND (b.quote_id IS NOT NULL)  and (q.estado = 1)  
                          LEFT JOIN 
                              products AS p ON b.product_id = p.id OR q.product_id = p.id  
                          WHERE 
                              b.user_id = ? AND b.estado = 1; 
                      ";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $userId);

            if(!$stmt->execute())
            {
              throw new InternalServerErrorResponse("Error al ejecutar la consulta: " . $stmt->error);
            }

            $stmt->execute();
            $stmt->bind_result($total_cotizacion, $bag_id, $productid, $cantidad, $user_id, $bag_estado, $quote_id, $quote_estado, $quote_created_at, $productname, $productmarca, $productcode, $cantidadproduct);
            while($stmt->fetch()) {
              $results[] = [
                "userid" => $user_id,
                'productid' => $productid,
                'total' => $total_cotizacion,
                'cantidad' => $cantidad,
                'id_quote' => $quote_id !== null ? $quote_id : "",
                'productname' => $productname,
                'productmarca' => $productmarca,
                'productcode' => $productcode,
                'cantidadproduct' => $cantidadproduct,
              ];
            }
        }
        else
        {
          throw new BadRequestResponse('Método no permitido');
        }

        return $results;
      }
      catch (Exception $e)
      {
        throw $e;
      }
    }

    public function setCantidad()
    {
      global $host, $dbUser, $dbPassword, $dbName, $dbPort;

      try{

        $headers = getallheaders();

        $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

        Middleware::validateToken($token);
        $userId = Middleware::validateToken($token)->aud;

        $conn = new mysqli($host, $dbUser, $dbPassword, $dbName, $dbPort);

        if($conn->connect_error)
        {
          throw new InternalServerErrorResponse('Error de conexión a la base de datos: ' . $conn->connect_error);
        }

        if($_SERVER['REQUEST_METHOD'] == "PUT")
        {

          $data = json_decode(file_get_contents('php://input'), true);

          $id_quote = isset($data['id_quote']) ? $data['id_quote'] : null;
          $id_product = isset($data['idproducto']) ? $data['idproducto'] : null;
          $cantidad = isset($data['cantidad']) ? $data['cantidad'] : null;
            $query="";
          if($cantidad < 0) throw new BadRequestResponse('La cantidad no puede ser 0');
            if($id_quote !== null)
            {
              $query = "UPDATE quotations SET cantidad = ? WHERE id_quote = ? AND product_id = ? AND user_id = ?";
               
                $stmt = $conn->prepare($query);
                $stmt->bind_param('iiii', $cantidad, $id_quote, $id_product, $userId);
                if(!$stmt->execute())
                {
                  throw new InternalServerErrorResponse("Error al ejecutar la consulta: " . $stmt->error);
                }

                $affectedRows = $stmt->affected_rows;

                if ($affectedRows > 0) {
                    return "Producto actualizado correctamente";
                } else {
                    return "No se encotró el producto para actualizar";
                }
            }
            else
            {
              
                $query = "UPDATE bag SET cantidad = ? WHERE product_id = ? AND user_id = ?";
               
              $stmt = $conn->prepare($query);
              $stmt->bind_param('iii', $cantidad, $id_product, $userId);
              if(!$stmt->execute())
              {
                throw new InternalServerErrorResponse("Error al ejecutar la consulta: " . $stmt->error);
              }

              $affectedRows = $stmt->affected_rows;

              if ($affectedRows > 0) {
                  return "Producto actualizado correctamente";
              } else {
                  return "No se encotró el producto para actualizar";
              }
            }
           
        }
        else
        {
          throw new BadRequestResponse('Método no permitido');
        }
      }
      catch(Exception $e)
      {
        throw $e;
      }

    }

     public function TrashQuote()
      {

        
        $headers = getallheaders();

        // Verificar si el encabezado de autorización está presente
        $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

        if (is_null($token)) {
            throw new UnauthorizedResponse('Token de autorización no proporcionado.');
        }
        Middleware::validateToken($token);

        $userId = Middleware::validateToken($token)->aud;
        try{
            $method_request = $_SERVER['REQUEST_METHOD'];
              if($method_request == "PUT")
              {
                    global $host, $dbUser, $dbPassword, $dbName, $dbPort;
                    $conn = new mysqli($host, $dbUser, $dbPassword, $dbName, $dbPort);

                    if ($conn->connect_error) {
                      Middleware::jsonMiddleware(['error' => 'Error de conexión a la base de datos: ' . $conn->connect_error], 500);
                  }

                  $data = json_decode(file_get_contents('php://input'), true);

                  $quoteId = $data['id_quote'];

                  if($quoteId === null)
                  {
                    throw new BadRequestResponse('Debe proporcionar un id de cotización');
                  }
                else
                {
                  $queryEstado = "SELECT estado FROM bag WHERE user_id = ? AND quote_id = ?";
                    $stmt = $conn->prepare($queryEstado);
                    $stmt->bind_param('ii', $userId, $quoteId);
                    $stmt->execute();
                    $stmt->bind_result($estado);
                    $stmt->fetch();
                    $stmt->close();

                
                  if ($estado == 1) {
                      $query = "UPDATE bag SET estado = 0 WHERE user_id = ?  AND quote_id = ?";
                      $stmt = $conn->prepare($query);

                      if (!$stmt) {
                          throw new InternalServerErrorResponse("Error al preparar la consulta de actualización: " . $conn->error);
                      }

                      $stmt->bind_param('ii', $userId,  $quoteId);
                      $stmt->execute();
                  }
                  else {
                    throw new BadRequestResponse('La cotización ya se encuentra eliminada');
                  }
                // Obtener el id_quote más alto para el user_id actual
                
                }

                return "Cotización eliminada correctamente";
                
            }  else
            {
              throw new BadRequestResponse('Método no permitido');
            }

            $conn->close();
          
             
        }
        catch (Exception $e)
        {
          throw $e;
        }
        
    }
}