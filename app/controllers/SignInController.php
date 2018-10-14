<?php
namespace App\Controllers;
use App\Models\User;
use Respect\Validation\Validator as v;
use Zend\Diactoros\Response\RedirectResponse;
class SignInController extends BaseController {
    public function getSignInAction($request){
        $responseMessage = '';
        if ($request->getMethod() == 'POST'){
            $postData = $request->getParsedBody();    
            $userValidator = v::key('email', v::stringType()->notEmpty())
                  ->key('password', v::stringType()->notEmpty());


            try {
                $userValidator->assert($postData);
                $users = User::where('email',$postData['email'])->first();
                if ($users){
                    if (\password_verify($postData['password'],$users->password)){
                        $_SESSION['userID'] = $users->id;
                        return new RedirectResponse('/platzi/intro/admin');
                    }else{
                        $responseMessage = "Contraseña incorrecta";
                    }
                }else{
                    $responseMessage = "No existe el user";
                }
            } catch(\Exception $e){
                $responseMessage= $e->getMessage();
            }
        }
        return $this->renderHTML('signin.twig',[
            "responseMessage" => $responseMessage
        ]);
    }
    public function getLogout(){
        unset($_SESSION['userID']);
        return new RedirectResponse('/platzi/intro/signin');
    }
}