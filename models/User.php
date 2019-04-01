<?php

/**
 * Класс User - модель для работы с пользователями
 */
class User
{


    /**
     * Проверяем существует ли пользователь в телеграме
     * @param string $email <p>E-mail</p>
     * @param string $password <p>Пароль</p>
     * @return mixed : integer user id or false
     */
    public static function checkUserData($user_id)
    {
        // Соединение с БД
        $db = Db::getConnection();

        // Текст запроса к БД
        $sql = 'SELECT * FROM telegram_users WHERE user_id = :user_id';

        // Получение результатов. Используется подготовленный запрос
        $result = $db->prepare($sql);
        $result->bindParam(':euser_id', $uset_id, PDO::PARAM_INT);
        $result->execute();

        // Обращаемся к записи
        $user = $result->fetch();

        if ($user) {
            // Если запись существует, возвращаем id пользователя
            return $user['id'];
        }
        return false;
    }


    public static function getInstagramFollowers($telegram_user_id)
    {
        // Соединение с БД        
        $db = Db::getConnection();

        // Текст запроса к БД
        $sql = 'SELECT * FROM telegram_unstagram WHERE telegram_user_id = :telegram_user_id';

        // Получение результатов. Используется подготовленный запрос
        $result = $db->prepare($sql);
        $result->bindParam(':telegram_user_id', $telegram_user_id, PDO::PARAM_STR);
        $result->execute();

        // Получение и возврат результатов

        $followers = array();
        while ($row = $result->fetch()) {
            $followers[] = $row['instagram_user'];;
        }

        return $followers;
    }

    public static function addInstaFolow($t_user_id, $inst_name)
    {
        // Соединение с БД
        $db = Db::getConnection();

        // Текст запроса к БД
        $sql = 'UPDATE telegram_instagram
            SET telegram_user_id = :name, 
                phone = :phone, 
                email = :email, 
                position = :position, 
                description = :description WHERE id = :id';


        // Получение и возврат результатов. Используется подготовленный запрос
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);

        // Указываем, что хотим получить данные в виде массива
        $result->setFetchMode(PDO::FETCH_ASSOC);
        $result->execute();

        return $result->fetch();
    }

    public static function getUsers()
    {
        // Соединение с БД
        $db = Db::getConnection();

        // Текст запроса к БД
        $sql = "SELECT id, name, email, phone, position, description, image  FROM user WHERE role = 'admin'";

        // Используется подготовленный запрос
        $result = $db->prepare($sql);

        // Выполнение коменды
        $result->execute();

        // Получение и возврат результатов
        $i = 0;
        $productsList = array();
        while ($row = $result->fetch()) {
            $productsList[$i]['id'] = $row['id'];
            $productsList[$i]['name'] = $row['name'];
            $productsList[$i]['email'] = $row['email'];
            $productsList[$i]['phone'] = $row['phone'];
            $productsList[$i]['position'] = $row['position'];
            $productsList[$i]['description'] = $row['description'];
            $productsList[$i]['image'] = $row['image'];
            $i++;
        }
        return $productsList;
        // Получение и возврат результатов
    }

    public static function updateUsers($id, $options)
    {
        AdminBase::checkAdmin();
        $conacts = array();
        // Соединение с БД
        $db = Db::getConnection();
        foreach ($options as $option_key => $option) {
            foreach ($option as $key => $value) {
                $conacts[$key][$option_key] = $value;
            }
        }

        foreach ($_FILES['image'] as $option_key => $option) {
            foreach ($option as $key => $value) {
                if($option_key == 'tmp_name'){
                    // Проверим, загружалось ли через форму изображение
                    if (is_uploaded_file($_FILES["image"]["tmp_name"][$key])) {
                        $img_url = "/img/contact_foto/".($key).".jpg";
                        if(!file_exists(ROOT."/img/contact_foto")) {
                            mkdir(ROOT."/img/contact_foto");
                        }
                        $conacts[$key]['image'] = $img_url;
//                        unlink($_SERVER['DOCUMENT_ROOT'] . $img_url);
                        // Если загружалось, переместим его в нужную папке, дадим новое имя
                        move_uploaded_file($_FILES["image"]["tmp_name"][$key], $_SERVER['DOCUMENT_ROOT'] . $img_url);

                    }
                }
            }
        }
        
        

        foreach ($conacts as $conact) {
            if(isset($conact['image'])) {
                $sql = 'UPDATE user
            SET name = :name, 
                phone = :phone, 
                email = :email, 
                position = :position, 
                description = :description, 
                image = :image WHERE id = :id';
            } else {
                $sql = 'UPDATE user
            SET name = :name, 
                phone = :phone, 
                email = :email, 
                position = :position, 
                description = :description WHERE id = :id';
            }

            $result = $db->prepare($sql);
            $result->bindParam(':id', $conact['id'], PDO::PARAM_INT);
            $result->bindParam(':name', $conact['name'], PDO::PARAM_STR);
            $result->bindParam(':phone', $conact['phone'], PDO::PARAM_STR);
            $result->bindParam(':email', $conact['email'], PDO::PARAM_STR);
            $result->bindParam(':position', $conact['position'], PDO::PARAM_STR);
            $result->bindParam(':description', $conact['description'], PDO::PARAM_STR);
            if (isset($conact['image'])) {
                $result->bindParam(':image', $conact['image']);
            }
			$result->execute();

            $result_sql =  $result->execute();
        }
        return $result_sql ? true : false;
    }

}
