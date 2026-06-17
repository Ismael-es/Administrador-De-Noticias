<?php
// tests/NoticiaTest.php



use PHPUnit\Framework\TestCase;

define('TESTING', true); // se define para que se pueda testear, sin necesidad de 


require_once __DIR__ . '/../Controller/procesar_crear_noticia.php';



class procesar_crear_noticiaTest extends TestCase {

    private $modeloDeNoticia;
    private $parametrosConfigurables;

    public function setUp():void {

        $this->modeloDeNoticia=$this->createMock(Noticia::class); // creo el objeto noticia simulado, falso
        $this->parametrosConfigurables=$this->createMock(Parametro::class); // lo mismo para parametro

        

    }

    // testing de session incorrecta

    public function testLoginSinSessionRetorno(){



        $this->assertEquals('redirect:../View/login.php',procesarCrearNoticia([],[],[], 
        $this->modeloDeNoticia,
        $this->parametrosConfigurables)); // estos parametros se pasan vacios ya no es necesario usarlos, menos la session para probar el testing


    }

    public function testLoginConSessionRetornoSinPersimo(){
           $this->assertEquals('redirect:../index.php?error=sin_permiso',procesarCrearNoticia([
            'id' => 2,
            'roles' => [2]
           ],[],[], 
        $this->modeloDeNoticia,
        $this->parametrosConfigurables));
    
        }

        public function testCamposInvalidosNoticia(){
            $sessionValida = ['id' => 1,'roles' => [1]];


                $this->assertEquals('redirect:../View/crear_noticia.php?error=campos_vacios&titulo=&descripcion=', procesarCrearNoticia(
                        $sessionValida,
                        ['titulo' => '', 'descripcion' => ''],
                        [],
                        $this->modeloDeNoticia,
                        $this->parametrosConfigurables

                ));

        }


        public function testCamposInvalidosTituloInvalidoLimiteSuperior(){
             $sessionValida = ['id' => 1,'roles' => [1]];

            $urlEsperada = 'redirect:../View/crear_noticia.php?error=titulo_invalido' . 
                    '&titulo=' . urlencode(str_repeat('a', 101)) . 
                    '&descripcion=' . urlencode(str_repeat('lorem impsum ', 20));

             $this->assertEquals($urlEsperada,
             procesarCrearNoticia(
                $sessionValida,
                ['titulo' => str_repeat('a', 101), 'descripcion' => str_repeat('lorem impsum ', 20)],
                [],
                $this->modeloDeNoticia,
                $this->parametrosConfigurables

             )
             );
        }

          public function testCamposInvalidosTituloInvalidoLimiteInferior(){
             $sessionValida = ['id' => 1,'roles' => [1]];

            $urlEsperada = 'redirect:../View/crear_noticia.php?error=titulo_invalido' . 
                    '&titulo=' . urlencode('holacomos') . 
                    '&descripcion=' . urlencode(str_repeat('lorem impsum ', 20));

             $this->assertEquals($urlEsperada,
             procesarCrearNoticia(
                $sessionValida,
                ['titulo' => 'holacomos' , 'descripcion' => str_repeat('lorem impsum ', 20)],
                [],
                $this->modeloDeNoticia,
                $this->parametrosConfigurables

             )
             );
        }

          public function testCamposInvalidosDescripcionInvalidaLimiteInferior(){
             $sessionValida = ['id' => 1,'roles' => [1]];

            $urlEsperada = 'redirect:../View/crear_noticia.php?error=descripcion_invalida' . 
                    '&titulo=' . urlencode('holacomoestas') . 
                    '&descripcion=' . urlencode(str_repeat('a', 49));

             $this->assertEquals($urlEsperada,
             procesarCrearNoticia(
                $sessionValida,
                ['titulo' => 'holacomoestas' , 'descripcion' => str_repeat('a', 49)],
                [],
                $this->modeloDeNoticia,
                $this->parametrosConfigurables

             )
             );
        }

        public function testNoticiaTituloRepetido(){

            $this->modeloDeNoticia->method('existeTitulo')->willReturn(true);
            $sessionValida = ['id' => 1,'roles' => [1]];

            $urlEsperada = 'redirect:../View/crear_noticia.php?error=titulo_duplicado' . 
                    '&titulo=' . urlencode('aaaaaaaaaaaaa') . 
                    '&descripcion=' . urlencode(str_repeat('a', 55));

             $this->assertEquals($urlEsperada,
             procesarCrearNoticia(
                $sessionValida,
                ['titulo' => 'aaaaaaaaaaaaa' , 'descripcion' => str_repeat('a', 55)],
                [],
                $this->modeloDeNoticia,
                $this->parametrosConfigurables

             )
             );


        }

        public function testNoticiaImagenExedeTamaño(){
            $post = ['titulo' => 'Titulo valido', 'descripcion' => str_repeat('a', 60)];
            $sessionValida = ['id' => 1,'roles' => [1]];
              $this->parametrosConfigurables->method('obtenerPorClave')->willReturn(2);

               $urlEsperada = 'redirect:../View/crear_noticia.php?error=imagen_tamano' . 
                    '&titulo=' . urlencode('Titulo valido') . 
                    '&descripcion=' . urlencode(str_repeat('a', 60));

            $this->assertEquals(
                    $urlEsperada,
                     procesarCrearNoticia(
                $sessionValida,
                $post,
                ['imagen' => [
                    'name' => 'foto_gigante.jpg',
                    'type' => 'image/jpeg',
                    'size' => 3 * 1024 * 1024, 
                    'tmp_name' => '/tmp/phpXYZ',
                    'error' => UPLOAD_ERR_OK
                ]],
                $this->modeloDeNoticia,
                $this->parametrosConfigurables

             )
            );

        }

        public function testNoticiaImagenFormatoInvalido(){
    $post = ['titulo' => 'Titulo valido', 'descripcion' => str_repeat('a', 60)];
    $sessionValida = ['id' => 1,'roles' => [1]];
    $this->parametrosConfigurables->method('obtenerPorClave')->willReturn(2);

    $urlEsperada = 'redirect:../View/crear_noticia.php?error=imagen_formato' . 
            '&titulo=' . urlencode('Titulo valido') . 
            '&descripcion=' . urlencode(str_repeat('a', 60));

    $this->assertEquals(
        $urlEsperada,
        procesarCrearNoticia(
            $sessionValida,
            $post,
            ['imagen' => [
                'name' => 'archivo.gif',
                'type' => 'image/gif',
                'size' => 1 * 1024 * 1024,
                'tmp_name' => '/tmp/phpXYZ',
                'error' => UPLOAD_ERR_OK
            ]],
            $this->modeloDeNoticia,
            $this->parametrosConfigurables
        )
    );
}



      





}