<?php


namespace App\Http\Controllers\API;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use App\Models\User;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\Client;
// use Lcobucci\JWT\Parser;
use Carbon\Carbon;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\UnencryptedToken;

// use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;

class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected static function getCurrentClient(Request $request){
		$clientRepository = new ClientRepository();
		if(is_null($request->bearerToken())){ 
			return null;
		}
        $token = $request->bearerToken();

        $tokenParts = explode(".", $token);  
        $tokenHeader = base64_decode($tokenParts[0]);
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtHeader = json_decode($tokenHeader);
        $jwtPayload = json_decode($tokenPayload);

        $client = $clientRepository->find($jwtPayload->aud);
        
		if(Carbon::now()->timestamp > $jwtPayload->exp){
            $client = NULL;
        }
       
		return $client;
	}

	protected static function getCurrentToken(Request $request){
	    $tokenRepository = new TokenRepository();
        $token = $request->bearerToken();

        $tokenParts = explode(".", $token);  
        $tokenHeader = base64_decode($tokenParts[0]);
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtHeader = json_decode($tokenHeader);
        $jwtPayload = json_decode($tokenPayload);

	    $jwt = (new Parser(new JoseEncoder()))->parse($request->bearerToken());
	    $token= $tokenRepository->find($jwt->claims()->all()['jti']);
        $token->expires_at = now();

        
	    return $token;
	}    

	protected static function refreshClientToken(Client $client){
	    $tokenRepository = new ClientRepository();
	    $client= $tokenRepository->regenerateSecret($client);
	    return $client;
	}
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($message,$result,$code)
    {
    	$response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];
        return response()->json($response, 200);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code)
    {
    	$response = [
            'success' => false,
            'message' => $error,
        ];
        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }
        return response()->json($response, $code);
    }

    /**
     * Validate client exists or valid client 
     *  #header @param bearerToken
    */
    public function validateClient()
    {
        $clientlogin = $this::getCurrentClient(request());
        
        if(is_null($clientlogin)) return $this->sendError('Token Expired',[],401);

        $user = User::find($clientlogin->user_id);
        if(is_null($user)) return $this->sendError('Unauthorized',[],401);
        
        if($user->active == false) return $this->sendError('User is blocked so please contact admin',[],403);

        return $user;
    }
}