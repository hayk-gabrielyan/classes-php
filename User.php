<?php

session_start();

class User {
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;
    public $bdd;

    public function __construct() {
        $this ->bdd = new mysqli('localhost', 'root', '', 'classes');
    }

    
    public function register($login, $password, $email, $firstname, $lastname) {
        $requete = "INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES ('$login', '$password', '$email', '$firstname', '$lastname')";
        $exec_requete = $this->bdd->query($requete);
        return $affichage = "inscription effectué";
    }



    
    public function connect($login, $password) {
        // Vérifiez la connexion
        if($login !== "" && $password !== ""){
            //requete pour selectionner  l'utilisateur qui a pour login et mot de passe les identifiants qui ont été entrées
            $requete = "SELECT count(*) FROM utilisateurs where 
                    login = '".$login."'";// and password = '".$password."' ";
            $exec_requete = $this->bdd->query($requete);
            $reponse = mysqli_fetch_array($exec_requete);
            $count = $reponse['count(*)'];

            if($count!=0){ // nom d'utilisateur correct
                $requete = "SELECT password FROM utilisateurs where login = '".$login."'";
                $exec_requete = $this->bdd->query($requete);
                $reponse= mysqli_fetch_array($exec_requete);
                
                $_SESSION["login"]=$login;
                $_SESSION["password"]=$password;
                $affichage = "connexion réussie";
                return $affichage;
                
            }else{
                $affichage = "connexion echoué: utilisateur inexistant";
                return $affichage;
            }
            
        }
    }
    
    public function disconnect() {
        // Déconnectez-vous de la base de données
        session_unset();
        session_destroy();
        $affichage="Déconnecté de la base de données";
        return $affichage;
      }
      
    // public function __destruct(){
    //     echo $this->disconnect();
    // }

    public function delete() {
        //  //récuperation de id_utilisateur de la db
        $login=$_SESSION["login"];
        $requete = ("SELECT id FROM utilisateurs WHERE `login` = '$login' ");
        $exec_requete = $this->bdd->query($requete);
        $reponse_fetch_array = $exec_requete -> fetch_array();
        $user_id = $reponse_fetch_array['id'];

        $requete2 = "DELETE FROM `utilisateurs` WHERE `utilisateurs`.`id` = '$user_id'";
        $exec_requete2 = $this->bdd->query($requete2);
        $affichage = "suppression de user: $user_id effectué";
        return $affichage;
    }

    
    public function update($login, $password, $email, $firstname, $lastname){
        $login_sess=$_SESSION["login"];
        // $catchInfos = $this->bdd->query("SELECT login, password, email, firstname, lastname FROM `utilisateurs` WHERE `login` = '$login_sess' ");
        // $affichage = $catchInfos -> fetch_all();
        
        $requete3 = $this->bdd->query("UPDATE utilisateurs SET login = '$login' , password = '$password', email = '$email', firstname = '$firstname', lastname = '$lastname' WHERE login = '$login_sess'");
        $exec_requete3 = $this->bdd->query($requete3);
        $affichage = "<br>".'Les modifications ont été enregistrés si connexion est réussi'."<br>";
        echo "<br>"."login de la session est : $login_sess";
        return $affichage;
    
    }
    
}

// Créer un nouvel utilisateur
$user = new User('john.doe', 'john.doe@example.com', 'John', 'Doe');

// // Régistration dans la bdd
// echo $user->register("john.doe", "pass",  "john.doe@example.com", "John", "Doe")."<br>";

////Connexion
echo $user->connect("john.doe", "pass");
//echo "<br>".$_SESSION["login"]."<br>";
// echo $user->disconnect();
// echo "<br>".$_SESSION["login"]."<br>";

////Déconnexion de l'utilisateur'
//echo $user->disconnect();
// echo "<br>";

////suppression de l'utilisateur qui est connecté
// echo $user->delete();

// //mise à jour des informations d'utilisateur qui est connecté
// echo $user->update('john.doe','pass', 'john.doe@example.com', 'John', 'Doe');