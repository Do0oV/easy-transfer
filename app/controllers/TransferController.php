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
        if (isset($_POST['upload']) && !empty($_POST['exp_email']) && !empty($_POST['dest_email']) && !empty($_FILES['uploadFile'])) {
          $transfer = new Transfer();
          $transfer->exp_email = $_POST['exp_email'];
          $transfer->dest_email = $_POST['dest_email'];
          $file = $_FILES['uploadFile']['name'];

          $path = 'app/transfers/';

          if (move_uploaded_file($_FILES['uploadFile']['tmp_name'], $path.$file)) {
            $transfer->path = $file;
            $transfer->message = $_POST['message'];

            $transfer->save();

            $path .= $transfer->id;
            $id = $transfer->id;

            $this->flashbag->set('alert', [
                'type' => 'success',
                'msg' => 'transfer added youhou !'
            ]);
            //$this->url->redirect('');
            echo $this->twig->render('transfers/result.html.twig',[
                'file' => $file,
                'path' => $path,
                'id' => $id,
                'dest_email' => $_POST['dest_email']
            ]);
        }else{
            die();
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
        $transfer = Transfer::findOne([
            'id' => $id
        ]);
        $file = dirname(__FILE__,2).'/transfers/'.$transfer->path;
        var_dump($file);
        $mime = mime_content_type($file);
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
        readfile($file);
        exit;


    }
}