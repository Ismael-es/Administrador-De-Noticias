<?php

 use PHPUnit\Framework\TestCase;



define('TESTING', true);

 require_once __DIR__ . '/../Controller/procesar_cambiar_estado.php';
class procesar_cambiar_estadoTest extends TestCase{


    private $modeloNoticia;
    private $modeloParametro;
    private $modeloUsuario;

    public function setUp(): void {

        $this->modeloNoticia=$this->createMock(Noticia::class);

    }



    public function testCambiarEstadoValoresVaciosSession(){

        $this->assertEquals('redirect:../View/login.php', procesarCambiarEstado([],[],$this->modeloNoticia));

    
    }

    public function testCambiarEstadoValoresVaciosNoticia(){

        $sessionValida=['id'=>1, 'roles'=>[1,2]];
        
        $this->assertEquals('redirect:../index.php', procesarCambiarEstado(
            $sessionValida,
            ['noticia_id'=> '', 'estado_nuevo' => ''],
            $this->modeloNoticia
            ));


    }

    public function testCambiarEstadoValoresIncorrectos(){

        $sessionValida=['id'=>1, 'roles'=>[1,2]];

        $this->modeloNoticia->method('obtenerPorId')->willReturn(['noticia_estado' => 1, 'noticia_autor'=>34]);

        $url_esperada='redirect:../View/detalle_noticia.php?id=6&error=sin_permiso';

        $this->assertEquals($url_esperada, procesarCambiarEstado(
            $sessionValida,
            ['noticia_id' => 6, 'estado_nuevo'=> 6],
            $this->modeloNoticia
        ));

    }

    public function testCambiarEstadoFalloCambiarEstado(){


              $sessionValida=['id'=>1, 'roles'=>[1,2]];

        $this->modeloNoticia->method('obtenerPorId')->willReturn(['noticia_estado' => 2, 'noticia_autor'=>4]);

        $this->modeloNoticia->method('cambiarEstado')->willReturn(false);

        $url_esperada='redirect:../View/detalle_noticia.php?id=10&error=error_estado';

        $resultado = procesarCambiarEstado(
            $sessionValida,
            ['noticia_id' => 10, 'estado_nuevo' => 3],
            $this->modeloNoticia
        );

        $this->assertEquals($url_esperada, $resultado);


    }


    public function testCambiarEstadoExitoCambiarEstado(){

        $sessionValida=['id'=>1, 'roles'=>[1,2]];

        $this->modeloNoticia->method('obtenerPorId')->willReturn(['noticia_estado' => 2, 'noticia_autor'=>3]);

        $this->modeloNoticia->method('cambiarEstado')->willReturn(true);


        $url_esperada='redirect:../View/detalle_noticia.php?id=5&exito=estado_cambiado';

        $this->assertEquals($url_esperada, procesarCambiarEstado(
            $sessionValida,
            ['noticia_id'=>5, 'estado_nuevo'=>4],
            $this->modeloNoticia
        ));

    }

     public function testCambiarEstadoExitoCambiarEstadoEditor(){

        $sessionValida=['id'=>1, 'roles'=>[1]];

        $this->modeloNoticia->method('obtenerPorId')->willReturn(['noticia_estado' => 1, 'noticia_autor'=>1]);

        $this->modeloNoticia->method('cambiarEstado')->willReturn(true);


        $url_esperada='redirect:../View/detalle_noticia.php?id=5&exito=estado_cambiado';

        $this->assertEquals($url_esperada, procesarCambiarEstado(
            $sessionValida,
            ['noticia_id'=>5, 'estado_nuevo'=>2],
            $this->modeloNoticia
        ));

    }



    public function testCambiarEstadoValoresSinPermisoEditor(){

        $sessionValida=['id'=>1, 'roles'=>[1]];

        $this->modeloNoticia->method('obtenerPorId')->willReturn(['noticia_estado' => 2, 'noticia_autor'=>3]);

        $this->modeloNoticia->method('cambiarEstado')->willReturn(true);

         $url_esperada='redirect:../View/detalle_noticia.php?id=6&error=sin_permiso';

        $this->assertEquals($url_esperada, procesarCambiarEstado(
            $sessionValida,
            ['noticia_id'=>6, 'estado_nuevo'=>4],
            $this->modeloNoticia
        ));
    }


