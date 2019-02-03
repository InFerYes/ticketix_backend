try {
    $auth->admin()->deleteUserByEmail($_POST['email']);
}
catch (\Delight\Auth\InvalidEmailException $e) {
    die('Unknown email address');
}