<?php
namespace App\Controllers;
use App\Models\User;
use Respect\Validation\Validator as v;
class SignUpController extends BaseController {
    public function getSignUpAction($request){
        $responseMessage = '';
        if ($request->getMethod() == 'POST'){
            $postData = $request->getParsedBody();    
            $userValidator = v::key('email', v::stringType()->notEmpty())
                  ->key('password', v::stringType()->notEmpty());


            try {
                $userValidator->assert($postData);
                $user = new User();
                
                $user->email = $postData['email'];
                $user->password = password_hash($postData['password'],PASSWORD_DEFAULT);
                $user->name = $postData['name'];
                $user->save();
                $responseMessage = "Usuario creado";
            } catch(\Exception $e){
                $responseMessage= $e->getMessage();
            }
        }
        return $this->renderHTML('signup.twig',[
            "responseMessage" => $responseMessage
        ]);
    }
}