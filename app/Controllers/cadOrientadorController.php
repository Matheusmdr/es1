<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Views;
use App\Models\User;
use App\Utils\System;

class cadOrientadorController extends Controller{

    public function __construct() {}

    public static function index($params)
    {
      session_start();
      $page =  Views::render("template_usp","cadOrientador", [
        'URL' => '<base href="'.getenv('URL').'">',
        'title' => 'Sistema de Avaliação de Desempenho dos alunos do PPgSI - Cadastro Orientador'
      ]);

      $error = System::errorExists();

      echo str_replace('{{error}}', $error, $page);
    }

    public static function cadastrar()
    {
      if (isset($_POST['email']) && isset($_POST['password']))
      {
        User::cadastro('orientador');
      }
      else{
        header('Location: '.getenv('URL') .'cadOrientador');
      }
    }

    public static function getMethods()
    {
      return ["index","cadastrar"];
    }

}
