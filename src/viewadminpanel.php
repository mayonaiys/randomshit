<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>RandomShit</title>
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="images/icon.png">
    </head>
    <body>
        <nav class="navbar navbar-light bg-light justify-content-between">
            <?php
            include 'controller.php';
            session_start();
            echo '<h3 class="navbar-brand">Bienvenue <strong>'.$_SESSION['login'].'</strong></h3>';
            ?>
            <div>
                <a class="btn btn-success" href="../index.php">Accueil</a>
                <a class="btn btn-danger" href="controller.php?function=logout">Deconnexion</a>
            </div>
        </nav>
        <br>
        <div class="container">
            <div class="card">
                <div class="card-header">
                    Memes en attente
                </div>
                <div class="card-body">
                    <?php
                        displayWaitingMemes();
                    ?>
                </div>
            </div>
            <br>
            <div class="card">
                <div class="card-header">
                    Liste de tous les mêmes
                </div>
                <div class="card-body">
                    <?php
                        displayAllMemes();
                    ?>
                </div>
            </div>
            <br>
            <form action="controller.php?function=uploadMeme" method="post" enctype="multipart/form-data">
                Ajouter un meme* :
                <input type="file" name="fileToUpload" id="fileToUpload">
                <input type="submit" id="upload" value="Upload Image" name="submit">
            </form>
            <small>*Une approbation des administrateurs sera requise.</small>
            <section id="errors" class="container alert alert-danger" style="display: none"></section>
            <br>
        </div>
    </body>
</html>