<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';
require 'src/Connexion.php';


//middleWare
$mw = function($request,$response,$next){
$body = $request->getBody();
$data = json_decode($body,true);
if($data['age'] <= 0){
    $response->getBody()->write('Age not correct');
}else{
    $response = $next($request, $response);
}
return $response;
};
$app = new \Slim\App;
$app->get('/products', function(Request $request,Response $response,array $args) use ($con) {
$rqt=$con->query('select * from products');
$rs=$rqt->fetchAll();
var_dump(json_encode($rs));
});
$app->get('/personnes', function(Request $request,Response $response,array $args) use ($con) {
$rqt=$con->query('select * from peresonne');
$rs=$rqt->fetchAll();
var_dump(json_encode($rs));
});
$app->post('/personnes', function(Request $request,Response $response,array $args) use ($con) {
    $body = $request->getBody();
    $data = json_decode($body,true);
    $nom = $data['nom'];
    $sexe = $data['sexe'];
    $age = $data['age'];
    $rqt=$con->prepare("INSERT INTO peresonne(nom,sexe,age) VALUES (:nom,:sexe,:age)");
    $rqt->execute([
        'nom'=>$nom,
        'sexe'=>$sexe,
        'age'=>$age,
    ]);
    echo("Insertion avec succes");
    })->add($mw);
$app->post('/products', function(Request $request,Response $response,array $args) use ($con) {
    $body = $request->getBody();
    $data = json_decode($body,true);
    $designation = $data['designation'];
    $quantite = $data['quantite'];
    $prix = $data['prix'];
    $rqt=$con->prepare("INSERT INTO products(designation,quantite,prix) VALUES (:designation,:quantite,:prix)");
    $rqt->execute([
        'designation'=>$designation,
        'quantite'=>$quantite,
        'prix'=>$prix,
    ]);
    echo("Insertion avec succes");
    });
    $app->put('/products/{id}', function(Request $request,Response $response,array $args) use ($con) {
        $body = $request->getBody();
        $data = json_decode($body,true);
        $designation = $data['designation'];
        $quantite = $data['quantite'];
        $prix = $data['prix'];
        $id = $args['id'];
        $rqt=$con->prepare("UPDATE `products` SET `designation`=:designation,`quantite`=:quantite,`prix`=:prix WHERE `id`=:id");
        $rqt->execute([
            'designation'=>$designation,
            'quantite'=>$quantite,
            'prix'=>$prix,
            'id'=>$id,
        ]);
        echo("Modification avec succes");
        });
        $app->delete('/products/{id}', function(Request $request,Response $response,array $args) use ($con) {
            // $body = $request->getBody();
            // $data = json_decode($body,true);
            $id = $args['id'];
            $rqt=$con->prepare("DELETE FROM `products` WHERE `id`=:id");
            $rqt->execute([
                'id'=>$id,
            ]);
            echo("Suppression avec succes");
            });
$app->run();
