<?php

class MainController extends DefaultController
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
        $count_items_on_page = $this->config['pagination']['count_items_on_page'];

        $allrecords = ImageModel::countImages();
        $gallery = ImageModel::findAll(0, $count_items_on_page);

        $data = [];
        $data['gallery'] = $gallery;

        if ($allrecords > $count_items_on_page) {
            $data['pagination'] = true;
        }

        return $this->template->render('index', $data);
    }

    /**
     * Action save canvas
     * @return json
     */
    public function save()
    {
        if (isset($_SESSION['userid']) && isset($_POST['image']) && isset($_POST['password'])) {
            $uploadDir = 'upload/';
            $path = SYSPATH . $uploadDir;
            $fileName = uniqid() . '.png';

            $img = str_replace('data:image/png;base64,', '', $_POST['image']);
            $img = str_replace(' ', '+', $img);

            file_put_contents($path . $fileName, base64_decode($img));

            $data = [];
            $data['userid'] = $_SESSION['userid'];
            $data['name'] = $fileName;
            $data['hash'] = System::crypt($_POST['password']);

            $sql = ImageModel::add($data);

            $json = [];
            $json['error'] = (!$sql) ? true : false;

            if ($sql) {
                $json['image'] = DIRECTORY_SEPARATOR . $uploadDir . $fileName;
                $json['id'] = ImageModel::$lastInsertId;
            }

            static::json($json);
        }
    }

    /**
     * Action get access for edit canvas
     * @return json
     */
    public function access()
    {
        if (isset($_SESSION['userid']) && isset($_POST['password']) && isset($_POST['imageid'])) {
            $imageInfo = ImageModel::checkAccess($_POST['imageid']);

            $check = false;
            $password = $_POST['password'];

            if (password_verify($password, $imageInfo['hash'])) {
                $check = true;
            }

            $json = [];
            $json['error'] = !$check;

            if ($check) {
                $uploadDir = 'upload/';
                $path = SYSPATH . $uploadDir . $imageInfo['name'];
                $imageData = file_get_contents($path);

                $json['image'] = 'data:image/png;base64,' . base64_encode($imageData);
            }

            static::json($json);
        }
    }

    /**
     * Action get images
     * @return void
     */
    public function getimages()
    {
        if (isset($_SESSION['userid'])) {
            $currentPage = (intval($_POST['page']) <= 1) ? 1 : intval($_POST['page']);
            $allrecords = ImageModel::countImages();
            $count_items_on_page = $this->config['pagination']['count_items_on_page'];

            $start = $count_items_on_page * ($currentPage - 1);

            $show = ($allrecords >= $start + $count_items_on_page + 1) ? true : false;

            $json = [];
            $json['button'] = $show;
            $json['path'] = '/upload/';
            $json['currentpage'] = $currentPage;
            $json['images'] = ImageModel::findAll($start, $count_items_on_page);

            static::json($json);
        }
    }

    /**
     * Send json content
     * @param  array $array json
     * @return void
     */
    protected static function json($array)
    {
        header('Content-type: application/json');
        echo json_encode($array);
        exit();
    }
}