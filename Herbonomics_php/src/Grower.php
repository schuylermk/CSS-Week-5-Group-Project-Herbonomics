<?php

class Grower
{
    private $id;
    private $name;
    private $website;
    private $email;
    private $username;
    private $password;

    function __construct($id = null, $name, $website, $email, $username, $password)
    {
        $this->id = $id;
        $this->name = $name;
        $this->website = $website;
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
    }

    function getId()
    {
        return $this->id;
    }

    function setName($new_name)
    {
        $this->name = $new_name;
    }

    function getName()
    {
        return $this->name;
    }

    function setWebsite($new_web_name)
    {
        $this->website = $new_web_name;
    }

    function getWebsite()
    {
        return $this->website;
    }

    function setUsername($new_username)
    {
        $this->username = $new_username;
    }

    function getUsername()
    {
        return $this->username;
    }

    function setEmail($new_email)
    {
        $this->email = $new_email;
    }

    function getEmail()
    {
        return $this->email;
    }

    function setPassword($new_password)
    {
        $this->password = $new_password;
    }

    function getPassword()
    {
        return $this->password;
    }

    function save()
    {
        $GLOBALS['DB']->exec("INSERT INTO growers (name, website, email, username, password) VALUES ('{$this->getName()}', '{$this->getWebsite()}', '{$this->getEmail()}', '{$this->getUsername()}', '{$this->getPassword()}');");
        $this->id = $GLOBALS['DB']->lastInsertId();
    }

    static function getAll()
    {
        $returned_growers = $GLOBALS['DB']->query("SELECT * FROM growers;");
        $growers = array();

        foreach($returned_growers as $grower) {
            $id = $grower['id'];
            $name = $grower['name'];
            $website = $grower['website'];
            $email = $grower['email'];
            $username = $grower['username'];
            $password = $grower['password'];
            $new_grower = new Grower($id, $name, $website, $email, $username, $password);
            array_push($growers, $new_grower);
        }
        return $growers;
    }

    static function deleteAll()
    {
        $GLOBALS['DB']->exec("DELETE FROM growers;");
    }

    static function findById($search_id)
    {
        $found_grower = null;
        $growers = Grower::getAll();

        foreach($growers as $grower) {
            $grower_id = $grower->getId();
            if ($grower_id == $search_id) {
                $found_grower = $grower;
            }
        }
        return $found_grower;
    }

    function update($new_name, $new_website, $new_email, $new_username, $new_password)
    {
        $GLOBALS['DB']->exec("UPDATE growers SET name = '{$new_name}', website = '{$new_website}', email = '{$new_email}', username = '{$new_username}', password = '{$new_password}' WHERE id = {$this->getId()};");
        $this->setName($new_name);
        $this->setWebsite($new_website);
        $this->setEmail($new_email);
        $this->setUsername($new_username);
        $this->setPassword($new_password);
    }

    function deleteOneGrower()
    {
        $GLOBALS['DB']->exec("DELETE FROM growers WHERE id = {$this->getId()};");
        // $GLOBALS['DB']->exec("DELETE FROM dispensaries_growers WHERE grower_id = {$this->getId()};");
    }

    static function search($search_term)
    {
       $query = $GLOBALS['DB']->query("SELECT * FROM growers WHERE name LIKE '%{$search_term}%'");
       $all_growers = $query->fetchAll(PDO::FETCH_ASSOC);
       $found_growers = array();
       foreach ($all_growers as $grower) {
           $id = $grower['id'];
           $name = $grower['name'];
           $website = $grower['website'];
           $email = $grower['email'];
           $username = $grower['username'];
           $password = $grower['password'];

           $new_grower = new Grower($id, $name, $website, $email, $username, $password);
           array_push($found_growers, $new_grower);
       }
       return $found_growers;
    }

    static function signIn($username, $password)
    {
       $all_growers = Grower::getAll();
       $user = null;
       foreach($all_growers as $grower){
           $grower_username = $grower->getUsername();
           $grower_password = $grower->getPassword();
           if($grower_username == $username && $grower_password == $password) {
               $user = $grower;
           }
       }
       return $user;
    }

    function saveId()
    {
        $_SESSION['id'] = $this->id;
        $_SESSION['type'] = "grower";
    }

    function addDispensary($dispensary_id)
    {
      $GLOBALS['DB']->exec("INSERT INTO dispensaries_growers (grower_id, dispensary_id) VALUES ({$this->getId()}, {$dispensary_id}) ;");
    }

    function getDispensaries()
    {
      $query = $GLOBALS['DB']->query("SELECT dispensaries.* FROM growers JOIN dispensaries_growers ON (growers.id = dispensaries_growers.grower_id) JOIN dispensaries ON (dispensaries_growers.dispensary_id = dispensaries.id) WHERE growers.id = {$this->getId()}; ");
      $returned_dispensaries = $query->fetchAll(PDO::FETCH_ASSOC);
      $dispensaries = array();
      foreach($returned_dispensaries as $dispensary){
         $name = $dispensary['name'];
         $website = $dispensary['website'];
         $email = $dispensary['email'];
         $username = $dispensary['username'];
         $password = $dispensary['password'];
         $id = $dispensary['id'];
         $new_dispensary = new Dispensary($name, $website, $email, $username, $password, $id);
         array_push($dispensaries, $new_dispensary);
        }
      return $dispensaries;
    }

    function checkFollow($dispensary_id)
    {
      $existing_follows = $GLOBALS['DB']->query("SELECT * FROM dispensaries_growers");
           foreach ($existing_follows as $follow) {
             if ($follow['dispensary_id'] == $dispensary_id && $follow['grower_id'] == $this->getId()) {
               return true;
             }
           }
           return false;
    }

}

 ?>
