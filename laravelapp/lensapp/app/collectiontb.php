<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class collectiontb extends Model
{
    
    public function getShopdata($shop_name){
        // = Session::get('myOshop');
        $res_data = DB::table('users')->where('name',$shop_name)->first();
        return $res_data;
    }
    
    
    /** Start function for fetching all collection at frontend page **/
     public function getCollections($shop_name)
    {
        
        $all_cols = DB::table('collectiontb')->where('shopify_store_name',$shop_name)->get();

        return $all_cols;
    }
    
    /** End function for fetching all collection at frontend page **/
    
    /*** Insert default two collection for newly installation **/
    public function insertDefaultColls($dataCollect){
        
       $aa = DB::table('collectiontb')->insert($dataCollect);
       return $aa;
    }
    

    
}
