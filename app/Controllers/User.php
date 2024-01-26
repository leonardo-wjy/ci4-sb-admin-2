<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Validation\Rules\CustomRules;

class User extends BaseController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        return view('user');
    }

    public function all() 
    {
        $payload = [
            "pageSize" => $this->request->getGet("length"),
            "currentPage" => ($this->request->getGet("start") / $this->request->getGet("length")) + 1,
            "search" => $this->request->getGet("search"),
            "sort" => $this->request->getGet("sort"),
            "sortType" => $this->request->getGet("sortType")
        ];

        $condition = [];

        $addCondition = [
            "search"        => $this->request->getGet("search"),
            "sort"          => $this->request->getGet("sort"),
            "sortType"      => $this->request->getGet("sortType")
        ];

        $limit = $this->request->getGet("length");
        $offset = $this->request->getGet("start");
        $userData = $this->userModel->getList($condition, $addCondition, $limit, $offset);

        $dataUser = [];

        $no = ($payload["pageSize"] * ($payload["currentPage"] - 1)) + 1;

        foreach ($userData['data'] as $data) {
            array_push($dataUser, [
                "no"            => $no++,
                "id"            => $data->user_id,
                "name"          => $data->name,
                "role"         => $data->role,
                "email"         => $data->email,
                "phone"         => $data->phone,
                "createdAt" => $data->createdAt ? date("d/m/Y H:i:s", strtotime($data->createdAt)) : "-",
                "updatedAt" => $data->updatedAt ? date("d/m/Y H:i:s", strtotime($data->updatedAt)) : "-"
            ]);
        }

        $data = [
            "draw"              => intval($this->request->getGet("draw")),
            "recordsTotal"      => $userData['totalData'],
            "recordsFiltered"   => $userData['totalFilteredData'],
            "data"              => $dataUser,
        ];

        echo json_encode($data);
        return;
    }

    public function create()
    {
        try {
            $rules = [
                "name" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Nama tidak boleh kosong'
                    ]
                ],
                "role" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Role tidak boleh kosong'
                    ]
                ],
                "email" => [
                    "rules" => "permit_empty|valid_email|is_unique[user.email]",
                    'errors' => [
                        'valid_email' => 'Email harus valid',
                        'is_unique' => 'Email sudah ada'
                    ]
                ],
                "phone" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Phone tidak boleh kosong'
                    ]
                ],
                "passwd" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Password tidak boleh kosong'
                    ]
                ],
            ];

            if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]],
                ];
                echo json_encode($data);
                return;
            }

            if ($this->validate($rules)) {
                $values = [
                    "name" => $this->request->getPost("name"),
                    "role" => $this->request->getPost("role"),
                    "email" => $this->request->getPost("email"),
                    "phone" => $this->request->getPost("phone"),
                    "password" => password_hash($this->request->getPost("passwd"), PASSWORD_BCRYPT)
                ];

                $save = $this->userModel->insert($values);
                if ($save > 0) {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil disimpan"
                    ];
                    echo json_encode($data);
                } else {
                    $message = 'Data Gagal Disimpan';
                    $data = [
                        "status"            => false,
                        "message"    => $message
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

    public function update($id)
    {
        try {
            $rules = [
                "name_edit" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Nama tidak boleh kosong'
                    ]
                ],
                "role_edit" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Role tidak boleh kosong'
                    ]
                ],
                "phone_edit" => [
                    "rules" => "required",
                    'errors' => [
                        'required' => 'Phone tidak boleh kosong'
                    ]
                ]
            ];

            if (!$this->validate($rules)) {
                $errorList = $this->validator->getErrors();
                $data = [
                    "status"    => false,
                    "message"   => $errorList[array_keys($errorList)[0]],
                ];
                echo json_encode($data);
                return;
            }

            if ($this->validate($rules)) {
                $results = $this->userModel->checkId($id);

                if (count($results) > 0) {
                    if($this->request->getPost("passwd_edit"))
                    {
                        $values = [
                            "name" => $this->request->getPost("name_edit"),
                            "role" => $this->request->getPost("role_edit"),
                            "phone" => $this->request->getPost("phone_edit"),
                            "password" => password_hash($this->request->getPost("passwd_edit"), PASSWORD_BCRYPT)
                        ];

                        $condition = [
                            'user_id' => $id
                        ];
    
                        $response = $this->userModel->where($condition)->set($values)->update();

                        if($response)
                        {
                            $data = [
                                "status"            => true,
                                "message"   => "Data Berhasil diubah"
                            ];
                            echo json_encode($data);
                        }
                        else
                        {
                            $message = 'Data Gagal Disimpan';
                            $data = [
                                "status"            => false,
                                "message"    => $message
                            ];
                            echo json_encode($data);  
                        }
                    }
                    else
                    {
                        $values = [
                            "name" => $this->request->getPost("name_edit"),
                            "role" => $this->request->getPost("role_edit"),
                            "phone" => $this->request->getPost("phone_edit")
                        ];

                        $condition = [
                            'user_id' => $id
                        ];

                        $response = $this->userModel->where($condition)->set($values)->update();

                        if($response)
                        {
                            $data = [
                                "status"            => true,
                                "message"   => "Data Berhasil diubah"
                            ];
                            echo json_encode($data);
                        }
                        else
                        {
                            $message = 'Data Gagal Disimpan';
                            $data = [
                                "status"            => false,
                                "message"    => $message
                            ];
                            echo json_encode($data);  
                        }
                    }
                }
                else
                {
                    $message = 'Data Gagal Disimpan';
                    $data = [
                        "status"            => false,
                        "message"    => $message
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

    public function remove() 
    {
        try {
            $results = $this->userModel->checkId($this->request->getPost("id"));
            if (count($results) > 0) 
            {
                $values = [
                    "deletedAt" => date("Y-m-d H:i:s")
                ];

                $condition = [
                    'user_id' => $this->request->getPost("id")
                ];

                $response = $this->userModel->where($condition)->set($values)->update();

                if($response)
                {
                    $data = [
                        "status"            => true,
                        "message"   => "Data Berhasil dihapus"
                    ];
                    echo json_encode($data);
                }
                else
                {
                    $message = 'Data Gagal Dihapus';
                    $data = [
                        "status"            => false,
                        "message"    => $message
                    ];
                    echo json_encode($data);  
                }
            }
            else 
            {
                $message = 'Data Gagal Dihapus';
                $data = [
                    "status"            => false,
                    "message"    => $message
                ];
                echo json_encode($data);  
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
}
