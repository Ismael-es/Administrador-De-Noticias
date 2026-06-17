<?php


use PHPUnit\Framework\TestCase;
define('TESTING', true);

require_once __DIR__ . '/../Controller/procesar_editar_noticia.php';


class procesar_editar_noticiaTest extends TestCase {

    private $modeloNoticia;
    private $modeloParametro;

    protected function setUp(): void {
        $this->modeloNoticia = $this->createMock(Noticia::class);
        $this->modeloParametro = $this->createMock(Parametro::class);
    }

    // sesiones

        public function testEditarNoticiaSinSession() {
            $resultado = procesarEditarNoticia([], [], [], $this->modeloNoticia, $this->modeloParametro);
            $this->assertEquals('redirect:../View/login.php', $resultado);
        }

    public function testEditarNoticiaCamposVacios() {
        $session = ['id' => 1, 'roles' => [2]];
        $post = [];
        $files = [];

      
        $this->assertEquals('redirect:../index.php?error=sin_permiso',   procesarEditarNoticia(
            $session, 
            $post,
            $files,
            $this->modeloNoticia,
            $this->modeloParametro));
    }

    public function testEditarNoticiaIdNulo(){

        $sessionValido= ['id' => 1, 'roles' => [1]];
        $post = ['noticia_id' => ''];

        $this->assertEquals('redirect:../index.php',   procesarEditarNoticia(
            $sessionValido, 
            $post,
            [],
            $this->modeloNoticia,
            $this->modeloParametro));


    }

    public function testEditarNoticiaTituloYDescripcionVacios(){

        $sessionValido= ['id' => 1, 'roles' => [1]];
        $post = ['noticia_id' => 1, 'titulo' => '', 'descripcion' => ''];

        $this->assertEquals('redirect:../View/crear_noticia.php?id=1&error=campos_vacios&titulo=&descripcion=',   procesarEditarNoticia(
            $sessionValido, 
            $post,
            [],
            $this->modeloNoticia,
            $this->modeloParametro));

    }


    public function testEditarNoticiaLimiteInferiorTitulo(){
    
        $sessionValido= ['id' => 1, 'roles' => [1]];
        $post = ['noticia_id' => 1, 'titulo' => str_repeat('a', 9), 'descripcion' => str_repeat('lorem impsum ', 20)];

        $urlEsperada = 'redirect:../View/crear_noticia.php?id=1&error=titulo_invalido' . 
                    '&titulo=' . urlencode(str_repeat('a', 9)) . 
                    '&descripcion=' . urlencode(str_repeat('lorem impsum ', 20));

        $this->assertEquals($urlEsperada,   procesarEditarNoticia(
            $sessionValido, 
            $post,
            [],
            $this->modeloNoticia,
            $this->modeloParametro));

    }

       public function testEditarNoticiaLimiteSuperiorTitulo(){
    
        $sessionValido= ['id' => 1, 'roles' => [1]];
        $post = ['noticia_id' => 1, 'titulo' => str_repeat('a', 101), 'descripcion' => str_repeat('lorem impsum ', 20)];

        $urlEsperada = 'redirect:../View/crear_noticia.php?id=1&error=titulo_invalido' . 
                    '&titulo=' . urlencode(str_repeat('a', 101)) . 
                    '&descripcion=' . urlencode(str_repeat('lorem impsum ', 20));

        $this->assertEquals($urlEsperada,   procesarEditarNoticia(
            $sessionValido, 
            $post,
            [],
            $this->modeloNoticia,
            $this->modeloParametro));

    }

      public function testEditarNoticiaValoresEspecialesTitulo(){
    
        $sessionValido= ['id' => 1, 'roles' => [1]];
        $post = ['noticia_id' => 1, 'titulo' => -3, 'descripcion' => str_repeat('lorem impsum ', 20)];

        $urlEsperada = 'redirect:../View/crear_noticia.php?id=1&error=titulo_invalido' . 
                    '&titulo=' . urlencode(-3) . 
                    '&descripcion=' . urlencode(str_repeat('lorem impsum ', 20));

        $this->assertEquals($urlEsperada,   procesarEditarNoticia(
            $sessionValido, 
            $post,
            [],
            $this->modeloNoticia,
            $this->modeloParametro));

    }

    public function testEditarNoticiaLimiteInferiorDescripcion(){
    
        $sessionValido= ['id' => 1, 'roles' => [1]];
        $post = ['noticia_id' => 1, 'titulo' => str_repeat('a', 50), 'descripcion' => str_repeat('a', 49)];

        $urlEsperada = 'redirect:../View/crear_noticia.php?id=1&error=descripcion_invalida' . 
                    '&titulo=' . urlencode(str_repeat('a', 50)) . 
                    '&descripcion=' . urlencode(str_repeat('a', 49));

        $this->assertEquals($urlEsperada,   procesarEditarNoticia(
            $sessionValido, 
            $post,
            [],
            $this->modeloNoticia,
            $this->modeloParametro));

    }

