<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    public function jsonResponse($arr) {
        $resp = new Response();
        $resp->headers->set("Content-Type", 'application/json');
        $resp->setContent(\json_encode($arr));
        return $resp;
    }
    public function genToken()
    {
        // return
        // substr(join("", shuffle(explode("", '123456789abcdefghijklmnopqrstuvwxyz'))), 0, 24);
        return substr(\str_shuffle('123456789abcdefghijklmnopqrstuvwxyz'), 0, 24);
    }
    public function readUsers()
    {
        $file = __DIR__ . "/../users.data";
        $handler = fopen($this->file, "a+");
        if (!$handler) {
            exit("the users.data can not read or create");
        }
        // read users
        rewind($handler);
        if ($size = filesize($this->file)) {
            $content = fread($handler, filesize($this->file) || 0);

        } else {
            $content = "";
        }
        fclose($handler);
        // parse string
        if ($content === "") {
            return [];
        }
        $arr = explode("\n", $content);
        $i = 0;
        while ($i < count($arr)) {
            $this->users[$arr[i]] = $arr[i + 1];
            $i += 2;
        }
        return $arr;
    }
    public function writeUser($name, $pw)
    {
        $file = __DIR__ . "/../users.data";
        file_put_contents($file, "$name\n$pw\n", FILE_APPEND);
    }
    /**
     * @Route("/login", name="login", methods={"GET"})
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
        // $request = Request::createFromGlobals();
        // $name = $request->request->get("name");
        // $password = $request->request->get("password");
        // if (!isset($users, $name)) {
        //     $error = "此用户未注册";
        // } else if ($users[$name] !== $password) {
        //     $error = "密码不正确";
        // }
        // if ($error) {
        //     return $this->render("/login/index.html.twig", [
        //         "error" => $error,
        //     ]);
        // } else {
        //     // go to the home page
        //     return $this->render("/index.html.twig");
        // }
        $request = Request::createFromGlobals();
        $name = $request->request->get('name');
        $pwd = $request->request->get('password');
        $userEntity = $this->getDoctrine()->getRepository(User::class);
        $user = $userEntity->findOneBy(['name' => $name]);
        if (!$user) {
            // have not registered.
            return $this->jsonResponse(['error' => 'you have not registered yet.']);
        }
        if ($user->getPassword() !== $pwd) {
            return $this->jsonResponse(['error' => 'password not correct.']);
        }
        // success!
        $token = $this->genToken();
        $user->setToken($token);
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($user);
        $manager->flush();

        // set cookie
        $resp = new Response();
        $resp->headers->setCookie(new Cookie('token', $token));
        return $resp; 
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
        // list($name, $password, $passwordAgain, $email, $qq) =
        //     array_map(function ($k) {return $_POST[$k];}, ["name", "password", "passwordAgain", "email", "qq"]);
        // // TODO: validate at server side
        // $resp = new Response();
        // $users = $this->getUsers();
        // $resp->headers->set("content-Type", "application/json");
        // if (isset($users[$name])){
        //     $resp->setContent(json_encode(["error" => true, "message" => "用户名已存在！"]));
        // } else {
        //     $resp->setContent(json_encode(["success" => true, "message" => "注册成功"]));
        // }

        // return $resp;
        list($name, $password, $passwordAgain, $email, $qq) =
            array_map(function ($k) {return $_POST[$k];}, ["name", "password", "passwordAgain", "email", "qq"]);
        if ($password !== $passwordAgain) {
            return $this->jsonResponse(['error' => "passwords do not match"]);
        }
        $userEntity = $this->getDoctrine()->getRepository(User::class);
        if ($old = $userEntity->findOneBy(['name' => $name])) {
            return $this->jsonResponse(['error' => 'username already exists']);
        }
        // can register!
        $manager = $this->getDoctrine()->getManager();

        $user = new User();
        $user->setName($name);
        $user->setEmail($email);
        $user->setPassword($password);
        $user->setQq($qq);
        $manager->persist($user);
        $manager->flush();
        return new Response($user->getId());
    }
    /**
     * @Route("/logout", methods={"POST"})
     *
     * @return void
     */
    public function logout() {
        // remote the token
        $req = Request::createFromGlobals();

        $token = $req->cookies->get('token');
        if(isset($token)) {
            $manager = $this->getDoctrine()->getManager();
            $userEntity = $this->getDoctrine()->getRepository(User::class);
            $user = $userEntity->findOneBy(['token' => $token]);
            if(!isset($user)) {
                // token has expired.
                return new Response("Need to relogin.");
            }
            $manager->persist($user);
            $manager->flush();
            return new Response("logged out.");
        }
        return new Response("you have not login yet.");
    }
}
