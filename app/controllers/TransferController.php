<?php

namespace Controllers;

use Core\Controllers\Controller;
use Model\Transfer;

class TransferController extends Controller {

    /**
     * Index method
     *
     * @param string $page
     * @return void
     */
    public function index($page = "1") 
    {
        //$transfers = Transfer::find();

        echo $this->twig->render('transfers/index.html.twig');
    }


    /**
     * Deleting category
     *
     * @param int $id
     * @return void
     */
    // public function delete($id) 
    // {
    //     $category = Category::findOne([
    //         'id' => $id
    //     ]);

    //     $category->delete();

    //     $this->flashbag->set('alert', [
    //         'type' => 'success',
    //         'msg' => 'Category deleted !'
    //     ]);

    //     $this->url->redirect('categories');
    // }

    /**
     * Add category
     *
     * @return void
     */
    public function add()
    {
        if(isset($_POST['upload']) && !empty($_POST['exp_email']) && !empty($_POST['dest_email']) && !empty($_FILES['uploadFile'])) {
            $transfer = new Transfer();
            $transfer->exp_email = $_POST['exp_email'];
            $transfer->dest_email = $_POST['dest_email'];
            $file = $_FILES['uploadFile']['name'];

            $ext = pathinfo($_FILES['uploadFile']['name'], PATHINFO_EXTENSION);
            $fake_file = uniqid().'.'.$ext;

            
            var_dump($fake_file);
            var_dump($file);


            $path = 'app/transfers/';
            $size_max = 524288000;
            $size_file = filesize($_FILES['uploadFile']['tmp_name']);

            if ($size_file <= $size_max) {

               if(move_uploaded_file($_FILES['uploadFile']['tmp_name'], $path.$fake_file)){
                $transfer->path = $file;
                $transfer->fake_path = $fake_file;
                $transfer->message = $_POST['message'];

                $transfer->save();

                $id = $transfer->id;
                $fakeId = rand(100000,900000);
                $fake= $fakeId.$id;

                $this->flashbag->set('alert', [
                    'type' => 'success',
                    'msg' => 'transfer added youhou !'
                ]);

                echo $this->twig->render('transfers/result.html.twig',[
                    'file' => $file,
                    'fake' => $fake,
                    'dest_email' => $_POST['dest_email']
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
        var_dump($mime); 
        header('Content-Description: File Transfer');
        header('Content-Type:'.$mime.'');
        header('Content-Disposition: attachment; filename='.basename($file));
        header('Content-Transfer-Encoding: binary');
                // header('Expires: 0');
                // header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                // header('Pragma: public');
                // header('Content-Length: '.$file);
        ob_clean();
        flush();
        readfile($fake_file);
        exit;

    }
}