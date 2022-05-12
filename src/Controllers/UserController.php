<?php

namespace App\Controllers;

use Exception;
use App\Helpers\HashPassword;
use App\Models\UserModel;
use App\Helpers\JwtToken;
use Rakit\Validation\Validator;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use App\Helpers\Registry;

class UserController extends BaseController
{
    const DEFAULT_ROLES = ['client'];

    public function store()
    {
        $defaultRoles = ['client'];
        $user = $this->auth();
        $userAdmin = in_array('admin', $user['roles'] ?? []);

        if ($user && !$userAdmin) {
            return $this->errorForbidden();
        }

        $userModel = new UserModel();
        $validator = $this->request->getValidator();
        $postData = [
            'email' => $this->request->post('email'),
            'password' => $this->request->post('password'),
            'name' => $this->request->post('name'),
            'surname' => $this->request->post('surname'),
            'phone' => $this->request->post('phone'),
            'roles' => $this->request->post('roles')
        ];
        if (empty($postData['roles'])) {
            $postData['roles'] = $defaultRoles;
        }
        $rules =  [
            'email' => [
                'required',
                'email',
                'max:255',
                function ($email) use ($userModel) {
                    $userExists = $userModel->isUserEmailExists($email);
                    return $userExists ? "Имейл адресът е зает." : true;
                }
            ],
            'password' => 'required|min:6|max:255',
            'name' => 'required|min:2|max:255',
            'surname' => 'required|min:2|max:255',
            'phone' => [
                'required',
                'min:2',
                'max:15',
                'regex:/^([00]{2}[0-9]{1,13}|[+]{1}[0-9]{1,14})$/'
            ],
            'roles' => [
                function($postRoles) use ($userModel, $userAdmin) {
                    if (!$userAdmin) {
                        return true;
                    }

                    $postRoles = (array) $postRoles;
                    $idsPostRoles = $userModel->getRolesByName($postRoles);

                    if (count($idsPostRoles) !== count($postRoles)) {
                        return "Ролите не са коректни.";
                    }

                    return true;
                }
            ]
        ];
        $validation = $validator->make($postData, $rules);
        $validation->validate();

        if ($validation->fails()) {
            return $this->errorBadRequest($this->request->getErrorsByField($validation->errors()));
        } else {
            $user = $userModel->storeUser(
                $postData['name'],
                $postData['surname'],
                $postData['email'],
                $postData['password'],
                $postData['phone'],
                $userAdmin ? $postData['roles'] : $defaultRoles
            );
            return $this->response
                ->setCode(201)
                ->setContent($user);
        }
    }

    public function listUsers()
    {
        $user = $this->auth();
        if (!$user) {
            return $this->errorUnauthorized();
        }
        if (!in_array('admin', $user['roles'] ?? [])) {
            return $this->errorForbidden();
        }
        $userModel = new UserModel();
        $users = $userModel->listUsers();
        return $users;
    }

    public function show(int $id)
    {
        $userAuth = $this->auth();
        $userModel = new UserModel();

        if (!$userAuth) {
            return $this->errorUnauthorized();
        }

        if (!in_array('admin', $userAuth['roles'] ?? [])) {
            return $this->errorForbidden();
        }

        if (!in_array('client', $userAuth['roles'] ?? [])) {
            $userClient = $userModel->showUser($userAuth['id']);

        }

        $user = $userModel->showUser($id);
        if ($user) {
            return [
                'id' => $user['id'],
                'email' => $user['email'],
                'name' => $user['name'],
                'surname' => $user['surname'],
                'phone' => $user['phone'],
                'roles' => $user['roles'],
                'created' => date('c', strtotime($user['created'])),
            ];
        }

        return $this->errorNotFound();
    }

