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
    public function __construct(private ProductService $productService = new ProductService, 
    private MultimediaService $multimediaService = new MultimediaService) 
    {  
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
        if ($data !== false) {
           Response::success('', $data, 200);
        }else {
           Response::error('record not found');
        }        
    }

    public function update(int $id, Request $request)
    {
        $data = $this->productService->update($id,$request); 
        if ($data !== false) {
           Response::success('record updated with success', $data, 200);
        }else {
           Response::error('record not found');
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

            $multiName = $request->getBody()['multi_name'] ?? null; 
            
            if ($multiName) { 
                $filenames = ResizeImage::store("image",[1920, 800, 400],"products"); 
                CompressImage::run($filenames, $_FILES['image']['type']); 
                $multimediaId = $this->multimediaService->store($request); 
                $request->extra['xMultimediaId'] = $multimediaId;
            }else {
                echo json_encode(['multi_image' => 'multi image is required if you want insert an image']);
            }   
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