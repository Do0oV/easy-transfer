<?php

namespace Controllers;

use Core\Controllers\Controller;
use Model\Transfer;

class TransferController extends Controller {


    public function index($page = "1") 
    {

        echo $this->twig->render('transfers/index.html.twig');
    }


    public function add()
    {
        if(isset($_POST['upload']) && !empty($_POST['exp_email']) && !empty($_POST['dest_email']) && !empty($_FILES['uploadFile'])) {
            $transfer = new Transfer();

            $transfer->exp_email = htmlspecialchars($_POST['exp_email']);
            $transfer->dest_email = htmlspecialchars($_POST['dest_email']);
            $transfer->message = htmlspecialchars($_POST['message']);
            $file = $_FILES['uploadFile']['name'];

            $ext = pathinfo($_FILES['uploadFile']['name'], PATHINFO_EXTENSION);
            $fake_file = uniqid().'.'.$ext;

            $path = 'app/transfers/';
            $size_max = 524288000;
            $size_file = filesize($_FILES['uploadFile']['tmp_name']);

            if ($size_file <= $size_max) {

               if(move_uploaded_file($_FILES['uploadFile']['tmp_name'], $path.$fake_file)){
                $transfer->path = $file;
                $transfer->fake_path = $fake_file;

                $transfer->save();

                $id = $transfer->id;
                $exp_email = $transfer->exp_email;
                $dest_email = $transfer->dest_email;
                $message = $transfer->message;

                
                $id = $transfer->id;
                $fakeId = rand(100000,900000);
                $fake= $fakeId.$id;

                $this->sendeMailDest($exp_email, $dest_email, $file, $this::formatBytes($size_file), $fake, $message);
                $this->sendeMailExp($exp_email, $dest_email, $file, $this::formatBytes($size_file), $fake, $message);

                $this->flashbag->set('alert', [
                    'type' => 'success',
                    'msg' => 'transfer added youhou !'
                ]);
                echo $this->twig->render('transfers/result.html.twig',[
                    'file' => $file,
                    'fake' => $fake,
                    'dest_email' => $_POST['dest_email'],
                    'size' => $this::formatBytes($size_file)
                ]);

                }else{            
                    $this->flashbag->set('alert', [
                        'type' => 'danger',
                        'msg' => 'upload failed'
                    ]);
                    $this->url->redirect(''); 
                }
            }else{
                 $this->flashbag->set('alert', [
                    'type' => 'danger',
                    'msg' => 'file size not allowed'
                ]);
                 $this->url->redirect(''); 
            }
         }else{
            $this->flashbag->set('alert', [
                'type' => 'danger',
                'msg' => 'Please fill all the fields'
            ]);
            $this->url->redirect('');
        }         
    }

    public function download($id)
    {
        $id = substr($id, 6);
        $transfer = Transfer::findOne([
            'id' => $id
        ]);
        $fake_file = dirname(__FILE__,2).'/transfers/'.$transfer->fake_path;
        $file = $transfer->path;
        $mime = mime_content_type($fake_file);
        // var_dump($mime); 
        header('Content-Description: File Transfer');
        header('Content-Type:'.$mime.'');
        header('Content-Disposition: attachment; filename='.basename($file));
        header('Content-Transfer-Encoding: binary');
        ob_clean();
        flush();
        readfile($fake_file);
        die;

    }

    public function sendeMailDest($exp_email, $dest_email, $file, $size_file, $fake, $message){

        $to         = $dest_email;
        $headers    = 'From: "contact" <info@easytransfer.com>' . "\r\n";
        $headers    .= "Mime-Version: 1.0\n";
        $headers    .= "Content-Transfer-Encoding: 8bit\n";
        $headers    .= "Content-type: text/html; charset= utf-8\n";
        $subject = 'No-reply Easy Transfer: download your file';

        $bodyHtml = $this->twig->parse('emails/dest_email.html.twig', [
            'exp_email' => $exp_email,
            'dest_email' => $dest_email,
            'file' => $file,
            'size' => $size_file,
            'id' => $fake,
            'message' => $message

        ]);
            //uncomment to send by email
            mail($to, $subject, $bodyHtml, $headers);
    }
    
    private function sendeMailExp($exp_email, $dest_email, $file, $size_file, $fake, $message){

        $to         = $exp_email;
        $headers    = 'From: "contact" <info@easytransfer.com>' . "\r\n";
        $headers    .= "Mime-Version: 1.0\n";
        $headers    .= "Content-Transfer-Encoding: 8bit\n";
        $headers    .= "Content-type: text/html; charset= utf-8\n";
        $subject = 'No-reply Easy Transfer: your file has been sent';

        $bodyHtml = $this->twig->parse('emails/exp_email.html.twig', [
            'exp_email' => $exp_email,
            'dest_email' => $dest_email,
            'file' => $file,
            'size' => $size_file,
            'id' => $fake,
            'message' => $message

        ]);
            //uncomment to send by email
            mail($to, $subject, $bodyHtml, $headers);
    }

    public function grabFile($id){

        $fake = $id;
        $id = substr($id, 6);
        $transfer = Transfer::findOne([
            'id' => $id
        ]);
        $pathtofile = 'app/transfers/'.$transfer->fake_path;


        echo $this->twig->render('transfers/download.html.twig',[
            'exp_email' => $transfer->exp_email,
            'file' => $transfer->path,
            'fake' => $fake,
            'message' => $transfer->message,
            'size' => $this::formatBytes(filesize($pathtofile))

        ]);
    }

    public function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array('octets', 'Ko', 'Mo', 'Go', 'To');   
        return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
    }

}