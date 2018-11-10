<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    public $users = [];
    public $hasRead = false;
    public $file = __DIR__ . "/../users.data";
    public function readUsers()
    {
        $handler = fopen($this->file, "a+");
        if (!$handler) {
            exit("the users.data can not read or create");
        }
        // read users
        rewind($handler);
        if($size = filesize($this->file)) {
            $content = fread($handler, filesize($this->file) || 0);

        } else {
            $content = "";
        }
        fclose($handler);
        // parse string
        $arr = explode("\n", $content);
        $i = 0;
        while ($i < count($arr)) {
            $this->users[$arr[i]] = $arr[i + 1];
            $i += 2;
        }
        $this->hasRead = true;
    }
    public function getUsers()
    {
        if (!$this->hasRead) {
            $this->readUsers();
        }

        return $this->users;
    }
    public function writeUser($name, $pw)
    {
        $us = $this->getUsers();
        $us[$name] = $pw;
        file_put_contents($this->file, "$name\n$pw\n", FILE_APPEND);
    }
    /**
     * @Route("/login", name="login")
     */
    public function index()
    {
        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
            "login" => true,
        ]);
    }
    /**
     * @Route("/login", name="login_post", methods={"POST"})
     *
     * @return void
     */
    public function onLogin()
    {
        // $resp = new Response();
        // $resp->headers->set("Content-Type", "application/json");
        $request = Request::createFromGlobals();
        $name = $request->request->get("name");
        $password = $request->request->get("password");
        if (!isset($users, $name)) {
            $error = "此用户未注册";
        } else if ($users[$name] !== $password) {
            $error = "密码不正确";
        }
        if ($error) {
            return $this->render("/login/index.html.twig", [
                "error" => $error,
            ]);
        } else {
            // go to the home page
            return $this->render("/index.html.twig");
        }
    }

    /**
     * @Route("/register", name="register", methods={"GET"})
     *
     * @return void
     */
    public function register()
    {
        return $this->render('register/index.html.twig', [

            'controller_name' => 'LoginController',
            'login' => false,   
        ]);
    }
    /**
     * @Route("/register", name="register_post", methods={"POST"})
     *
     * @return void
     */
    public function onRegister()
    {
        list($name, $password, $passwordAgain, $email, $qq) =
            array_map(function ($k) {return $_POST[$k];}, ["name", "password", "passwordAgain", "email", "qq"]);
        // TODO: validate at server side
        $resp = new Response();
        $users = $this->getUsers();
        $resp->headers->set("content-Type", "application/json");
        if (isset($users[$name])){
            $resp->setContent(json_encode(["error" => true, "message" => "用户名已存在！"]));
        } else {
            $resp->setContent(json_encode(["success" => true, "message" => "注册成功"]));
        }

        return $resp;
    }
}
