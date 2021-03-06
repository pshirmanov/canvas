<?php

class UserController extends DefaultController
{
    /**
     * Template
     * @var object
     */
    protected $template;

    /**
     * Default action
     * @return string content
     */
    public function index()
    {
        $errors = [];

        // Auth
        if (!empty($_POST) && isset($_POST['username']) && isset($_POST['password'])) {

            $username = $_POST['username'];
            $password = $_POST['password'];

            $userInfo = UserModel::findByName($username);

            if (!$userInfo) {
                $errors[] = 'User not found';
            }

            if (!password_verify($password, $userInfo->hash)) {
                $errors[] = 'Username or password are incorrect';
            }

            if (!empty($errors)) {
                return $this->template->render('userIndex', ['errors' => $errors]);
            }

            $_SESSION['userid'] = $userInfo->id;
            $_SESSION['username'] = $userInfo->username;

            header('Location: /');
        }

        return $this->template->render('userIndex');
    }
}
