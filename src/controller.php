<?php
//Connexion base de donnée
function connexbdd($base,$user,$password){
    try {
        $bdd = new PDO($base,$user,$password);
        return $bdd;
    } catch (PDOException $exception){
        header("Location: ../index.html");
        return 0;
    }
}

//Choix fonction
if($_GET['function']=="register"){
    register();
} else if($_GET['function']=="login"){
    login();
} if($_GET['function']=="deleteMeme"){
    deleteMeme($_GET['id']);
} if($_GET['function']=="refuseMeme"){
    refuseMeme($_GET['id']);
} if($_GET['function']=="acceptMeme"){
    acceptMeme($_GET['id']);
} if($_GET['function']=="uploadMeme"){
    uploadMeme();
}

//Fonction d'enregistrement de nouvel utilisateur
function register(){
    $bdd = connexbdd('pgsql:dbname=randomshit;host=localhost;port=5432', 'postgres', 'passwordbdd');
    if(isset($_POST['login']) && isset($_POST['mail']) && isset($_POST['password']) && isset($_POST['passwordrepeat'])){
        if($_POST['password'] == $_POST['passwordrepeat']){
            $query = "SELECT COUNT(*) as nb FROM utilisateur";
            $result = $bdd->query($query);
            $id = $result->fetch()['nb'] + 1;
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $add = $bdd->prepare('INSERT INTO utilisateur(id, login, password, mail, status) VALUES(?, ?, ?, ?, ?)');
            $add->execute(array($id,$_POST['login'],$password,$_POST['mail'],0));
            header("Location: ../index.html");
        } else {
            echo "Les mots de passe ne correspondent pas !";
        }
    }
}

//Fonction de connexion
function login(){
    $bdd = connexbdd('pgsql:dbname=randomshit;host=localhost;port=5432', 'postgres', 'passwordbdd');
    if(isset($_POST['mail']) && isset($_POST['password'])){
        $user = $bdd->prepare('SELECT * FROM utilisateur WHERE mail=?');
        $user->execute(array($_POST['mail']));
        $isconnected = false;
        while(($infos = $user->fetch())!=0 && !$isconnected){
            if(password_verify($_POST['password'],$infos['password'])){
                $isconnected = true;
                session_start();
                $_SESSION['userid']=$infos['id'];
                $_SESSION['login']=$infos['login'];
                $_SESSION['userstatus']=$infos['status'];
            }
        }

        if($isconnected){
            if($_SESSION['userstatus']==0){
                header("Location: viewuserpanel.php");
            } else if($_SESSION['userstatus']==1){
                header("Location: viewadminpanel.php");
            }
        } else  {
            echo '<br><section id="errors" class="container alert alert-danger">Email ou mot de passe incorrect.</section>';
        }
    }
}

//Fonction d'affichage de la liste de memes
function displayListMemes(){
    $bdd = connexbdd('pgsql:dbname=randomshit;host=localhost;port=5432', 'postgres', 'passwordbdd');
    $memesList = $bdd->prepare('SELECT * FROM memes WHERE userid=?');
    $memesList->execute(array($_SESSION['userid']));
    
    echo '<div class="table-wrapper-scroll-y my-custom-scrollbar">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Aperçu</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>';

    while(($data=$memesList->fetch())!=0){
        echo '<tr>
                <th>'.$data['id'].'</th>',
                '<td><img src="'.$data['link'].'" class="rounded" style="max-width: 20%; height: auto ;" alt="'.$data['link'].'"></td>';
        if($data['status']==0){
            echo '<td><h4><span class="badge badge-warning">En attente</span></h4></td>';
        } else if($data['status']==1){
            echo '<td><h4><span class="badge badge-success">Accepté</span></h4></td>';
        } else if($data['status']==2){
            echo '<td><h4><span class="badge badge-danger">Refusé</span></h4></td>';
        }
    }

    echo '</tbody>
        </table>
    </div>';


}

//Fonction d'affichage d'une liste de tous les mêmes
function displayAllMemes(){
    $bdd = connexbdd('pgsql:dbname=randomshit;host=localhost;port=5432', 'postgres', 'passwordbdd');
    $memesList = $bdd->prepare('SELECT * FROM memes');
    $memesList->execute();
    
    echo '<div class="table-wrapper-scroll-y my-custom-scrollbar">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Aperçu</th>
                        <th scope="col">Propriétaire</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>';

    while(($data=$memesList->fetch())!=0){
        if($data['status']==1) {
            echo '<tr>
                <th>' . $data['id'] . '</th>',
                '<td><img src="' . $data['link'] . '" class="rounded" style="max-width: 20%; height: auto ;" alt="' . $data['link'] . '"></td>';
            $owner = $bdd->prepare('SELECT * FROM utilisateur WHERE id=?');
            $owner->execute(array($data['userid']));
            $owner = $owner->fetch();
            echo '<td>' . $owner['login'] . '</td>',
                '<td><form method="post" action="controller.php?function=deleteMeme&id=' . $data['id'] . '"><button type="submit" class="btn btn-danger">Supprimer</button></form></td>';
        }
    }

    echo '</tbody>
        </table>
    </div>';


}

