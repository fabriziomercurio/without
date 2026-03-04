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
           $productId = $this->productService->edit($id);
            var_dump($productId); 
            echo PHP_EOL;
             if (ResizeImage::hasFile('image')) { 
                //se l'immagine viene inviata faccio un controllo se è la stessa, altrimenti carico una nuova          
              
            $productId["xMultimediaId"] . PHP_EOL; 
                 //se $productId non è null, aggiorno, altrimenti salvo una nuova immagine e salvo il nome nel db 
                  if ($productId["xMultimediaId"] !== null) {
                
                      $multimedia = $this->multimediaService->edit($productId["xMultimediaId"]); 
           
                      $parts = explode("_", $multimedia["filename"], 2); 
                
                      if ($_FILES["image"]["name"] !== $parts[1] ) {
                          echo 'non sono uguali quindi vanno aggiornati' . PHP_EOL . PHP_EOL. PHP_EOL;
                          ///salva fisicamente immagine 
                      
                          $filenames = ResizeImage::store("image",[1920, 800, 400],"products"); 
                          CompressImage::run($filenames['paths'], $_FILES['image']['type']); 
                          $request->extra['multi_name'] = $filenames["baseName"]; 
                          //$this->multimediaService->update($productId["xMultimediaId"], $request); // da scommentare se il test va bene

                          /**
                           * prima dell'update recupero il filename vecchio 
                           */

                          ///////////////// DA TESTARE

                          $multi = $this->multimediaService->edit($productId["xMultimediaId"]); 

                          if (!empty($multi)) {  
                         
                // eliminare anche immagine da disco 
                //se la cartella esiste 
                //elimino i file contenenti quel nome 69a0044152595_Tennis-PNG-Image.png 
                $formats = ['max','medium','min']; 


            $date = explode(' ', $multi["created_at"]); 
            $formattedDate= (new \DateTime($date[0]))->format("d-m-Y");

           $dir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/images/products/".$formattedDate."/"; 

                foreach ($formats as $key => $value) { 


                  
                //  $dir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/images/products/".$formattedDate."/".strtolower($formats[$key])."/"; 
                  if (is_dir($dir)) { 
                    
                    echo 'trovato' . PHP_EOL; 
                     echo         $file = $dir.strtolower($formats[$key])."/".$multi["filename"]; 

                    if (file_exists($file)) { 
                        unlink($file); 
                        echo "il file esiste ed è stato eliminato correttamente" . PHP_EOL;
                    } 

                 


                  } else {
                    echo 'non trovato' . PHP_EOL; 
                  }
                  
                }  

                // $this->deleteEmptyDirs($dir); 
                // $request->extra["xMultimediaId"] = null; 
             
                $this->productService->update($id,$request); 
               // $this->multimediaService->delete($productId["xMultimediaId"]); 

               $this->multimediaService->update($productId["xMultimediaId"], $request);
             
              } else {
                echo 'il record multimedia è vuoto ' . PHP_EOL; 
              }


                          /////////////////// fine da testare

                        //   $this->multimediaService->update($productId["xMultimediaId"], $request); 


                          
                   
                          /// salva sulla tabella 
                      }else {
                         /// anche se le sono uguali, i record devono comunque essere salvati 
                        echo "sono uguali";
                      }
                
                      //quindi aggiorno; 
                   
                  } else {
                       echo 'è null quindi creo una nuova immagine'; 
                       $filenames = ResizeImage::store("image",[1920, 800, 400],"products"); 
                       CompressImage::run($filenames['paths'], $_FILES['image']['type']); 
                       $request->extra['fileimage'] =  $filenames['baseName'];
                       $multimediaId = $this->multimediaService->store($request);
                       $request->extra['xMultimediaId'] = (int)$multimediaId; 
                    
                       $this->productService->update($id,$request); 
           
                  }
               
              //    $productId["xMultimediaId"];   
             }else { 
          
              if ($productId["xMultimediaId"] !== null) {
                  $multi = $this->multimediaService->edit($productId["xMultimediaId"]);
              }        
         
              if (!empty($multi)) {  
                // eliminare anche immagine da disco 
                //se la cartella esiste 
                //elimino i file contenenti quel nome 69a0044152595_Tennis-PNG-Image.png 
                $formats = ['max','medium','min']; 


            $date = explode(' ', $multi["created_at"]); 
            $formattedDate= (new \DateTime($date[0]))->format("d-m-Y");

           $dir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/images/products/".$formattedDate."/"; 

                foreach ($formats as $key => $value) { 


                  
                //  $dir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/images/products/".$formattedDate."/".strtolower($formats[$key])."/"; 
                  if (is_dir($dir)) { 
                    
                    echo 'trovato' . PHP_EOL; 
           echo         $file = $dir.strtolower($formats[$key])."/".$multi["filename"]; 

                    if (file_exists($file)) { 
                        unlink($file); 
                        echo "il file esiste ed è stato eliminato correttamente" . PHP_EOL;
                    } 

                 


                  } else {
                    echo 'non trovato' . PHP_EOL; 
                  }
                  
                }  

                $this->deleteEmptyDirs($dir); 
                $request->extra["xMultimediaId"] = null; 
             
                $this->productService->update($id,$request); 
                $this->multimediaService->delete($productId["xMultimediaId"]); 
             
              } else {
                echo 'immagine non esiste su tabella, impossibile eliminarla ' . PHP_EOL; 
              }
             
             
                echo "l'immagine non esiste";
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

        // $data = $this->productService->delete($productId); 

        // if ($data === true) {
        //     Response::success('record delete with success');
        // }else {
        //     Response::error('record delete failed');
        // } 

        
            
            //edit per verificare se xMultimedia non è null, 
            $product = $this->productService->edit($productId);
            if($product == false) throw new \Exception('product id not found'); 
            $multi = $this->multimediaService->edit($product['xMultimediaId']); 

            $formats = ['max','medium','min']; 


            $date = explode(' ', $multi["created_at"]); 
            $formattedDate= (new \DateTime($date[0]))->format("d-m-Y");

         $dir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/images/products/".$formattedDate."/"; 
    
$fileToDelete = []; 
      foreach ($formats as $key => $value) { 


                  
                //  $dir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/images/products/".$formattedDate."/".strtolower($formats[$key])."/"; 
                  if (is_dir($dir)) { 
                    
                    echo 'trovato' . PHP_EOL; 
               $file = $dir.strtolower($formats[$key])."/".$multi["filename"]; 
           $fileToDelete[] = $file; 

                    // if (file_exists($file)) { 
                    //     // unlink($file); 
                    //     echo "il file esiste ed è stato eliminato correttamente" . PHP_EOL;
                    // } 

                 


                  } else {
                    echo 'non trovato' . PHP_EOL; 
                  }
                  
                }  

                Transaction::beginTransaction();
        try {

            //  $this->multimediaService->delete($product['xMultimediaId']);
            
            $this->productService->delete($productId); 
            $this->multimediaService->delete($product['xMultimediaId']);

              Transaction::commit();

             if(!empty($fileToDelete)) 
             {
                foreach ($fileToDelete as $file) {
                    if(file_exists($file)) 
                    {  
                        unlink($file); 
                        $this->deleteEmptyDirs($dir);
                        echo "il file esiste ed è stato eliminato correttamente" . PHP_EOL;
                    }
                } 

                echo $dir . PHP_EOL;
             }
        

            // prendo xMultimediaId e lo utilizzo per eliminare immagine sia su tabelle e successivamente su disco 
            // e se si dovesse rompere qualcosa durante l'esecuzione e l'immagine viene eliminata lo stesso?
            //elimino la tabella figlia, products ...
           
            Response::success('record deleted with success', '', 200);
        } catch (\Exception $e) {
            Response::error($e->getMessage(), null, 400);
            Transaction::rollBack();
        }
    } 



public function deleteEmptyDirs($dir) {
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



public function deleteEmptyDirs($dir) {
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



}


?>