    public function testEditarNoticiaExistenteTitulo(){

        $sessionValido = ['id' => 1, 'roles' => [1]];
        $post = [
            'noticia_id'  => 1,
            'titulo'      => str_repeat('a', 50),
            'descripcion' => str_repeat('lorem impsum ', 20)
        ];

        $this->modeloNoticia
            ->method('existeTitulo')
            ->willReturn(true);

        $urlEsperada = 'redirect:../View/crear_noticia.php?id=1&error=titulo_duplicado'
            . '&titulo='       . urlencode(str_repeat('a', 50))
            . '&descripcion='  . urlencode(str_repeat('lorem impsum ', 20));

        $this->assertEquals($urlEsperada, procesarEditarNoticia(
            $sessionValido,
            $post,
            [],
            $this->modeloNoticia,
            $this->modeloParametro
        ));
    }

    //testing de imagenes, formatos, tamaños, errores al guardar, etc.

    public function testEditarNoticiaFormatoImagenInvalido() {
        $sessionValido = ['id' => 1, 'roles' => [1]];
        $post = [
            'noticia_id'  => 1,
            'titulo'      => str_repeat('a', 50),
            'descripcion' => str_repeat('lorem impsum ', 20)
        ];
        $files = [
            'imagen' => [
                'name' => 'foto.gif',
                'type' => 'image/gif',
                'tmp_name' => '/tmp/phpYzdqkD',
                'error' => UPLOAD_ERR_OK,
                'size' => 500000
            ]
        ];

        $urlEsperada = 'redirect:../View/crear_noticia.php?id=1&error=imagen_formato'
            . '&titulo='       . urlencode(str_repeat('a', 50))
            . '&descripcion='  . urlencode(str_repeat('lorem impsum ', 20));

        $this->assertEquals($urlEsperada, procesarEditarNoticia(
            $sessionValido,
            $post,
            $files,
            $this->modeloNoticia,
            $this->modeloParametro
        ));
    }


    public function testEditarNoticiaTamañoImagenExcedido() {
        $sessionValido = ['id' => 1, 'roles' => [1]];
        $post = [
            'noticia_id'  => 1,
            'titulo'      => str_repeat('a', 50),
            'descripcion' => str_repeat('lorem impsum ', 20)
        ];
        $files = [
            'imagen' => [
                'name' => 'foto.jpg',
                'type' => 'image/jpeg',
                'tmp_name' => '/tmp/phpYzdqkD',
                'error' => UPLOAD_ERR_OK,
                'size' => 5 * 1024 * 1024 
            ]
        ];

        $this->modeloParametro
            ->method('obtenerPorClave')
            ->with('imagen_max_mb')
            ->willReturn(2);

        $urlEsperada = 'redirect:../View/crear_noticia.php?id=1&error=imagen_tamano'
            . '&titulo='       . urlencode(str_repeat('a', 50))
            . '&descripcion='  . urlencode(str_repeat('lorem impsum ', 20));

        $this->assertEquals($urlEsperada, procesarEditarNoticia(
            $sessionValido,
            $post,
            $files,
            $this->modeloNoticia,
            $this->modeloParametro
        ));
    }

    public function testEditarNoticiaErrorAlGuardar() {
        $sessionValido = ['id' => 1, 'roles' => [1]];
        $post = [
            'noticia_id'  => 1,
            'titulo'      => str_repeat('a', 50),
            'descripcion' => str_repeat('lorem impsum ', 20)
        ];
        $files = [];

        $this->modeloNoticia
            ->method('editar')
            ->willReturn(false);

        $urlEsperada = 'redirect:../View/crear_noticia.php?id=1&error=error_guardar'
            . '&titulo='       . urlencode(str_repeat('a', 50))
            . '&descripcion='  . urlencode(str_repeat('lorem impsum ', 20));

        $this->assertEquals($urlEsperada, procesarEditarNoticia(
            $sessionValido,
            $post,
            $files,
            $this->modeloNoticia,
            $this->modeloParametro
        ));
    }

    public function testEditarNoticiaExito() {
        $sessionValido = ['id' => 1, 'roles' => [1]];
        $post = [
            'noticia_id'  => 1,
            'titulo'      => str_repeat('a', 11),
            'descripcion' => str_repeat('lorem impsum ', 20)
        ];
        $files = [];

        $this->modeloNoticia
            ->method('editar')
            ->willReturn(true);

        $resultado = procesarEditarNoticia(
            $sessionValido,
            $post,
            $files,
            $this->modeloNoticia,
            $this->modeloParametro
        );

        $this->assertEquals('redirect:../View/detalle_noticia.php?id=1&exito=estado_cambiado', $resultado);
    }

