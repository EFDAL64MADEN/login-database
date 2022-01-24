<?php 

session_start();
// Démarre une nouvelle session ou reprend une session existante

require "src/connect.php";
// require inclut et exécute le fichier spécifié en argument
// Lorsqu'une erreur survient, il produit également une erreur fatale

if(!empty($_POST["email"]) && !empty($_POST["password"])){
// Si les champs du formulaire ne sont pas vides alors ont peut traiter la demande de connexion
    $email = htmlspecialchars($_POST["email"]);
    $password = htmlspecialchars($_POST["password"]);
    // htlmspecialchars convertit les caractères spéciaux en entités HTML

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    // on vérifie si ce qui est entré dans le champs email est bien un email

        header('location:index.php?error=1&message=Format de l\'email incorrect !');
        exit();
        // Si le format de l'email est incorrect, on redirige sur index.php et on affiche un message dans l'url, message qu'on pourra récupérer plus bas pour l'afficher directement sur la page

    }

    // Connexion au site

    $requete = $db->prepare("SELECT * FROM user WHERE email = ?");
    $requete->execute(array($email));

    while($user = $requete->fetch()){
    // fetch permet de récupérer la ligne suivante d'un jeu de résultats PDO

        if(password_verify($password, $user["password"])){
        // si le mot de passe donné dans le formulaire correspond au mot de passe de l'utilisateur en bdd

            $_SESSION['connect'] = 1;
            // On dit qu'une session est connectée
            $_SESSION['email'] = $user['email'];
            // On dit quel utilisateur est connecté à la session
            if(isset($_POST["remember"])){
                setcookie('Id', $user['id_user'], time()+365*24*3600, null, null, false, true);
                setcookie('Email', $user['email'], time()+365*24*3600, null, null, false, true);
            }
            header('location:index.php?success=1&message=Vous êtes connecté');
            exit();

        } else {

            header('location:index.php?error=1&message=Mot de passe incorrect');
            exit();
            
        }
        
    }
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

            <?php if(isset($_SESSION['connect'])){ ?>

                <h1>Bienvenue Otaku !</h1>
                <?php if(isset($_GET['success'])){
                    echo '<div class="alert success">'.htmlspecialchars($_GET['message']).'</div>';
                    // Si on a indiqué succes plus haut, le message pris en compte sera celui avec un id success et on affiche le message contenu dans l'url
                } ?>
                <p>Choisissez parmi une vaste sélection des meilleurs mangas</p>
                <small id="deco"><a href="logout.php">Se déconnecter</a></small>

            <?php } else { ?>

                <h1>Se connecter</h1>
                <?php if(isset($_GET['success'])){
                    echo '<div class="alert success">'.htmlspecialchars($_GET['message']).'</div>';
                } else if(isset($_GET['error'])){
                    echo '<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';
                }
            ?>

            <form method="post" action="index.php">
                <input type="email" name="email" placeholder="Entrez votre adresse mail" required />
                <input type="password" name="password" placeholder="Entrez mot de passe" required />
                <button type="submit">Se connecter</button>
                <label id="option"><input type="checkbox" name="remember" />Se souvenir de moi</label>
            </form>

            <p class="grey">Pas encore de compte ? <a href="inscription.php">Inscrivez-vous</a>.</p>
            <?php } ?>
        <!-- </div> -->
    </section>
    <?php include("src/footer.php"); ?>
</body>
</html>