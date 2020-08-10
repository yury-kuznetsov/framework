<?php

/* @var app\models\forms\RegisterForm $model */

$errors = $model->getErrors();

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <title>Log In</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link href="styles/cabinet_login.css" rel="stylesheet">
</head>
<body>

<form class="form-signin" method="post">

    <h1 class="h3 mb-3 font-weight-normal text-center">Create new account</h1>

    <? if (count($errors) > 0) { ?>

        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h4 class="alert-heading">Errors!</h4>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>

            <? foreach ($errors as $name => $error) { ?>

                <strong><?= $name ?></strong> <?= $error ?><br/>

            <? } ?>

        </div>

    <? } ?>

    <br/>

    <label for="inputLogin" class="sr-only">Email address</label>
    <input type="email" class="form-control" id="inputLogin" name="RegisterForm[login]" placeholder="Email" value="<?= $model->login ?>" required autofocus/>

    <label for="inputPassword" class="sr-only">Password</label>
    <input type="password" class="form-control" id="inputPassword" name="RegisterForm[password]" placeholder="Password" value="<?= $model->password ?>" required/>

    <label for="inputPasswordRepeat" class="sr-only">Password</label>
    <input type="password" class="form-control" id="inputPasswordRepeat" name="RegisterForm[passwordRepeat]" placeholder="Password Repeat" value="<?= $model->passwordRepeat ?>" required/>

    <div class="row">
        <div class="col col-xs-6">
            <img src="?r=default/captcha" class=""/>
        </div>
        <div class="col col-xs-6">
            <input type="text" class="form-control float-right" name="RegisterForm[captcha]" placeholder="Captcha" value="<?= $model->captcha ?>" required/>
        </div>
    </div>

    <br/><br/>

    <button class="btn btn-lg btn-primary btn-block" type="submit">Create account</button>
    <div class="color-grey text-center mt-3 mb-3">- OR -</div>
    <a href="?r=account/login" class="btn btn-lg btn-light btn-block">Sign in</a>
</form>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

</body>
</html>
