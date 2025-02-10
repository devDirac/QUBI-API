<?php
namespace App\Http\Controllers\API;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;

use Illuminate\Http\Request;
use App\Utils\SmsSend;

class WhatsAppController extends BaseController{

    public function sendWhatsAppMessage(Request $request){
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'to' => 'required',
                'body' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => 'Los parametros "body" y "to" son requeridos'], 500);
            }
            $ultramsg_token="gmxwvtq6ts9up00d"; // Ultramsg.com token
            $instance_id="instance80546"; // Ultramsg.com instance id
            $sendMail = new SmsSend($ultramsg_token,$instance_id);
            $to=$input['to'];//"+525635309370"; 
            $body=$input['body']; //"Hello ...."; 
            $api=$sendMail->sendChatMessage($to,$body);
            print_r($api);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}