    public function update(int $id)
    {
        $userAuth = $this->auth();
        $userAdmin = in_array('admin', $userAuth['roles'] ?? []);
        $userModel = new UserModel();

        if (!$userAuth) {
            return $this->errorUnauthorized();
        }

        if (!in_array('admin', $userAuth['roles'] ?? []) && $id != $userAuth['id']) {
            return $this->errorForbidden();
        }

        $validator = $this->request->getValidator();
        $postData = [
            'email' => $this->request->post('email'),
            'password' => $this->request->post('password'),
            'name' => $this->request->post('name'),
            'surname' => $this->request->post('surname'),
            'phone' => $this->request->post('phone'),
            'roles' => $this->request->post('roles')
        ];

        $rules =  [
            'email' => [
                'email',
                'max:255',
                function ($email) use ($userModel, $id) {
                    $userExists = $userModel->isUserEmailExists($email, $id);
                    return $userExists ? "Имейл адресът е зает." : true;
                }
            ],
            'password' => 'min:6|max:255',
            'name' => 'min:2|max:255',
            'surname' => 'min:2|max:255',
            'phone' => [
                'min:2',
                'max:15',
                'regex:/^([00]{2}[0-9]{1,13}|[+]{1}[0-9]{1,14})$/'
            ],
            'roles' => [
                function($postRoles) use ($userModel, $userAdmin) {
                    if (!$userAdmin) {
                        return true;
                    }

                    $postRoles = (array) $postRoles;
                    $idsPostRoles = $userModel->getRolesByName($postRoles);

                    if (count($idsPostRoles) !== count($postRoles)) {
                        return "Ролите не са коректни.";
                    }

                    return true;
                }
            ]
        ];
        $validation = $validator->make($postData, $rules);
        $validation->validate();

        if ($validation->fails()) {
            return $this->errorBadRequest($this->request->getErrorsByField($validation->errors()));
        }

        $user = $userModel->showUser($id);
        if (!$user) {
            return $this->errorNotFound();
        }

        $result = array_merge($user, array_filter($postData));

        $userModel->updateUser(
            $result['id'],
            $result['name'],
            $result['surname'],
            $result['email'],
            $result['password'] ?? null,
            $result['phone'],
            $userAdmin ? $postData['roles'] : null,
            $userAuth['id']
        );

        return $userModel->showUser($id);
    }

    public function showProfile()
    {
        $user = $this->auth();

        if (!$user) {
            return $this->errorUnauthorized();
        }

        $userModel = new UserModel();
        $userProfile = $userModel->showUser($user['id']);

        return $userProfile;
    }

    public function getToken()
    {
        $validator = $this->request->getValidator();
        $validation = $validator->validate([
            'email' => $this->request->post('email'),
            'password' => $this->request->post('password'),
        ], [
            'email' => 'required|email|max:255',
            'password' => 'required|min:6',
        ]);

        if ($validation->fails()) {
            return $this->errorBadRequest($this->request->getErrorsByField($validation->errors()));
        }

        $validData = $validation->getValidData();

        $userModel = new UserModel();
        $user = $userModel->findByEmail($validData['email']);
        if (!$user) {
            return $this->errorBadRequest([
                'email' => ['Невалидна парола или Email'],
            ]);
        }
        $pass = new HashPassword();
        $validPass  = $pass->isValid($validData['password'] ?? '', $user['password'] ?? '');

        if ($validPass) {
            $jwt = new JwtToken(
                Registry::get('jwt.private'),
                Registry::get('jwt.public'),
                Registry::get('jwt.algorithm')
            );
            $id = $user['id'] ?? '';
            $roles = $user['roles'] ?? '';
            $userJwt = $jwt->create(['id' => $id]);
            $this->response
                ->setCode(200)
                ->setContent([
                    'token' => $userJwt,
                    'user'=> [
                        'id' => $id,
                        'email' => $user['email'],
                        'name' => $user['name'],
                        'surname' => $user['surname'],
                        'phone' => $user['phone'],
                        'roles' => $roles,
                        'created' => $user['created'],
                    ]
                ]);
        } else {
            return $this->errorBadRequest([
                'email' => ['Невалидна парола или Email'],
            ]);
        }
        return $this->response->getOutput();
    }

