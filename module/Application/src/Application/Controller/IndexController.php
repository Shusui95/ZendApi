<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;


use Application\Entity\Pokemon;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractRestfulController
{
    protected $pokemonTable;
    protected $geoTable;
    protected $userTable;

    public function getPokemonTable(){
        if (!$this->pokemonTable){
            $sm = $this->getServiceLocator();
            $this->pokemonTable = $sm->get('Application\Model\PokemonTable');
        }
        return $this->pokemonTable;
    }

    public function getGeoTable(){
        if (!$this->geoTable){
            $sm = $this->getServiceLocator();
            $this->geoTable = $sm->get('Application\Model\GeoTable');
        }
        return $this->geoTable;
    }

    public function getUserTable(){
        if (!$this->userTable){
            $sm = $this->getServiceLocator();
            $this->userTable = $sm->get('Application\Model\UserTable');
        }
        return $this->userTable;
    }

    public function indexAction()
    {
        return new ViewModel();
//        $array = ['test' => 'test'];
//        return new JsonModel($array);
    }

    // Get All pokemon list
    // /api/pokemons/getList
    public function getListAction()
    {
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
        # code...
        $request = $this->getRequest();

        $response = $this->getResponse();

        $list = $this->getPokemonTable()->fetchAll();
        foreach ($list as $elem){

                $array = $elem->evolution;
                $elem->evolution = [];
                $this->getPokemonTable()->moreDetailsBis($array,$elem);
        }
//        echo '<pre>';
//        echo print_r($list);
//        echo '</pre>';
        return new JsonModel($list);
    }

    // Get pokemon details
    // /api/pokemons/details/:id
    public function detailsAction(){
        $pokemon = new Pokemon();
        $slug = $this->getEvent()->getRouteMatch()->getParam('pokemonId');
        $pokemon = $this->getPokemonTable()->moreDetails($slug);
        $array = $this->getGeoTable()->getUnder30minGeolocByPokemonId($pokemon->getId());
        $pokemon->affectGeolocalisation($array);

        return new JsonModel([$pokemon]);
    }

    public function geoAction(){
        $pokemon = new Pokemon();
        $slug = $this->getEvent()->getRouteMatch()->getParam('pokemonId');
        $pokemon = $this->getPokemonTable()->getPokemonInfos($slug);
        $array = $this->getGeoTable()->getUnder30minGeolocByPokemonId($pokemon->getId());
        $pokemon->affectGeolocalisation($array);
        return new JsonModel([$pokemon]);
    }
    public function geoSubmitAction(){
        $pokemon = new Pokemon();
        if ($this->getRequest()->isPost()){
            $slug = $_POST['idnational'];

            $lat = $_POST['lat'];
            $lng = $_POST['lng'];
            $pokemon = $this->getPokemonTable()->getPokemonInfos($slug);
            $array = $this->getGeoTable()->addGeoloc($pokemon->id, $lat, $lng);
        }

        return new JsonModel([]);
    }

    public function uploadAction(){
        $pokemon = new Pokemon();
        if ($this->getRequest()->isPost()){
            $slug = $_POST['id'];
            $pokemon = $this->getPokemonTable()->moreDetails($slug);
            define('UPLOAD_DIR', 'public/img/');
            $img = $_POST['image'];
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            $file = UPLOAD_DIR . $slug . '.png';
            $pokemon->setPicture(UPLOAD_DIR . $slug . '.png');
            $success = file_put_contents($file, $data);
            print $success ? $file : 'Unable to save the file.';

            $this->getPokemonTable()->updatePokemon($pokemon);
        }
        return new JsonModel([1]);
    }

    // Create pokemon
    // /api/pokemons/add
    public function addAction()
    {
        # code...
        // Fill pokemon data to create
        if ($this->getRequest()->isPost()){

            $pokemon = new Pokemon();
            $slug = intval($_POST['idnational']);
            if ($slug < 10){
                $slug = "00".strval($slug);
            }
            else if ($slug < 100){
                $slug = "0".strval($slug);
            }else{
                $slug = strval($slug);
            }
            $pokemon->setIdNational($slug);
            $pokemon->setName($_POST['name']);
            $pokemon->setDescription($_POST['description']);
            //!empty($_POST['evolution']) ? $_POST['evolution'] : null;
            //!empty($_POST['types']) ? $_POST['types'] : null;
            if (!empty($_POST['evolution'])){
                $pokemon->setEvolutionArray($_POST['evolution']);
            }
            if(!empty($_POST['types'])){
                $pokemon->setTypeArray($_POST['types']);
            }

            define('UPLOAD_DIR', 'public/img/');
            $img = $_POST['image'];
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            $file = UPLOAD_DIR . $slug . '.png';
            $pokemon->setPicture(UPLOAD_DIR . $slug . 'mini.png');
            $success = file_put_contents($file, $data);
            print $success ? $file : 'Unable to save the file.';

            $this->resize(150, UPLOAD_DIR . $slug . 'mini', $file);
            $this->resize(40, UPLOAD_DIR . $slug . 'minimini', $file);


            $this->getPokemonTable()->insertPokemon($pokemon);
            $id = $this->getPokemonTable()->getLastIdInserted();
            foreach ($pokemon->getType() as $type){
                $idtype = $this->getPokemonTable()->getType($type);
                $this->getPokemonTable()->insertType($id, $idtype);
            }

            $array = $pokemon->getEvolution();
            foreach ($array as $evol){

                $evol = substr($evol, 1, 3);

                $evol = $this->getPokemonTable()->getEvolutionId($evol);
                $this->getPokemonTable()->insertEvolution($id, $evol);
            }
        }
        return new JsonModel();
    }

    function resize($newWidth, $targetFile, $originalFile) {

        $info = getimagesize($originalFile);
        $mime = $info['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $image_create_func = 'imagecreatefromjpeg';
                $image_save_func = 'imagejpeg';
                $new_image_ext = 'jpg';
                break;

            case 'image/png':
                $image_create_func = 'imagecreatefrompng';
                $image_save_func = 'imagepng';
                $new_image_ext = 'png';
                break;

            case 'image/gif':
                $image_create_func = 'imagecreatefromgif';
                $image_save_func = 'imagegif';
                $new_image_ext = 'gif';
                break;

            default:
                throw new Exception('Unknown image type.');
        }

        $img = $image_create_func($originalFile);
        imagealphablending($img, false);
        imagesavealpha($img, true);
        list($width, $height) = getimagesize($originalFile);

        $newHeight = ($height / $width) * $newWidth;
        $tmp = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($tmp, false);
        imagesavealpha($tmp, true);
        imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        if (file_exists($targetFile)) {
            unlink($targetFile);
        }
        $image_save_func($tmp, "$targetFile.$new_image_ext");
    }

    // Update pokemon
    // /api/pokemons/update/:id
    public function updateAction()
    {
        # code...
        // Fill pokemon data to create
        if ($this->getRequest()->isPost()){

            $pokemon = new Pokemon();
            $slug = intval($_POST['idnational']);
            if ($slug < 10){
                $slug = "00".strval($slug);
            }
            else if ($slug < 100){
                $slug = "0".strval($slug);
            }else{
                $slug = strval($slug);
            }
            $pokemonArray = $this->getPokemonTable()->searchByName($_POST['name']);
            $pokemon = $pokemonArray[0];
            $pokemon->setIdNational($slug);
            $pokemon->setName($_POST['name']);
            $pokemon->setDescription($_POST['description']);
            if (!empty($_POST['evolution'])){
                $pokemon->setEvolutionArray($_POST['evolution']);
            }
            if(!empty($_POST['types'])){
                $pokemon->setTypeArray($_POST['types']);
            }
            if (!empty($_POST['image'])){
                define('UPLOAD_DIR', 'public/img/');
                $img = $_POST['image'];
                $img = str_replace('data:image/png;base64,', '', $img);
                $img = str_replace(' ', '+', $img);
                $data = base64_decode($img);
                $file = UPLOAD_DIR . $slug . '.png';
                $pokemon->setPicture(UPLOAD_DIR . $slug . '.png');
                $success = file_put_contents($file, $data);
                print $success ? $file : 'Unable to save the file.';
                $this->resize(150, UPLOAD_DIR . $slug . 'mini', $file);
                $this->resize(40, UPLOAD_DIR . $slug . 'minimini', $file);
            }

            $this->getPokemonTable()->deleteType($pokemon->id);
            foreach (array_filter($pokemon->getType()) as $type){
                $idtype = $this->getPokemonTable()->getType($type);
                $this->getPokemonTable()->insertType($pokemon->id, $idtype);
            }

            $array = array_filter($pokemon->getEvolution());
            $this->getPokemonTable()->deleteEvolution($pokemon->id);
            foreach ($array as $evol){

                $evol = substr($evol, 1, 3);

                $evol = $this->getPokemonTable()->getEvolutionId($evol);
                $this->getPokemonTable()->insertEvolution($pokemon->id, $evol);
            }
            $this->getPokemonTable()->updatePokemon($pokemon);
        }
        return new JsonModel();
    }

    // Delete pokemon
    // /api/pokemons/delete
    public function deleteAction()
    {
        # code...
        if ($this->getRequest()->isPost() ){
            $pokemon = new Pokemon();
            $pokemonArray = $this->getPokemonTable()->searchByName($_POST['name']);
            $pokemon = $pokemonArray[0];
            $slug = intval($_POST['idnational']);
            if ($slug < 10) {
                $slug = "00" . strval($slug);
            } else if ($slug < 100) {
                $slug = "0" . strval($slug);
            } else {
                $slug = strval($slug);
            }
            $pokemon->setIdNational($slug);
            $pokemon->setName($_POST['name']);
            $this->getPokemonTable()->deleteEvolution($pokemon->id);
            $this->getPokemonTable()->deleteEvolutionBis($pokemon->id);
            $this->getPokemonTable()->deleteType($pokemon->id);
            $this->getPokemonTable()->deletePokemon($pokemon->id);
        }
        return new JsonModel();
    }

    public function connexionAction(){
        $pokemon = new Pokemon();
        if ($this->getRequest()->isPost()){
            $login = $_POST['login'];
            $password = $_POST['password'];
            $pokemon = $this->getUserTable()->getUserByLoginPassword($login, $password);

        }
        return new JsonModel([$pokemon]);
    }

    public function typesAction(){
        $result = $this->getPokemonTable()->getTypes();

        return new JsonModel($result);
    }

    public function namesAction(){
        $result = $this->getPokemonTable()->getNames();

        return new JsonModel($result);
    }
    public function lastIdAction(){
        $result = $this->getPokemonTable()->getLastId();

        return new JsonModel([$result]);
    }
}
