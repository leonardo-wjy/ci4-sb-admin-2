<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Login extends BaseController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        if (!empty(is_login())) {
            return redirect()->to("home");
        }

        return view('login');
    }

    public function login() 
    {
        try {
            $rules = [
                "email" => [
                    "rules" => "required|valid_email"
                ],
                "password" => [
                    "rules" => "required"
                ]
            ];
    
            if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]]
                ];
                echo json_encode($data);
                return;
            } else {
                $email      = $this->request->getPost("email");
                $password   = $this->request->getPost("password");

                $results = $this->userModel->login($email, md5($password));

                if($results) {
                    $session = (object) [
                        "isLogin" => true,
                        "user_id" => $results[0]["user_id"],
                        "email" => $results[0]["email"],
                        "name" => $results[0]["name"],
                        "role" => $results[0]["role"],
                    ];

                    session()->setTempdata("login", $session, 36000);
                    
                    $data = [
                        "status"            => true,
                        "message"    => "Login Berhasil"
                    ];
                    echo json_encode($data);
                } else {
                    $data = [
                        "status"            => false,
                        "message"    => "Login Gagal!"
                    ];
                    echo json_encode($data);
                }
            }
        } catch (\Exception $e) {
            $data = [
                "status"            => false,
                "message"    => $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine()
            ];
            echo json_encode($data);
        }
        return;
    }

    public function logout() 
    {
        session()->destroy();

        return redirect()->to("/");
    }
}