
<?php

use PHPUnit\Framework\TestCase;

define('TESTING', true); // se define para que se pueda testear

require_once __DIR__ . '/../Controller/procesar_parametros.php';


class procesar_parametrosTest extends TestCase{

    private $parametrosConfigurables;
    private $modeloDeNoticia;

    public function setUp(): void{
        $this->parametrosConfigurables = $this->createMock(Parametro::class);
        $this->modeloDeNoticia = $this->createMock(Noticia::class);
    }


    // test basicos, sessiones validas/ invalidas, campos vaios, valores no numericos, valores negativos, error al guardar, exito al guardar

    public function testPrametrosLoginSinSessionRetorno(){
        $this->assertEquals('redirect:../View/login.php',procesarParametros([],[], 
        $this->parametrosConfigurables,
        $this->modeloDeNoticia)); 
    }

    public function testParametrosLoginConSessionRetorno(){
        $sessionValida=['id'=>1, 'roles'=>[1,2]];
        $this->assertEquals('redirect:../View/parametros.php?error=campos_vacios',procesarParametros($sessionValida,[], 
        $this->parametrosConfigurables,
        $this->modeloDeNoticia)); 
    }

    public function testParametrosCamposVaciosRetorno(){
        $sessionValida=['id'=>1, 'roles'=>[1,2]];
        $this->assertEquals('redirect:../View/parametros.php?error=campos_vacios',procesarParametros($sessionValida,['dias_expiracion'=>'', 'imagen_max_mb'=>''], 
        $this->parametrosConfigurables,
        $this->modeloDeNoticia)); 
    }

      public function testParametrosRetornoValoresInvalidosParametros(){
        $sessionValida=['id'=>1, 'roles'=>[1,2]];
        $this->assertEquals('redirect:../View/parametros.php?error=valores_invalidos',procesarParametros($sessionValida,['dias_expiracion'=>'asdsad', 'imagen_max_mb'=>'asdasdsad'], 
        $this->parametrosConfigurables,
        $this->modeloDeNoticia)); 
    }

    public function testParametrosRetornoValoresInvalidosNegativos(){
        $sessionValida=['id'=>1, 'roles'=>[1,2]];
        $this->assertEquals('redirect:../View/parametros.php?error=valores_invalidos',procesarParametros($sessionValida,['dias_expiracion'=>-5, 'imagen_max_mb'=>-5], 
        $this->parametrosConfigurables,
        $this->modeloDeNoticia)); 
    }

     public function testParametrosRetornoValoresInvalidosNegativosValidoYExitoso(){
        $sessionValida=['id'=>1, 'roles'=>[1,2]];
        $this->assertEquals('redirect:../View/parametros.php?error=valores_invalidos',procesarParametros($sessionValida,['dias_expiracion'=>-5, 'imagen_max_mb'=>5], 
        $this->parametrosConfigurables,
        $this->modeloDeNoticia)); 
    }

    public function testParametrosNoValidos(){
        $sessionValida=['id'=>1, 'roles'=>[1,2]];

        $this->parametrosConfigurables->method('actualizar')->willReturn(false);

        $this->assertEquals('redirect:../View/parametros.php?error=error_guardar',procesarParametros($sessionValida,['dias_expiracion'=>5, 'imagen_max_mb'=>5], 
        $this->parametrosConfigurables,
        $this->modeloDeNoticia));

    }

        public function testParametrosExito(){
            $sessionValida=['id'=>1, 'roles'=>[1,2]];
    
            $this->parametrosConfigurables->method('actualizar')->willReturn(true);
    
            $this->assertEquals('redirect:../View/parametros.php?exito=parametros_guardados',procesarParametros($sessionValida,['dias_expiracion'=>5, 'imagen_max_mb'=>5], 
            $this->parametrosConfigurables,
            $this->modeloDeNoticia));

        }

        // valores especiales, como 0, numeros muy grandes, etc. para verificar que se manejen correctamente

        public function testParametrosExitoValoresEspeciales(){
            $sessionValida=['id'=>1, 'roles'=>[1,2]];
    
            $this->parametrosConfigurables->method('actualizar')->willReturn(true);
    
            $this->assertEquals('redirect:../View/parametros.php?error=campos_vacios',procesarParametros($sessionValida,['dias_expiracion'=>0, 'imagen_max_mb'=>0], 
            $this->parametrosConfigurables,
            $this->modeloDeNoticia));

        }

         public function testParametrosExitoValoresEspecialesExtraordinarios(){
            $sessionValida=['id'=>1, 'roles'=>[1,2]];
    
    
            $this->assertEquals('redirect:../View/parametros.php?error=valores_invalidos',procesarParametros($sessionValida,['dias_expiracion'=>[1,2], 'imagen_max_mb'=>[0.20, 0.30]], 
            $this->parametrosConfigurables,
            $this->modeloDeNoticia));

        }

        
         public function testParametrosExitoValoresEspecialesExtraordinariosExpirarNoticia(){
            $sessionValida=['id'=>1, 'roles'=>[1,2]];

            $this->parametrosConfigurables->method('actualizar')->willReturnOnConsecutiveCalls(true, false);
    
            $this->assertEquals('redirect:../View/parametros.php?error=error_guardar',procesarParametros($sessionValida,['dias_expiracion'=>20, 'imagen_max_mb'=>2], 
            $this->parametrosConfigurables,
            $this->modeloDeNoticia));

        }





}

?>