//Fonction de suppression d'un même
function deleteMeme($id){
    $bdd = connexbdd('pgsql:dbname=randomshit;host=localhost;port=5432', 'postgres', 'passwordbdd');
    $getMeme = $bdd->prepare("SELECT * FROM memes WHERE id=?");
    $getMeme->execute(array($id));
    $getMeme = $getMeme->fetch();
    unlink($getMeme['link']);

    $delete = $bdd->prepare("DELETE FROM memes WHERE id=?");
    $delete->execute(array($id));

    $memesList = $bdd->prepare('SELECT * FROM memes');
    $memesList->execute();
    $id = 1;
    while(($data=$memesList->fetch())!=0){
        $changeId = $bdd->prepare("UPDATE memes SET id=? WHERE id=?");
        $changeId->execute(array($id,$data['id']));
        $id++;
    }
    header("Location: viewadminpanel.php");
}

//Fonction d'affichage des memes en attente
function displayWaitingMemes(){
    $bdd = connexbdd('pgsql:dbname=randomshit;host=localhost;port=5432', 'postgres', 'passwordbdd');
    $memesList = $bdd->prepare('SELECT * FROM memes');
    $memesList->execute();

    echo '<div class="table-wrapper-scroll-y my-custom-scrollbar">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Aperçu</th>
                        <th scope="col">Propriétaire</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>';

    while(($data=$memesList->fetch())!=0){
        if($data['status']==0) {
            echo '<tr>
                <th>' . $data['id'] . '</th>',
                '<td><img src="' . $data['link'] . '" class="rounded" style="max-width: 20%; height: auto ;" alt="' . $data['link'] . '"></td>';
            $owner = $bdd->prepare('SELECT * FROM utilisateur WHERE id=?');
            $owner->execute(array($data['userid']));
            $owner = $owner->fetch();
            echo '<td>' . $owner['login'] . '</td>',
                '<td><form method="post" action="controller.php?function=acceptMeme&id=' . $data['id'] . '"><button type="submit" class="btn btn-success">Accepter</button></form></td>',
                '<td><form method="post" action="controller.php?function=refuseMeme&id=' . $data['id'] . '"><button type="submit" class="btn btn-danger">Refuser</button></form></td>';
        }
    }

    echo '</tbody>
        </table>
    </div>';
}

//Fonction de refus d'un meme
function refuseMeme($id){
    $bdd = connexbdd('pgsql:dbname=randomshit;host=localhost;port=5432', 'postgres', 'passwordbdd');
    $refuse = $bdd->prepare("UPDATE memes SET status=2 WHERE id=?");
    $refuse->execute(array($id));

    $getMeme = $bdd->prepare("SELECT * FROM memes WHERE id=?");
    $getMeme->execute(array($id));
    $getMeme = $getMeme->fetch();
    unlink($getMeme['link']);

    header("Location: viewadminpanel.php");
}

//Fonction d'acceptation d'un meme
function acceptMeme($id){
    $bdd = connexbdd('pgsql:dbname=randomshit;host=localhost;port=5432', 'postgres', 'passwordbdd');
    $refuse = $bdd->prepare("UPDATE memes SET status=1 WHERE id=?");
    $refuse->execute(array($id));

    header("Location: viewadminpanel.php");
}

//Fonction d'importation d'un meme
function uploadMeme(){
    echo "test";
    $target_dir = "../images/memes/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }

// Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

// Check file size
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

// Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            session_start();
            $bdd = connexbdd('pgsql:dbname=randomshit;host=localhost;port=5432', 'postgres', 'passwordbdd');
            $query = "SELECT COUNT(*) as nb FROM memes";
            $result = $bdd->query($query);
            $id = $result->fetch()['nb'] + 1;
            $addMeme = $bdd->prepare('INSERT INTO memes(id,userid,link,status) VALUES(?,?,?,?);');
            $addMeme->execute(array($id,$_SESSION['userid'],$target_file,0));
            if($_SESSION['userstatus']==0){
                header("Location: viewuserpanel.php");
            } else if($_SESSION['userstatus']==1){
                header("Location: viewadminpanel.php");
            }
        }
    }
}

//Pick random meme
function randomPick(){
}
?>