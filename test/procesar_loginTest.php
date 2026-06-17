<?php

use PHPUnit\Framework\TestCase;

define('TESTING', true);

require_once __DIR__ . '/../Controller/procesar_login.php';


class procesar_loginTest extends TestCase {

    private $modeloUsuario;

    protected function setUp(): void {
        $this->modeloUsuario = $this->createMock(Usuario::class);
    }

    public function testLoginCamposVacios() {
        $resultado = procesarLogin([], $this->modeloUsuario);
        $this->assertEquals('../View/login.php?error=campos_vacios', $resultado['redirect']);
        $this->assertEmpty($resultado['session']);
    }

    public function testLoginCredencialesInvalidas() {
        $this->modeloUsuario->method('login')->willReturn(false);
        $resultado = procesarLogin(['usuario' => 'test', 'password' => 'wrong'], $this->modeloUsuario);
        $this->assertEquals('../View/login.php?error=credenciales_invalidas', $resultado['redirect']);
        $this->assertEmpty($resultado['session']);
    }

    public function testLoginExitoso() {
        $this->modeloUsuario->method('login')->willReturn(['id_usuario' => 1, 'nombre_usuario' => 'test']);
        $this->modeloUsuario->method('obtenerRoles')->willReturn([1, 2]);
        
        $resultado = procesarLogin(['usuario' => 'test', 'password' => 'correct'], $this->modeloUsuario);
        
        $this->assertEquals('../index.php', $resultado['redirect']);
        $this->assertEquals(1, $resultado['session']['id']);
        $this->assertEquals('test', $resultado['session']['nombre']);
        $this->assertEquals([1, 2], $resultado['session']['roles']);
    }

    // VALORES ESPECIALES

    public function testLoginValoresEspeciales() {
        $this->modeloUsuario->method('login')->willReturn(false);
        $resultado = procesarLogin(['usuario' => 123, 'password' => 'pass'], $this->modeloUsuario);
        $this->assertEquals('../View/login.php?error=credenciales_invalidas', $resultado['redirect']);
        $this->assertEmpty($resultado['session']);
    }
}


?>