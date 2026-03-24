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
 
        if ($data !== false && $data["xMultimediaId"] !== NULL) { 
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

    public function update(int $id, Request $request) 
    { 
      $product = new Product; 
      $media = new Multimedia;  
      $body = $request->getBody();

      $this->validation($product->validation($body),$media->validation($body)); 

      Transaction::beginTransaction();    
      try {          
         
      $prod = $this->productService->edit($id); 

      if($prod == false) throw new \Exception("record id not found"); 

          $data = $this->productService->update($id,$request); 
 
         //if send and image, it controls if the name is not equal and id is equal and then save it
         $productId = $this->productService->edit($id);
         
           if (ResizeImage::hasFile('image')) {                         
         
            //if $productId is not null, update, otherwise save a new image and save the name in the db
            if ($productId["xMultimediaId"] !== null) {
            
                $multimedia = $this->multimediaService->edit($productId["xMultimediaId"]); 
         
                $parts = explode("_", $multimedia["filename"], 2); 
            
              if ($_FILES["image"]["name"] === $parts[1] ) return; 

                $filenames = ResizeImage::store("image",[1920, 800, 400],"products"); 
                CompressImage::run($filenames['paths'], $_FILES['image']['type']); 
                $request->extra['multi_name'] = $filenames["baseName"];               
                $multi = $this->multimediaService->edit($productId["xMultimediaId"]); 
 
              if (empty($multi)) return;                
                
              $this->deleteImages($multi);
              $this->productService->update($id,$request); 
              $this->multimediaService->update($productId["xMultimediaId"], $request);                 
        
              } else {              
                     $filenames = ResizeImage::store("image",[1920, 800, 400],"products"); 
                     CompressImage::run($filenames['paths'], $_FILES['image']['type']); 
                     $request->extra['fileimage'] =  $filenames['baseName'];
                     $multimediaId = $this->multimediaService->store($request);
                     $request->extra['xMultimediaId'] = (int)$multimediaId;                  
                     $this->productService->update($id,$request);    
                }                    
            }else { 
        
            if ($productId["xMultimediaId"] === null) return; 
                $multi = $this->multimediaService->edit($productId["xMultimediaId"]);
                 
       
            if (empty($multi)) return; 

              $dir = $this->deleteImages($multi);

              $this->deleteEmptyDirs($dir); 
              $request->extra["xMultimediaId"] = null; 
           
              $this->productService->update($id,$request); 
              $this->multimediaService->delete($productId["xMultimediaId"]);       
          
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
        Transaction::beginTransaction();
        try { 
            
            //edit per verificare se xMultimedia non è null, 
            $product = $this->productService->edit($productId);

            if($product == false) throw new \Exception('product id not found'); 

            if ($product['xMultimediaId'] !== null) {
                $multi = $this->multimediaService->edit($product['xMultimediaId']);

            $formats = ['max','medium','min']; 

            $date = explode(' ', $multi["created_at"]); 
            $formattedDate= (new \DateTime($date[0]))->format("d-m-Y");

            $dir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/images/products/".$formattedDate."/";                   
    
            $fileToDelete = []; 

             foreach ($formats as $key => $value) { 
           
              if (!is_dir($dir)) return;                     
          
                $file = $dir.strtolower($formats[$key])."/".$multi["filename"]; 
                $fileToDelete[] = $file;                 
                } 
            } 
         
            $this->productService->delete($productId); 

            if ($product['xMultimediaId'] !== null) { 
            $this->multimediaService->delete($product['xMultimediaId']); 
            }

            Transaction::commit();

             if(empty($fileToDelete)) return;           
                foreach ($fileToDelete as $file) {
                    if(!file_exists($file)) return;        
                    unlink($file); 
                    $this->deleteEmptyDirs($dir);             
                }           

            Response::success('record deleted with success', '', 200);
        } catch (\Exception $e) {
            Response::error($e->getMessage(), null, 400);
            Transaction::rollBack();
        }
    } 

public function deleteEmptyDirs(string $dir) {
    $isEmpty = true;

    foreach (scandir($dir) as $item) {
        if ($item === '.' || $item === '..') continue;

        $path = $dir . DIRECTORY_SEPARATOR . $item;

        if (is_dir($path)) {
            if (!$this->deleteEmptyDirs($path)) {
                $isEmpty = false;
            }
        } else {
            $isEmpty = false;
        }
    }

    if ($isEmpty) {
        rmdir($dir);
    }

    return $isEmpty;
}    
    
   public function validation(array $x, array $y)  
   { 
      $errors = array_merge($x,$y);  
      if (!empty($errors)) {
          echo json_encode($errors); 
          exit; 
      } 
   } 

   public function generateDirectory(string $createdAt) : string 
   {
      $date = explode(' ', $createdAt); 
      $formattedDate= (new \DateTime($date[0]))->format("d-m-Y");

      return $_SERVER['DOCUMENT_ROOT'] . "/uploads/images/products/".$formattedDate."/"; 
   } 

   public function deleteImages(array $multi) : string 
   {
      $formats = ['max','medium','min']; 

      $dir = $this->generateDirectory($multi['created_at']); 

      foreach ($formats as $key => $value) { 
 
       if (is_dir($dir)) {                  
           
        $file = $dir.strtolower($formats[$key])."/".$multi["filename"]; 

        if (file_exists($file)) unlink($file);                                     
       }           
      }  

      return $dir;   
   }

}


?>