<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\collectiontb;
use App\Curluse;

class ContactController extends Controller
{
    
        
    /* function for sendEmail for contact form */
    public function sendEmail(Request $request){
        
          $shop_name = $request->get('shops'); 
         
           /* input fields data */
           $fname = $request->get('firstname');
           $lastname = $request->get('lastname');
           $name=$fname.''.$lastname;
           $email = $request->get('email');
           $store_url = 'Contact Support '.$shop_name;
           $subject = $request->get('subject');
           $query = $request->get('message');

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\b";
	$headers .= "From: " . 'godarapriya82@gmail.com' . "\r\n";
    $headers .= "Reply-To: $email" . "\r\n";
	$message = '<html><body><h4><span>'.$subject.'</span></h4>';
	$message .= "<p>Name: $name<br></p>";
	$message .= "<p>Email: $email<br></p>";	
	$message .= "<p>Store Url: $shop_name</p>";
	$message .= "<p><br>$query</p>";
	$message .= '</body></html>';
          
     $app_url = config('app.url');         
            $mails = mail('godarapriya82@gmail.com', $store_url ,$message,$headers);
        	if($mails==true){
        	echo 'Sent';
        	}else{
        	    echo 'not';
        	}
           
       
    }
    
    
}
?>