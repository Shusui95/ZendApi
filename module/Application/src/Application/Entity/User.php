<?php
/**
 * Created by PhpStorm.
 * User: jeremy.marchand
 * Date: 09/06/2017
 * Time: 10:02
 */
namespace Application\Entity;
class User{
    public $id;
    public $login;
    public $password;

    /**
     * User constructor.
     * @param $id
     * @param $login
     * @param $password
     */
    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param mixed $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function extract(){

        return [
            'id' => $this->id,
            'login' => $this->login,
            'password' => $this->password
        ];
    }

    public function hydrate(array $data){

        $this->id = (isset($data['id']) ? intval($data['id']) : null);
        $this->login = (isset($data['login']) ? $data['login'] : null);
        $this->password = (isset($data['password']) ? $data['password'] : null);
    }

}