    public function destroy(int $id)
    {
        $userAuth = $this->auth();
        $userModel = new UserModel();

        if (!$userAuth) {
            return $this->errorUnauthorized();
        } else if (!in_array('admin', $userAuth['roles'] ?? [])) {
            return $this->errorForbidden();
        } else if (!$id)  {
            return $this->errorNotFound();
        } else {
            if (!$userModel->showUser($id)) {
                return $this->errorNotFound();
            }

            if ($userModel->deleteUser($id)) {
                return $this->response->setCode(204);
            }
        }
    }

    public function forgot()
    {
        $userModel = new UserModel();
        $validator = $this->request->getValidator();
        $email = $this->request->post('email');

        $validation = $validator->validate([
            'email' => $email
        ], [
            'email' => [
                'required',
                'email',
                'max:255',
                function ($email) use ($userModel) {
                    $userExists = $userModel->isUserEmailExists($email);
                    return $userExists ?  true :  "Въведеният адрес не е наличен.";
                }
            ],
        ]);

        if ($validation->fails()) {
            return $this->errorBadRequest($this->request->getErrorsByField($validation->errors()));
        }

        $user = $userModel->findByEmail($email);

        $token = sha1(mt_rand());
        $tokenExpiredAt = time() + 60 * 60 * 2;
        $link = Registry::get('app.url') . '/reset/' . $token . '/' . $email;

        $userModel->updateUserToken($user['id'], $token, $tokenExpiredAt);

        $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug = false;
            $mail->isSMTP();

            $mail->Host       = Registry::get('mail.hostname');
            $mail->SMTPAuth   = true;
            $mail->Username   = Registry::get('mail.username');
            $mail->Password   = Registry::get('mail.password');
            $mail->SMTPSecure = Registry::get('mail.secure');
            $mail->Port       = Registry::get('mail.port');
            $mail->CharSet    = Registry::get('mail.charset');

            // Recipients
            $mail->setFrom(Registry::get('mail.from.address'), Registry::get('mail.from.name'));
            $mail->addAddress($user['email'], $user['name'] . ' ' . $user['surname']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Заявка за нова парола от Moonlight';
            $mail->AltBody = "Здравейте, заявили сте нова парола от Moonlight.\n\nМоля, последвайте следния линк: ${link}";
            $mail->Body = "Здравейте, заявили сте нова парола от <strong>Moonlight</strong>.<br />"
                . "<p>Моля, последвайте следния линк: <a href=\"${link}\" target=\"_blank\">${link}</a></p>"
                . "<p>Екипът на Moonlight</p>";

            $mail->send();
        } catch (Exception $e) {
            return $this->errorInternalServerError('Възстановяването на паролата не беше успешно.');
        }

        return $this->response->setCode(204);
    }

    public function reset()
    {
        $userModel = new UserModel();
        $validator = $this->request->getValidator();

        $postData = [
            'email' => $this->request->post('email'),
            'token' => $this->request->post('token'),
            'password' => $this->request->post('password'),
        ];
        $validation = $validator->validate($postData, [
            'email' => [
                'required',
                'email',
                'max:255',
                function ($email) use ($userModel) {
                    $userExists = $userModel->isUserEmailExists($email);
                    return $userExists ?  true :  "Въведеният адрес не е наличен.";
                }
            ],
            'password' => 'required|min:6|max:255',
            'token' => [
                'required',
                'max:40',
                function($token) use ($userModel, $postData) {
                    $isTokenValid = $userModel->isForgotTokenValid($token, $postData['email']);
                    return $isTokenValid ?  true :  "Въведеният токен е невалиден.";
                }
            ]
        ]);

        if ($validation->fails()) {
            return $this->errorBadRequest($this->request->getErrorsByField($validation->errors()));
        }

        $user = $userModel->findByEmail($postData['email']);

        $userModel->updateUser(
            $user['id'],
            $user['name'],
            $user['surname'],
            $user['email'],
            $postData['password'],
            $user['phone'],
            null,
            $user['id']
        );

        return $this->response->setCode(204);
    }
}
