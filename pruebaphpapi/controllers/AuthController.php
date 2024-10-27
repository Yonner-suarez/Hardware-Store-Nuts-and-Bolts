<?php
require_once(__DIR__ . "/../infraestructure/middleware.php");
require_once(__DIR__ . "/../config.php");

class AuthController{

  public function login($request)
  {
     global $host, $dbUser, $dbPassword, $dbName, $dbPort;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $data = json_decode(file_get_contents('php://input'), true);

        // Obtener los datos del cuerpo de la solicitud
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null; 


    
    $conn = new mysqli($host, $dbUser, $dbPassword, $dbName, $dbPort);

    
    if ($conn->connect_error) {
        Middleware::jsonMiddleware(['error' => 'Error de conexión a la base de datos: ' . $conn->connect_error], 500);
    }

    
    $stmt = $conn->prepare("SELECT id, password, tipo_persona FROM users WHERE email = ?");
    if (!$stmt) {
        Middleware::jsonMiddleware(['error' => 'Error en la consulta: ' . $conn->error], 500);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($userId, $hashedPassword, $tipoPersona);
    $stmt->fetch();

    
    if ($hashedPassword && password_verify($password, $hashedPassword)) {
      
      $tipoPersonaInt = 0;

      
      if($tipoPersona == "Nuevo"){
        $tipoPersonaInt = 5;
      }
      else if($tipoPersona == "Casual"){
        $tipoPersonaInt = 4;
      }
      else if($tipoPersona == "Periódico"){
        $tipoPersonaInt = 3;
      }
      else if($tipoPersona == "Permanente"){
        $tipoPersonaInt = 2;
      }
      else if($tipoPersona == "Admin"){
        $tipoPersonaInt = 1;
      }

        $token = Middleware::getToken($tipoPersonaInt, $email, $userId);
        return $token;
    } else {
        throw new UnauthorizedResponse('Credenciales inválidas');
    }

    
    $stmt->close();
    $conn->close();
}
  }
  
  public function register(){
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        global $host, $dbUser, $dbPassword, $dbName, $dbPort;

        $data = json_decode(file_get_contents('php://input'), true);

        
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;
        $email = $data['email'] ?? null;
        $nombre = $data['nombre'] ?? null; 
        $apellido = $data['apellido'] ?? null; 
        $numeroDocumento = $data['numeroDocumento'] ?? null; 
        $numeroTelefono = $data['numeroTelefono'] ?? null; 
        $departamento = $data['departamento'] ?? null; 
        $ciudad = $data['ciudad'] ?? null; 
        $codigoPostal = $data['codigoPostal'] ?? null; 
        $tipoDocumento = $data['tipoDocumento'] ?? null; 
        $tipoContribuyente = $data['tipoContribuyente'] ?? null; 

         // Validaciones
        if (empty($username)) {
            throw new BadRequestResponse('El campo "username" es obligatorio.');
        }
        if (empty($password)) {
            throw new BadRequestResponse('El campo "password" es obligatorio.');
        }
        if (empty($email)) {
            throw new BadRequestResponse('El campo "correoElectronico" es obligatorio.');
        }
        if (empty($nombre)) {
            throw new BadRequestResponse('El campo "nombre" es obligatorio.');
        }
        if (empty($apellido)) {
            throw new BadRequestResponse('El campo "apellido" es obligatorio.');
        }
        if (empty($numeroDocumento)) {
            throw new BadRequestResponse('El campo "numeroDocumento" es obligatorio.');
        }
        if (empty($numeroTelefono)) {
            throw new BadRequestResponse('El campo "numeroTelefono" es obligatorio.');
        }
        if (empty($departamento)) {
            throw new BadRequestResponse('El campo "departamento" es obligatorio.');
        }
        if (empty($ciudad)) {
            throw new BadRequestResponse('El campo "ciudad" es obligatorio.');
        }
        if (empty($codigoPostal)) {
            throw new BadRequestResponse('El campo "codigoPostal" es obligatorio.');
        }
        if (empty($tipoDocumento)) {
            throw new BadRequestResponse('El campo "tipoDocumento" es obligatorio.');
        }
        if (empty($tipoContribuyente)) {
            throw new BadRequestResponse('El campo "tipoContribuyente" es obligatorio.');
        }

    $conn = new mysqli($host, $dbUser, $dbPassword, $dbName, $dbPort);

   
    if ($conn->connect_error) {
        Middleware::jsonMiddleware(['error' => 'Error de conexión a la base de datos'], 500);
    }

   
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        throw new BadRequestResponse('El nombre de usuario o el correo ya están en uso');
    } else {
       
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        
        $tipoPersona = 5; 
       // Crear la consulta para insertar el nuevo usuario
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, tipo_persona, nombre, apellido, numeroDocumento, numeroTelefono, departamento, ciudad, codigoPostal, created_at, tipoDocumento, tipoContribuyente) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)");
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hashear la contraseña
        $stmt->bind_param("sssssssssssii", $username, $hashedPassword, $email, $tipoPersona, $nombre, $apellido, $numeroDocumento, $numeroTelefono, $departamento, $ciudad, $codigoPostal, $tipoDocumento, $tipoContribuyente); 

        if ($stmt->execute()) {
            return  "Usuario registrado exitosamente";
        } else {
            throw new InternalServerErrorResponse('Error al registrar el usuario');
        }
    }
}
  }
}