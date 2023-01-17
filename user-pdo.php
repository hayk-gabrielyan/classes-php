<?php
session_start();

class Userpdo {
    private $id;
    public $login;
    public $password;
    public $email;
    public $firstname;
    public $lastname;
    public $bdd;
    const CONSTANT_NAME = 'sda';
    
    //constructeur
    public function __construct(){

            // connexion à la BDD avec PDO
            $servername = 'localhost';
            $dbname = 'classes';
            $db_username = 'root';
            $db_password = '';

            // essaie de connexion
            try{
                $this->bdd = new PDO("mysql:host=$servername;dbname=$dbname; charset=utf8", $db_username, $db_password);

                // On définit le mode d'erreur de PDO sur Exception
                $this->bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                //echo "Connexion réussie"; 
            }         
            // si erreur, on capture les exceptions, s'il y en a une on affiche les infos
            catch(PDOException $e){
                echo "Echec de la connexion : " . $e->getMessage();
                exit;
            }
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

    //inscription
    public function register($login, $password, $email, $firstname, $lastname) {
        $query = "INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (:login, :password, :email, :firstname, :lastname)";
        $stmt = $this->bdd->prepare($query);
        $stmt->execute([
            ':login' => $login,
            ':password' => $password,
            ':email' => $email,
            ':firstname' => $firstname,
            ':lastname' => $lastname
            ]);
    
        return "l'utilisateur a bien été enregistré"."<br>";
    }

    //connexion
    public function connect($login, $password) {
        $user = $this->bdd->prepare("SELECT * FROM utilisateurs WHERE login = ?");
        $user->execute([$login]);
        $verif = $user->rowCount();
        
        if($verif == 1){
            $info = $user->fetch(PDO::FETCH_ASSOC);
            if ($password == $info["password"]){
                $_SESSION['id'] = $info['id'];
                $_SESSION['login'] = $info['login'];
                $_SESSION['firstname'] = $info['firstname'];
                $_SESSION['lastname'] = $info['lastname'];
                $_SESSION['password'] = $info['password'];
                $_SESSION['email'] = $info['email'];
                return "connexion réussi !"."<br>";
            } else {
                return "erreur de mot de passe incorrect";
            }
        } else {
            return "l'identifiant ou de mot de passe est incorrect";
        }
    }

    //déconnexion
    public function disconnect() {
        session_unset();
        session_destroy();
        return "déconnexion effectué";
    }

    //suppression
    public function delete() {
        $user_id = $_SESSION['id'];

        $del = $this->bdd->prepare ("DELETE FROM `utilisateurs` WHERE id = ? ");
        $del->execute([$user_id]);
        $this->disconnect();
        $affichage = "l'utilisateur - avec l'id - $user_id a été supprimé de la base de données.";
        return $affichage;
    } 

        // Vérification de la connexion
    //Is connected
    public function isConnected(){
        return !!$this->id;
    }

       // Modification
    public function update($login, $password, $email, $firstname, $lastname)
    {
        //vérification que la personne est connecté
        if($this->isConnected()){
            //vérification que les champs ne sont pas vides
            if($login !== "" && $password !== "" && $email !=="" && $firstname !=="" && $lastname !=="" ){

                // requête pour vérifier que le login choisi n'est pas déjà utilisé
                $requete = "SELECT * FROM utilisateurs where login = :login";

                // préparation de la requête
                $select = $this->bdd->prepare($requete);

                // exécution de la requête avec liaison des paramètres
                $select-> execute(array(':login' => $login));

                // récupération du tableau
                $fetch_all = $select->fetchAll();

                if(count($fetch_all) !== 0){ // login disponible
                    // récupération des données pour les attribuer aux attributs
                    $_SESSION['user']= [
                        'id' => $this->id,
                        'login' => $login,
                        'password' => $password,
                        'email' => $email,
                        'firstname' => $firstname,
                        'lastname' => $lastname
                    ];

                    // requête pour modifier l'utilisateur dans la base de données
                    $requete2 = "UPDATE utilisateurs SET login = :login, password = :password, email = :email, firstname = :firstname, lastname = :lastname WHERE id = :id";
                    // préparation de la requête
                    $update = $this->bdd->prepare($requete2);
                    // exécution de la requête avec liaison des paramètres
                    $update-> execute(array(
                        ':id' => $this->id,
                        ':login' => $login, 
                        ':password' => $password, 
                        ':email' => $email, 
                        ':firstname' => $firstname, 
                        ':lastname' => $lastname));

                    $error = "Modification réussie";
                    return $error; // modification réussie
                }
                else{
                    $error = "Le login choisi n'est pas disponible";
                    return $error; // login indisponible
                }
            }
            else{
                $error = "Tous les champs ne sont pas renseignés, il faut le login, le mot de passe, l'email, le prénom et le nom";
                return $error; // utilisateur ou mot de passe vide
            }
        }
        else{
            $error = "Vous n'êtes pas connecté, vous devez être connecté pour modifier le compte";
            return $error; // utilisateur non connecté
        }
    }
    public function getAllInfos()  {
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
                        <td><?= $_SESSION["login"]; ?></td>
                        <td><?= $_SESSION["password"]; ?></td>
                        <td><?= $_SESSION["email"]; ?></td>
                        <td><?= $_SESSION["firstname"]; ?></td>
                        <td><?= $_SESSION["lastname"]; ?></td>
                    </tr>
            </table>
        <?php
    }
    
    
    //affichage de login
    public function getLogin(){
        return "login : " . $_SESSION["login"];
    }
    
    //affichage de mot de email
    public function getEmail(){
        return "email : " . $_SESSION["email"];
    }
    
    //affichage de mot de firstname
    public function getFirstname(){
        return "firstname : " . $_SESSION["firstname"];
    }
    
    //affichage de mot de lastname
    public function getLastname(){
        return "lastname : " . $_SESSION["lastname"];
    }
    
}
//************TEST//************

    // Création d'un nouveau utilisateur
    $user = new Userpdo;
    
    // Test d'inscription
    // echo $user->register('john.doe','pass','john.doe@example.com','John','Doe');

    //connexion
    echo $user->connect("john.doe", "pass");

    var_dump($_SESSION);

    ////déconnexion
    //echo $user->disconnect();
    //var_dump($_SESSION);

    ////suppression d'utilisateur
    //echo $user->delete();

    ////modification d'informations d'utilisateur
    //echo $user->update('b', 'b', 'b', 'b', 'b');
    //var_dump($_SESSION);

    // //affichage de toutes informations
    // echo $user->getAllInfos();
        
    // // //affichage de login d'utilisateur
    // echo $user->getLogin()."<br>";

    // // //affichage de email d'utilisateur
    // echo $user->getEmail()."<br>";

    // // //affichage de firstname d'utilisateur
    // echo $user->getFirstname()."<br>";

    // // //affichage de lastname d'utilisateur
    // echo $user->getLastname()."<br>";
?>