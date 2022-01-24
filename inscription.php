<?php 
    session_start();

    require "src/connect.php";

    if(!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password2'])){
    // Il faut que les 3 champs contiennent quelque chose pour que l'inscription soit prise en compte
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);
        $hash = password_hash($password, PASSWORD_DEFAULT);
        // La fonction password_hash() crée un nouveau hachage en utilisant un algorithme de hachage fort et irréversible.
        // Les arguments demandés sont le mot de passe utilisateur, une constante de l'algorithme de mot de passe représentant l'algorithme à utiliser lors du hachage du mot de passe,et un tableau associatif contenant les options (facultatif, est mis tout seul s'il n'est pas renseigné)
        // password_hash va nous permettre plus bas d'inclure le mdp haché dans la bdd
        $password2 = htmlspecialchars($_POST['password2']);

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){

            header('location:inscription.php?error=1&message=Format de l\'email incorrect !');
            exit();

        }

        if($password != $password2){
            // Si les mdp ne correspondent pas, on affiche un message impossible
            // Pour la vérif, on n'utilise pas les mdp hachés, car les deux mdp identiques n'auront pas le même hachage
            header('location:inscription.php?error=1&message=Vos mots de passe ne correspondent pas !');
            exit();

        }

            $requete = $db->prepare("SELECT COUNT(*) as nbMail FROM user WHERE email = ?");
            $requete->execute(array($email));
            while($email_verif = $requete->fetch()){
                if($email_verif['nbMail'] != 0){
                    header('location:inscription.php?error=1&message=Cette adresse email est déjà utlisée');
                    exit();
                }
            }

            $requete = $db->prepare("INSERT INTO user(email, password) VALUES(?, ?)");
            $requete->execute([$email, $hash]);
            // Une requête préparée nous permet de nous prémunir des failles SQL
            // On insère le mail et le mdp haché dans la bdd

            header('location:index.php?success=1&message=Votre compte a été créé avec succès !');
            exit();
            
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="./css/style.css">
    <title>ADN</title>
</head>
<body>
    
    <section>
        <div id="login-body">
            <h1>Inscription</h1>

            <?php

            if(isset($_GET['message'])){

                echo '<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';

            }

            ?>
            <form method="post" action="inscription.php">
                <input type="email" name="email" placeholder="Entrez adresse email" required />
                <input type="password" name="password" placeholder="Entrez mot de passe" required />
                <input type="password" name="password2" placeholder="Retapez mot de passe" required />
                <button type="submit">Valider inscription</button>
            </form>

            <p class="grey">Déjà inscrit ? <a href="index.php">Connexion</a>.</p>
        </div>
    </section>

    <?php include "src/footer.php"; ?>
</body>
</html>