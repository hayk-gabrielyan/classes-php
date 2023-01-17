<?php

session_start();

class User {
    private $id;
    public $login;
    public $password;
    public $email;
    public $firstname;
    public $lastname;
    public $bdd;

    //Constructeur
    public function __construct() {
        //connexion à la bdd
        $this->bdd = new mysqli('localhost', 'root', '', 'classes');
    
        // Vérification de la connexion
        if (isset($_SESSION['user'])){
            $this->id = $_SESSION['user']['id'];
            $this->login = $_SESSION['user']['login'];
            $this->password = $_SESSION['user']['password'];
            $this->email = $_SESSION['user']['email'];
            $this->firstname = $_SESSION['user']['firstname'];
            $this->lastname = $_SESSION['user']['lastname'];
        }
    }

    //Inscription
    public function register($login, $password, $email, $firstname, $lastname) {
        $requete = "INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES ('$login', '$password', '$email', '$firstname', '$lastname')";
        $this->bdd->query($requete);
        return "inscription effectué";
    }


    //Connexion
    public function connect($login, $password) {
        // Vérifiez la connexion
        if($login !== "" && $password !== ""){
            //requete pour selectionner  l'utilisateur qui a pour login et mot de passe les identifiants qui ont été entrées
            $requete = "SELECT count(*) FROM utilisateurs WHERE login ='".$login."' AND password = '".$password."' ";
            $exec_requete = $this->bdd->query($requete);
            $reponse = mysqli_fetch_array($exec_requete);
            $count = $reponse['count(*)'];

            if($count!=0){ // nom d'utilisateur correct
                    $requete3 = "SELECT * FROM utilisateurs WHERE login = '".$login."'";
                    $exec_requete3 = $this->bdd->query($requete3);
                    $reponse3= mysqli_fetch_array($exec_requete3);
                    //var_dump($reponse3);

                    $this->id = $reponse3['id'];
                    $this->login = $reponse3['login'];
                    $this->password = $reponse3['password'];
                    $this->email = $reponse3['email']; 
                    $this->firstname = $reponse3['firstname'];
                    $this->lastname = $reponse3['lastname'];
                    $_SESSION['user']= [
                        'id' => $reponse3['id'],
                        'login' => $reponse3['login'],
                        'password' => $reponse3['password'],
                        'email' => $reponse3['email'],
                        'firstname' => $reponse3['firstname'],
                        'lastname' => $reponse3['lastname']
                    ];
                    echo "connexion réussie"."<br>";
                
            }else{
                return "connexion echoué: utilisateur inexistant";
                
            }
        }
    }
        
    //Déconnexion
    public function disconnect() {
        // Déconnectez-vous de la base de données
        session_unset();
        session_destroy();
        return "Déconnecté de la base de données";
    }
      
    // public function __destruct(){
    //     echo $this->disconnect();
    // }

    //Suppression
    public function delete() {
        $requete2 = "DELETE FROM `utilisateurs` WHERE `utilisateurs`.`id` = '$this->id'";
        $this->bdd->query($requete2);
        return "suppression de user: $this->login effectué";
         
    }

    //Modification d'informations
    public function update($login, $password, $email, $firstname, $lastname){
        $requete3 = $this->bdd->query("UPDATE utilisateurs SET login = '$login' , password = '$password', email = '$email', firstname = '$firstname', lastname = '$lastname' WHERE login = '$this->login'");
        $this->bdd->query($requete3);
        return "<br>".'Les modifications ont été enregistrés si connexion est réussi'."<br>";
    }

    //Is connected
    public function isConnected(){
        return !!$this->id;
    }

    public function getAllInfos(){
            //affichage
            ?>
            <table style="text-align:center" border="1">
                <thead>
                    <tr>
                        <th>login</th>
                        <th>password</th>
                        <th>email</th>
                        <th>firstname</th>
                        <th>lastname</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= $this->login; ?></td>
                        <td><?= $this->password; ?></td>
                        <td><?= $this->email; ?></td>
                        <td><?= $this->firstname; ?></td>
                        <td><?= $this->lastname; ?></td>
                    </tr>
            </table>
            <?php    
    }

    //affichage de login
    public function getLogin(){
        return $this->login;
         
    }

    //affichage de mot de email
    public function getEmail(){
        return $this->email;
    }
    
    //affichage de mot de firstname
    public function getFirstname(){
        return $this->firstname;
    }
    
    //affichage de mot de lastname
    public function getLastname(){
        return $this->lastname;
    }

    
}

// Créer un nouvel utilisateur
$user = new User('john.doe', 'john.doe@example.com', 'John', 'Doe');


// // Régistration dans la bdd
//echo $user->register("john.doe", "pass",  "john.doe@example.com", "John", "Doe")."<br>";

//Connexion
echo $user->connect("john.doe", "pass");
var_dump($_SESSION);

// //Déconnexion
// echo $user->disconnect();
// $session = $_SESSION["login"] ?? "la session est vide";
// echo "<br>".$session."<br>";

//suppression de l'utilisateur qui est connecté
//echo $user->delete();

// //mise à jour des informations d'utilisateur qui est connecté
// echo $user->update('f','f','f','f','f');

// // Test du isConnected
// echo $user->isConnected();

// //affichage des informations d'utilisateur dans un tableau
// echo $user->getAllInfos();

// //affichage de login d'utilisateur
//  echo $user->getLogin()."<br>";

// // //affichage de email d'utilisateur
//  echo $user->getEmail()."<br>";

// // //affichage de firstname d'utilisateur
//  echo $user->getFirstname()."<br>";

// // //affichage de lastname d'utilisateur
//  echo $user->getLastname()."<br>";
