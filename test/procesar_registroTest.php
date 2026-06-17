<?php

use PHPUnit\Framework\TestCase;

define('TESTING', true);

 require_once __DIR__ . '/../Controller/procesar_registro.php';

class procesar_registroTest extends TestCase{

 private $modeloUsuario;

    protected function setUp(): void {
        $this->modeloUsuario = $this->createMock(Usuario::class);
    }

    public function testRegistroCamposVacios() {
        $resultado = procesarRegistro([], $this->modeloUsuario);
        $this->assertEquals('redirect:../view/login.php?error=campos_vacios', $resultado);
    }

    public function testRegistroCamposInvalidos() {
        
        $this->assertEquals('redirect:../view/login.php?error=passwords_no_coinciden', procesarRegistro(
        ['usuario' => 'test', 'password' => 'hola', 'password_confirmacion' => 'holasdos', 'rol'=>[]],
         $this->modeloUsuario));

    }

    public function testRegistroSinRol() {
        $this->assertEquals('redirect:../view/login.php?error=sin_rol', procesarRegistro(
        ['usuario' => 'test', 'password' => 'contraseña1', 'password_confirmacion' => 'contraseña1', 'rol'=>[]],
         $this->modeloUsuario));
    }

      public function testRegistroErrorAlRegistrarUsuarioExistente() {



        $this->assertEquals('redirect:../view/login.php?error=usuario_existente', procesarRegistro(
        ['usuario' => 'test', 'password' => 'hola', 'password_confirmacion' => 'hola', 'rol'=>[1,2]],
         $this->modeloUsuario));
    }

       public function testRegistrarUsuarioExito(){

       $this->modeloUsuario->method('registrar')->with('testdos', 'hola', [1, 2])->willReturn(true);

        $this->assertEquals('redirect:../view/login.php?exito=registro_ok', procesarRegistro(
        ['usuario' => 'testdos', 'password' => 'hola', 'password_confirmacion' => 'hola', 'rol'=>[1,2]],
         $this->modeloUsuario));
       }


       //valores limintes y especiales
        // FALLO EN EL TEST
      public function testRegistroUsuarioLargo() {
        $usuarioLargo = str_repeat('a', 255);
        $contraseñaLarga = str_repeat('a', 255);

        $resultado = procesarRegistro(
            ['usuario' => $usuarioLargo, 'password' => $contraseñaLarga, 'password_confirmacion' => $contraseñaLarga, 'rol' => [1]],
            $this->modeloUsuario
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Nombre de usuario o contraseña demasiado largos');
        procesarRegistro(
            ['usuario' => $usuarioLargo, 'password' => $contraseñaLarga, 'password_confirmacion' => $contraseñaLarga, 'rol' => [1]],
            $this->modeloUsuario
        );


    }

    //limites se supone que un usuario no debería tener más de 20 caracteres, si se ingresa un nombre de usuario con 20 caracteres, debería dar error por límite excedido, igual que la contraseña maximo 20 caracteres, si se ingresa una contraseña con 20 caracteres, debería dar error por límite excedido
    //fallo en el test
    public function testRegistroUsuarioLargoDeberiaDarErrorPorLimite() {
        $usuarioLargo = str_repeat('a', 31); 
        $contraseñaLarga = 'ismael_1';
                                                                                                                  
        $url_esperada = 'redirect:../view/login.php?error=nombre_demasiado_largo';

        $resultado = procesarRegistro(
            ['usuario' => $usuarioLargo, 'password' => $contraseñaLarga, 'password_confirmacion' => $contraseñaLarga, 'rol' => [1]],
            $this->modeloUsuario
        );

    
        $this->assertEquals($url_esperada, $resultado);
    }

    // fallo en el test
    public function testRegistroContraseñaLargaDeberiaDarErrorPorLimite() {
        $usuarioValido = 'usuarioValido';
        $contraseñaLarga = str_repeat('a', 9); 

        $url_esperada = 'redirect:../view/login.php?error=contraseña_demasiado_larga';

        $resultado = procesarRegistro(
            ['usuario' => $usuarioValido, 'password' => $contraseñaLarga, 'password_confirmacion' => $contraseñaLarga, 'rol' => [1]],
            $this->modeloUsuario
        );

    
        $this->assertEquals($url_esperada, $resultado);
    
    }

    public function testRegistroContraseñaLargaYUsuarioLargoDeberiaDarErrorPorLimite() {
        $usuarioLargo = str_repeat('a', 31); 
        $contraseñaLarga = str_repeat('a', 9); 

        $url_esperada = 'redirect:../view/login.php?error=datos_invalidos';

        $resultado = procesarRegistro(
            ['usuario' => $usuarioLargo, 'password' => $contraseñaLarga, 'password_confirmacion' => $contraseñaLarga, 'rol' => [1]],
            $this->modeloUsuario
        );

    
        $this->assertEquals($url_esperada, $resultado);

    }

    public function testRegistroUsuarioLargoYContraseñaLargaDeberiaDarErrorPorLimite() {
        $usuarioNoValido = 123123;
        $contraseñaNoValida = 234324; 

        $url_esperada = 'redirect:../view/login.php?error=datos_invalidos';

        $resultado = procesarRegistro(
            ['usuario' => $usuarioNoValido, 'password' => $contraseñaNoValida, 'password_confirmacion' => $contraseñaNoValida, 'rol' => [1]],
            $this->modeloUsuario
        );

    
        $this->assertEquals($url_esperada, $resultado);
    }

        public function testRegistroUsuarioConCaracteresEspecialesDeberiaDarErrorPorCaracteresInvalidos() {
            $usuarioConCaracteresEspeciales = 'usuario$%&';
            $contraseñaValida = 'contraseñaValida'; 

            $url_esperada = 'redirect:../view/login.php?error=datos_invalidos';

            $resultado = procesarRegistro(
                ['usuario' => $usuarioConCaracteresEspeciales, 'password' => $contraseñaValida, 'password_confirmacion' => $contraseñaValida, 'rol' => [1]],
                $this->modeloUsuario
            );

        
            $this->assertEquals($url_esperada, $resultado);
        }

          public function testRegistroUsuarioUsuarioNombreLimiteMenor() {
            $usuarioConCaracteresEspeciales = 'u';
            $contraseñaValida = 'contraseñaValida'; 

            $url_esperada = 'redirect:../view/login.php?error=nombre_demasiado_corto';

            $resultado = procesarRegistro(
                ['usuario' => $usuarioConCaracteresEspeciales, 'password' => $contraseñaValida, 'password_confirmacion' => $contraseñaValida, 'rol' => [1]],
                $this->modeloUsuario
            );

        
            $this->assertEquals($url_esperada, $resultado);
        }

        public function testRegistroUsuarioContraseñaLimiteMenor() {
            $usuarioValido = 'usuarioValido';
            $contraseñaCorta = 'c';

            $url_esperada = 'redirect:../view/login.php?error=contraseña_demasiado_corta';

            $resultado = procesarRegistro(
                ['usuario' => $usuarioValido, 'password' => $contraseñaCorta, 'password_confirmacion' => $contraseñaCorta, 'rol' => [1]],
                $this->modeloUsuario
            );

        
             $this->assertEquals($url_esperada, $resultado);    
        }

}
?>