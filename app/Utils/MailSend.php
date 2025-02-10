<?php

namespace App\Utils;
use App\Http\Controllers\API\BaseController as BaseController;

use Mail;

class MailSend extends BaseController {

    public function sendMailPro($data,$blade = 'confirmation', $subject = 'Confirm account') {
        /* try { */
            Mail::send(
                $blade,
                $data,
                function ($message) use ($data,$subject) {
                    $to = $data['email'];
                    $message->to($to)->subject($subject);
                }
            );
            return $this->sendResponse('exito al enviar correo');
        /* } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        } */
    }
}
