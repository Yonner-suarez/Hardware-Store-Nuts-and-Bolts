<?php
require_once 'config.php'; 
require_once 'Middleware.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

    try {
        
        $decoded = Middleware::validateToken($token);
        $userId = $decoded->sub;

        
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

        
        $productos = json_decode($_POST['productos'], true); 

        
        if (count($productos) < 5) {
            Middleware::jsonMiddleware(['error' => 'Se deben cotizar al menos 5 productos'], 400);
        }

        $totalCompra = 0;

        
        foreach ($productos as $producto) {
            $productId = $producto['id'];
            $cantidad = $producto['cantidad'];

           
            $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $stmt->bind_result($price);
            $stmt->fetch();
            $stmt->close();

            
            $totalCompra += $price * $cantidad;
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

        
        if ($totalCompra > 100000) {
            $descuento += 0.02; 
        }

        
        $totalConDescuento = $totalCompra * (1 - $descuento);

        
        foreach ($productos as $producto) {
            $productId = $producto['id'];
            $cantidad = $producto['cantidad'];

            $stmt = $conn->prepare("INSERT INTO quotations (user_id, product_id, quantity, total) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiid", $userId, $productId, $cantidad, $totalConDescuento);
            $stmt->execute();
            $stmt->close();
        }

        Middleware::jsonMiddleware(['total' => $totalConDescuento]);
    } catch (UnauthorizedResponse $e) {
        Middleware::jsonMiddleware(['error' => $e->getMessage()], 401);
    } catch (Exception $e) {
        Middleware::jsonMiddleware(['error' => 'Error: ' . $e->getMessage()], 500);
    } finally {
        
        if (isset($conn)) {
            $conn->close();
        }
    }
}
?>