    public function testEditarNoticiaExitoConImagen() {
        $sessionValido = ['id' => 1, 'roles' => [1]];
        $post = [
            'noticia_id'  => 1,
            'titulo'      => str_repeat('a', 11),
            'descripcion' => str_repeat('lorem impsum ', 20)
        ];
        $files = [
            'imagen' => [
                'name' => 'foto.jpg',
                'type' => 'image/jpeg',
                'tmp_name' => '/tmp/phpYzdqkD',
                'error' => UPLOAD_ERR_OK,
                'size' => 1 * 1024 * 1024
            ]
        ];

           $this->modeloNoticia
            ->method('existeTitulo')  
            ->willReturn(false);

         $this->modeloParametro
         ->method('obtenerPorClave') 
         ->willReturn(2);            


        $this->modeloNoticia
            ->method('editar')
            ->willReturn(true);

        $resultado = procesarEditarNoticia(
            $sessionValido,
            $post,
            $files,
            $this->modeloNoticia,
            $this->modeloParametro
        );

        $this->assertEquals('redirect:../View/detalle_noticia.php?id=1&exito=estado_cambiado', $resultado);
    }
    

    // CASO 1: estado_actual == 3 y hay estado_nuevo → SÍ llama cambiarEstado
        public function testEditarNoticiaCambiaEstadoCuandoEsEstado3() {
            $sessionValido = ['id' => 1, 'roles' => [1]];
            $post = [
                'noticia_id'    => 1,
                'titulo'        => str_repeat('a', 11),
                'descripcion'   => str_repeat('lorem impsum ', 20),
                'estado_actual' => 3,     
                'estado_nuevo'  => 2      
            ];

            $this->modeloNoticia->method('existeTitulo')->willReturn(false);
            $this->modeloNoticia->method('editar')->willReturn(true);
            $this->modeloParametro->method('obtenerPorClave')->willReturn(2);

            // Verifica que cambiarEstado SE llama exactamente 1 vez con esos parámetros
            $this->modeloNoticia
                ->expects($this->once())
                ->method('cambiarEstado')
                ->with(1, 2, 1); 

            procesarEditarNoticia(
                $sessionValido,
                $post,
                [],
                $this->modeloNoticia,
                $this->modeloParametro
            );
        }

// CASO 2: estado_actual != 3 → NO llama cambiarEstado
        public function testEditarNoticiaNoLlamaCambiarEstadoSiNoEsEstado3() {
            $sessionValido = ['id' => 1, 'roles' => [1]];
            $post = [
                'noticia_id'    => 1,
                'titulo'        => str_repeat('a', 11),
                'descripcion'   => str_repeat('lorem impsum ', 20),
                'estado_actual' => 1,      // <-- no es 3
                'estado_nuevo'  => 2
            ];

            $this->modeloNoticia->method('existeTitulo')->willReturn(false);
            $this->modeloNoticia->method('editar')->willReturn(true);
            $this->modeloParametro->method('obtenerPorClave')->willReturn(2);

            // Verifica que cambiarEstado NUNCA se llama
            $this->modeloNoticia
                ->expects($this->never())
                ->method('cambiarEstado');

            procesarEditarNoticia(
                $sessionValido,
                $post,
                [],
                $this->modeloNoticia,
                $this->modeloParametro
            );
        }

// CASO 3: estado_actual == 3 pero sin estado_nuevo → NO llama cambiarEstado
        public function testEditarNoticiaNoLlamaCambiarEstadoSinEstadoNuevo() {
            $sessionValido = ['id' => 1, 'roles' => [1]];
            $post = [
                'noticia_id'    => 1,
                'titulo'        => str_repeat('a', 11),
                'descripcion'   => str_repeat('lorem impsum ', 20),
                'estado_actual' => 3,      // es 3
                'estado_nuevo'  => null    // <-- pero no hay estado nuevo
            ];

            $this->modeloNoticia->method('existeTitulo')->willReturn(false);
            $this->modeloNoticia->method('editar')->willReturn(true);
            $this->modeloParametro->method('obtenerPorClave')->willReturn(2);

            $this->modeloNoticia
                ->expects($this->never())
                ->method('cambiarEstado');

            procesarEditarNoticia(
                $sessionValido,
                $post,
                [],
                $this->modeloNoticia,
                $this->modeloParametro
            );
        }





    // Agregar más tests para los casos restantes (formato de imagen, tamaño de imagen, error al guardar, etc.)
}


?>