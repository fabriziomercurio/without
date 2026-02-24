<?php
declare(strict_types=1); 

namespace App\Controllers; 
use App\Core\Connections\Connection; 
use App\Core\Connections\MySQL;
use App\Models\Product; 
use App\Models\Multimedia;
use App\Core\Request; 
use App\Services\ProductService; 
use App\Services\MultimediaService;
use App\Core\Response;
use App\Core\ResizeImage; 
use App\Core\CompressImage; 
use App\Core\Transaction;

class ProductController extends Controller 
{   
    public function __construct(private ?ProductService $productService = null, 
    private ?MultimediaService $multimediaService = null) 
    {  
       $this->productService = $productService ?? new ProductService; 
       $this->multimediaService = $multimediaService ?? new MultimediaService;
       parent::__construct(); 
    } 

    public function show() 
    {   
        $row = $this->productService->fetchAll(); 
        echo json_encode($row); 
    }

    public function edit(int $productId) 
    {   
        $data = $this->productService->edit($productId);
 
        if ($data["xMultimediaId"] !== NULL) { 
           $data['multimedia'] = $this->multimediaService->edit($data["xMultimediaId"]); 
           $formats = ['max', 'medium', 'min']; 
           
           $date = explode(' ',$data['multimedia']['updated_at']); 
           $formatted = (new \DateTime($date[0]))->format("d-m-Y"); 

          foreach ($formats as $value) {           
             $data['multimedia']['paths'][$value] = "/uploads/images/products/" . $formatted . "/" . $value . "/" . $data["multimedia"]["filename"];            
          }     
        } 

        if ($data !== false) {
           Response::success('', $data, 200);
        }else {
           Response::error('record not found');
        }        
    }

    // public function update(int $id, Request $request)
    // {  
    //     $data = $this->productService->update($id,$request); 
    //     if ($data !== false) {
    //        Response::success('record updated with success', $data, 200);
    //     }else {
    //        Response::error('record not found');
    //     }
    // } 

    public function update(int $id, Request $request) 
    {
        $product = new Product; 
        $media = new Multimedia; 

        $body = $request->getBody(); 

        $errors = array_merge($product->validation($body),$media->validation($body)); 

        if (!empty($errors)) {
            echo json_encode($errors); 
            exit; 
        } 

        Transaction::beginTransaction();   

        try {         

          $data = $this->productService->update($id,$request); 
  
           //se arriva l'immagine, controllo che il nome non sia uguale e l'id si e poi salvo 
            //quando arriva i record dei prodotti salvo ... 
           if (ResizeImage::hasFile('image')) { 
              //se l'immagine viene inviata faccio un controllo se è la stessa, altrimenti carico una nuova 
          
               $productId = $this->productService->edit($id); 
               //se $productId non è null, aggiorno, altrimenti salvo una nuova immagine e salvo il nome nel db 
                if ($productId["xMultimediaId"] !== null) {
                 
                    $multimedia = $this->multimediaService->edit($productId["xMultimediaId"]); 
            
                 $parts = explode("_", $multimedia["filename"], 2); 
                 echo PHP_EOL;
                 var_dump($parts[1]); 
                 var_dump($_FILES["image"]["name"]);
                  echo PHP_EOL;
                    if ($_FILES["image"]["name"] !== $parts[1] ) {
                        echo 'non sono uguali quindi vanno aggiornati' . PHP_EOL . PHP_EOL. PHP_EOL;
                        ///salva fisicamente immagine 
                       
                        $filenames = ResizeImage::store("image",[1920, 800, 400],"products"); 
                      
                        CompressImage::run($filenames['paths'], $_FILES['image']['type']); 
                    
                        /// salva sulla tabella 
                    }else {
                        exit("sono uguali");
                    }
                 
                    //quindi aggiorno; 
                    
                } else {
                     echo 'è null'; exit;
                }
                
            //    $productId["xMultimediaId"];   
           }

          Transaction::commit();
          if ($data !== false) {
             Response::success('record updated with success', $data, 200);
          }else {
             Response::error('record not found');
          }


        } catch (\Exception $e) {
            Response::error($e->getMessage(), null, 400);
            Transaction::rollBack();
        }
    }

    public function store(Request $request) 
    {   
        $data = new Product; 
        $media = new Multimedia; 
        
        $body = $request->getBody();
        
        $errors = array_merge($data->validation($body),$media->validation($body)); 

        if (!empty($errors)) {
            echo json_encode($errors);
            exit;
        }     

       Transaction::beginTransaction();  

        try {      

        if (ResizeImage::hasFile('image')) { 

            $filenames = ResizeImage::store("image",[1920, 800, 400],"products"); 
            CompressImage::run($filenames['paths'], $_FILES['image']['type']); 
            $request->extra['fileimage'] =  $filenames['baseName'];
            $multimediaId = $this->multimediaService->store($request); 
            $request->extra['xMultimediaId'] = $multimediaId;   
        }           
            $data = $this->productService->store($request);                     
            Transaction::commit();
            Response::success('record inserted with success', $data, 200);              

        } catch (\Exception $e) { 
            Response::error($e->getMessage(), null, 400);
            Transaction::rollBack();
        }      
    } 

    public function delete(int $productId)
    {   
        $data = $this->productService->delete($productId); 

        if ($data === true) {
            Response::success('record delete with success');
        }else {
            Response::error('record delete failed');
        }
    }

}


?>