        public function testCambiarEstadoValoresErrorPublicadaAListaParaValidacion(){

        $sessionValida=['id'=>1, 'roles'=>[1,2]];

        $this->modeloNoticia->method('obtenerPorId')->willReturn(['noticia_estado' => 4, 'noticia_autor'=>1]);

        $this->modeloNoticia->method('cambiarEstado')->willReturn(true);

         $url_esperada='redirect:../View/detalle_noticia.php?id=6&error=sin_permiso';

        $this->assertEquals($url_esperada, procesarCambiarEstado(
            $sessionValida,
            ['noticia_id'=>6, 'estado_nuevo'=>2],
            $this->modeloNoticia
        ));
    }

    public function testCambiarEstadoListaValidacionFallaSiEsElMismoAutor() {
        $sessionValida = ['id' => 5, 'roles' => [2]]; 
        
       
        $this->modeloNoticia->method('obtenerPorId')->willReturn(['noticia_estado' => 2, 'noticia_autor' => 5]);

        $url_esperada = 'redirect:../View/detalle_noticia.php?id=7&error=sin_permiso';

        $resultado = procesarCambiarEstado(
            $sessionValida,
            ['noticia_id' => 7, 'estado_nuevo' => 3],
            $this->modeloNoticia
        );

        $this->assertEquals($url_esperada, $resultado);
    }

            public function testCambiarEstadoDesdeParaCorreccion() {
            $sessionValida = ['id' => 1, 'roles' => [1]]; // editor

            $this->modeloNoticia->method('obtenerPorId')
                ->willReturn(['noticia_estado' => 3, 'noticia_autor' => 1]); // estado 3, mismo autor

            $this->modeloNoticia->method('cambiarEstado')->willReturn(true);

            $url_esperada = 'redirect:../View/detalle_noticia.php?id=8&exito=estado_cambiado';

            $this->assertEquals($url_esperada, procesarCambiarEstado(
                $sessionValida,
                ['noticia_id' => 8, 'estado_nuevo' => 2], // vuelve a lista para validación
                $this->modeloNoticia
            ));
        }

    // valores especiales

        public function testCambiarEstadoValoresEspeciales() {

            $sessionValida=['id'=>1, 'roles'=>[1,2]];

            $this->modeloNoticia->method('obtenerPorId')->willReturn(['noticia_estado' => 2, 'noticia_autor'=>3]);


             $url_esperada='redirect:../View/detalle_noticia.php?id=hola&error=sin_permiso';

            $this->assertEquals($url_esperada, procesarCambiarEstado(
                $sessionValida,
                ['noticia_id'=>'hola', 'estado_nuevo'=>'mundo'],
                $this->modeloNoticia
            ));
        }

            public function testCambiarEstadoValoresEspecialesValoresNull() {

            $session=['id'=>1, 'roles'=>[1,2]];

            $this->modeloNoticia->method('obtenerPorId')->willReturn(['noticia_estado' => 2, 'noticia_autor'=>3]);


             $url_esperada='redirect:../index.php';

            $this->assertEquals($url_esperada, procesarCambiarEstado(
                $session,
                ['noticia_id'=>null, 'estado_nuevo'=>null],
                $this->modeloNoticia
            ));
        }


          public function testCambiarEstadoValoresSinPermisoAdmin(){

        $sessionValida=['id'=>1, 'roles'=>[3]];

        $this->modeloNoticia->method('obtenerPorId')->willReturn(['noticia_estado' => 2, 'noticia_autor'=>3]);

        $this->modeloNoticia->method('cambiarEstado')->willReturn(true);

         $url_esperada='redirect:../View/detalle_noticia.php?id=6&error=sin_permiso';

        $this->assertEquals($url_esperada, procesarCambiarEstado(
            $sessionValida,
            ['noticia_id'=>6, 'estado_nuevo'=>4],
            $this->modeloNoticia
        ));
    }



    





} 


?>