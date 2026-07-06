<?php

require_once __DIR__ . '/app/Middleware/auth.php';
require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Controllers/UsuariosController.php';
require_once __DIR__ . '/app/Controllers/PessoasController.php';
require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';
require_once __DIR__ . '/app/Controllers/AtendimentosController.php';
require_once __DIR__ . '/app/Controllers/FrontendController.php';
require_once __DIR__ . '/app/Controllers/DashboardController.php';

$controller = $_GET['controller'] ?? 'auth';
$action = $_GET['action'] ?? 'login';

if($controller === 'auth'){
    $auth = new AuthController();

    switch ($action){
        case 'login':
            $auth->exibirLogin();
            break;
        case 'entrar':
            $auth->entrar();
            break;
        case 'dashboard':
            exigirAutenticacao();
            $auth->dashboard();
            break;
        case 'logout':
            $auth->logout();
            break;
        default:
            http_response_code(404);
            echo 'Ação de autenticação não encontrada.';
    }
    exit;
}

exigirAutenticacao();

switch ($controller) {
    case 'usuarios':
        $obj = new UsuariosController();
        break;
    case 'pessoas':
        $obj = new PessoasController();
        break;
    case 'tipos':
        $obj = new TiposAtendimentosController();
        break;
    case 'atendimentos':
        $obj = new AtendimentosController();
        break;
    case 'frontend':
        $obj = new FrontendController();
        break;
    case 'dashboard':
        $obj = new DashboardController();
        break;
    default:
        http_response_code(404);
        exit('Controller não encontrado.');
}

if (!method_exists($obj, $action)) {
    http_response_code(404);
    exit('Ação não encontrada.');
}

$obj->$action();

?>