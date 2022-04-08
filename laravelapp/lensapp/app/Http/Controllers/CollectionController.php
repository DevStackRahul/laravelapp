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

class CollectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.shopify')->except('testUpload'); //What does it actually do?
    }
    // $shop = Auth::user();
    //return view('lensfiles.settings', ['mydata' => $shop]);
    public function fetchCollections(Request $request)
    {
        $shop_name = $request->get('shops');
        $html = '';

        // load model function
        $colModels = new collectiontb();
        $all_data = $colModels->getCollections($shop_name);
        $col_Counts = count($all_data);
        $html .= '<div class="row empty-state"><div class="collection_Counts_heading">Showing ' . $col_Counts . ' Lens Collections</div></div>';
        $html .= '<div class="row lens-collectionsData"><div class="col"><div class="coltable-responsive-xl"><table class="colltable colltable-borderless colltable-hover"><tbody>';
        foreach ($all_data as $rowdata) {
            //print_r($rowdata);
            $collectionID = $rowdata->col_id;
            $collectionName = $rowdata->col_name;
            $collectionProducts_Count = $rowdata->col_products_count;

            $html .= '<tr class="collections-table-row">';
            $html .= '<td class="text-left no-hover-checkbox"><label class="Polaris-Choice" for="PolarisCheckbox1">
            <span class="Collec_Polaris-Checkbox">
            <input id="PolarisCheckbox1" name="collections-' . $collectionID . '" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="">
            </span></td>';
            $html .= '<td class="text-left cursor collections-table-td" data-colid="' . $collectionID . '">
            <span class="col-title-h2"><strong>' . $collectionName . '</strong></span>
            <span class="collections-product_totals">' . $collectionProducts_Count . ' Products</span>
            </td>
            <td class="text-right col-edit"><a href="javascript:void(0)" class="collect-edit-btn" onclick="editcollection(' . $collectionID . ')" data-id="' . $collectionID . '">Edit</a></td>
            <td class="text-right col-delete"><a href="javascript:void(0)" class="collect-delete-btn" onclick="deletecollection(' . $collectionID . ')" data-id="' . $collectionID . '">Delete</a></td>';

            $html .= '</tr>';
        }

        $html .= '</tbody></table></div></div></div>';

        echo $html;
    }


    /** function deleteCollections**/
    public function deleteCollections(Request $request)
    {
        $shop_name = $request->get('shops');
        $col_id = $request->get('delcol');

        // Curldeldata
        $ShopUsers = new collectiontb();
        $shop_data = $ShopUsers->getShopdata($shop_name);

        $shop_access_token = $shop_data->password;
        $api_version = env('SHOPIFY_API_VERSION');

        //api header
        $header = array(
            'Content-Type: application/json',
            'X-Shopify-Access-Token:' . $shop_access_token
        );

        $myobj = new Curluse();

        //get lensid from tb addlens
        $datas = DB::table('savedcollectiondata')->where('colection_id', $col_id)->get();

        //get shopify assign product id
        $assignproids = $datas[0]->shopify_product_id;
        if (!empty($assignproids)) {
            $AssignProducts = strlen($assignproids);
            if ($AssignProducts > 0) {
                $AssignProducts = explode(',', $assignproids);
                $productscount = count($AssignProducts);
                for ($j = 0; $j < $productscount; $j++) {
                    $productidss = $AssignProducts[$j];


                    //update product in shopify remove tag prescription
                    $apiUrl_tag = 'https://' . $shop_name . '/admin/api/2021-10/products/' . $productidss . '.json';
                    $tagdata = json_encode(
                        array(
                            "product" => array(
                                "id" => $productidss,
                                "tags" => ''
                            )
                        )
                    );

                    $responsetag = $myobj->CurlPutdata($apiUrl_tag, $tagdata, $header);
                }
            }
        }


        // now get lens shopify ids
        $clearlensids = $datas[0]->Clearlens_id;
        $antilensids = $datas[0]->Antilens_id;
        $Cleardatas = DB::table('addlenstb')->where('id', $clearlensids)->get();
        if (!empty(count($Cleardatas))) {

            $clearShopifyproid = $Cleardatas[0]->lensShopifyID;
            $apiUrl_2 = 'https://' . $shop_name . '/admin/api/2021-10/products/' . $clearShopifyproid . '.json';
            $response2 = $myobj->Curldeldata($apiUrl_2, $header);
        }


        $Antidatas = DB::table('addlenstb')->where('id', $antilensids)->get();
        if (!empty(count($Antidatas))) {
            $antiShopifyproid = $Antidatas[0]->lensShopifyID;
            /* call product del api */
            $apiUrl_1 = 'https://' . $shop_name . '/admin/products/' . $antiShopifyproid . ' .json';
            $response1 = $myobj->Curldeldata($apiUrl_1, $header);
        }

        // del lenes
        DB::delete('delete from addlenstb where id = ?', [$clearlensids]);

        DB::delete('delete from addlenstb where id = ?', [$antilensids]);

        DB::delete('delete from savedcollectiondata where colection_id = ?', [$col_id]);
        DB::delete('delete from collectiontb where col_id = ?', [$col_id]);

        echo 'Collection delete successfully!';
    }


    /** Default collection Save**/
    public function saveDefaultCollections(Request $request)
    {
        $shop_name = $request->get('shops');

        // form data
        $collection_title = $request->get("collection_title");
        $collection_main_category_id = $request->get("maincategory");
        $collection_main_category_title = $request->get("category_title");
        $collection_main_category_desc = $request->get("category_desc");

        $subArray = array();

        if (!empty($request->get("maincategory"))) {

            if (!empty($request->get("subcats"))) {
                $all_sub_catsids = $request->get("subcats");
                $totalsubcats = count($all_sub_catsids);
                $subcategories = implode(",", $all_sub_catsids);
                //echo $subcategories;
            } else {
                $subcategories = '';
            }


            $fetch_prescriptionName = DB::table('lenscategory')->Where('ID', $collection_main_category_id)->get();
            // print_r($fetch_prescriptionName);
            //exit;
            $typeName = $fetch_prescriptionName[0]->catname;
            $typeDesc = $fetch_prescriptionName[0]->catdesc;

            if ($collection_main_category_title == '') {
                $collection_main_category_title = $typeName;
            }

            if ($collection_main_category_desc == '') {
                $collection_main_category_desc = $typeDesc;
            }


            $data1 = array(
                ['col_name' => $collection_title, 'col_products_count' => 0, 'shopify_store_name' => $shop_name]
            );

            $collresid = DB::table('collectiontb')->insert($data1);
            $last = DB::table('collectiontb')->latest('col_id')->first();


            //multi sub cats




            $data2 = array(
                [
                    'category_id' => $collection_main_category_id, 'Clearlens_id' => '0', 'Antilens_id' => '0', 'colection_id' => $last->col_id, 'cat_disp_title' => $collection_main_category_title,
                    'cat_desc' => $collection_main_category_desc, 'shopify_product_id' => '0', 'sub_cat_id' => $subcategories, 'shop_name' => $shop_name
                ]
            );


            $savedresid = DB::table('savedcollectiondata')->insert($data2);
            $lastsaved =  DB::table('savedcollectiondata')->latest('id')->first();
            // now call lenses for create add lense form
            //$all_lenses = DB::table('catlensestb')->select('*')->get();
            $html = '';
            $html .= '<p class="addLensform-headers" data-collection-id="' . $last->col_id . '" data-category-id="' . $lastsaved->id . '" data-lenstype="' . $typeName . '">Add Lense to the ' . $typeName . '</p>';


            echo $html;
        } else {

            echo 'Sent';
        }
    }

    /** function for fetchPrescriptionTypes **/
    public function fetchPrescriptionTypes(Request $request)
    {
        $shop_name = $request->get('shops');
        $html = '';
        $i = 0;
        $all_prescription = DB::table('lenscategory')->select('*')->get();

        foreach ($all_prescription as $cats) {

            $cat_id = $cats->ID;
            $cat_name = $cats->catname;
            $cat_desc = $cats->catdesc;
            $catSlugs = str_replace(' ', '_', $cat_name);

            //subcats
            $subCatsdata = DB::table('lenssubcats')->where('main_cat_id', $cat_id)->get();




            $html .= '<div class="choiceinput-fields from_createCollec">';
            $html .= '<div class="customCreates_Choices">';
            $html .= '<label class="lensCats_Choices" for="prescription-type-checkbox-' . $catSlugs . '">';
            $html .= '<span class="choice-check-outer"><span class="choice-check-inner"><input data-subcat="' . $cats->hassubcats . '" data-title="' . $cat_name . '" type="checkbox" name="maincategory" id="prescription-type-checkbox-' . $catSlugs . '" class="maincatcheckbox-fields" value="' . $cat_id . '" data-item></span></span>';
            $html .= '<span class="pre-names">' . $cat_name . '</span></lable></div>';



            if ($cats->hassubcats == 'true') {

                $html .= '<div class="choose-subcats hideData">';
                $html .= '<p class="subcat-desc">Please choose a sub-category for ' . $cat_name . ' </p>';
            }



            foreach ($subCatsdata as $subItems) {
                $subSlugs = str_replace(' ', '_', $subItems->subcatname);
                $html .= '<div class="inner-subitems">
               <lable class="subcats" for="subcats">
               <span class="check-input"><input type="checkbox" name="subcats[' . $i . ']" value="' . $subItems->sub_id . '" class="subcategory-field"></span>
               <span class="disp-label">' . $subItems->subcatname . '</span></lable></div>
               ';
                $i++;
            }

            if ($cats->hassubcats == 'true') {
                $html .= '</div>';
            }

            $html .= '</div></div>';
        }

        echo $html;
    }


    /*** function saveLenes ***/
    public function saveLenes(Request $request)
    {
        $shop_name = $request->get('shops');
        $ShopUsers = new collectiontb();
        $shop_data = $ShopUsers->getShopdata($shop_name);

        $shop_access_token = $shop_data->password;
        $api_version = env('SHOPIFY_API_VERSION');

        //api header
        $header = array(
            'Content-Type: application/json',
            'X-Shopify-Access-Token:' . $shop_access_token
        );

        $myobj = new Curluse();


        // form data
        $collectionID = $request->get("collectionID");
        $SavedTbyID = $request->get("categoryID");


        $clearlens_gp = $request->get("clearlens-group");
        $antilens_gp = $request->get("antilens-group");

        if (!empty($request->get("lens_title1"))) {
            $lens_title1 = $request->get("lens_title1");
        } else {
            $lens_title1 = 'Regular clear lens';
        }

        if (!empty($request->get("lens_title2"))) {
            $lens_title2 = $request->get("lens_title2");
        } else {
            $lens_title2 = 'Anti Blue Light Lens';
        }

        if ($clearlens_gp[0]['lens_names'] != '' || $antilens_gp[0]['antilens_names'] != '') {

            $html = '';
            $clearlens_gpCount = count($request->get("clearlens-group"));
            $antilens_gpCount = count($request->get("antilens-group"));


            /* clear lens data product created in shopify */
            if (!empty($clearlens_gp)) {
                // echo 'sdsdds';
                foreach ($clearlens_gp as $clearL) {
                    if (empty($clearL['lensprice'])) {
                        $pprice = 0;
                    } else {
                        $pprice = $clearL['lensprice'];
                    }

                    $ClearLensdata = json_encode(array(
                        "product" => array(
                            "title" => $clearL['lens_names'],
                            "body_html" => "Clear lens product",
                            "vendor" => "lensapp",
                            "product_type" => "lensapp",
                            "price" => $pprice
                            //"lens_names" => $clearL['rxtype']
                        )

                    ));

                    if (!empty($clearL['lens_names'])) {
                        /* call product create api */
                        $apiUrl_1 = 'https://' . $shop_name . '/admin/products.json';
                        $response1 = $myobj->CurlPostdata($apiUrl_1, $ClearLensdata, $header);
                        if (!empty($response1)) {
                            $responseClear_L = json_decode($response1);
                            //  print_r($responseClear_L);

                            foreach ($responseClear_L as $rowClearL) {
                                $productClearID = $rowClearL->id;

                                //update price of the product
                                $ClearLensPrice = json_encode(array(
                                    "inventory_item" => array(
                                        "id" => $productClearID,
                                        "cost" => $pprice
                                    )

                                ));


                                $apiUrl_price1 = 'https://' . $shop_name . '/admin/inventory_items/' . $productClearID . '.json';
                                $price_response1 = $myobj->CurlPutdata($apiUrl_price1, $ClearLensPrice, $header);

                                $img1 = '//cdn.shopify.com/s/files/1/0922/6806/products/glasses.png';


                                //save in db db table addlenstb
                                $data_clear_1 = array(
                                    ['lensname' => $clearL['lens_names'], 'lensprice' => $pprice, 'lenstype' => $lens_title1, 'lensRX' => 'single-vision', 'lensShopifyID' => "$productClearID", 'shopify_store' => $shop_name, 'img' => $img1]
                                );

                                DB::table('addlenstb')->insert($data_clear_1);
                                $last_clear_LID = DB::table('addlenstb')->latest('id')->first();

                                //update savecollectiontb
                                $newdata = array('Clearlens_id' => $last_clear_LID->id);
                                DB::table('savedcollectiondata')->where('id', $SavedTbyID)->update($newdata);
                            }
                        }
                    }
                }
            }

            /* anti lens data product created in shopify */

            if (!empty($antilens_gp)) {
                //print_r($antilens_gp);
                foreach ($antilens_gp as $antiL) {

                    if (empty($antiL['antilensprice'])) {
                        $Apprice = 0;
                    } else {
                        $Apprice = $antiL['antilensprice'];
                    }

                    $AntiLensdata = json_encode(array(
                        "product" => array(
                            "title" => $antiL['antilens_names'],
                            "body_html" => "Anti lens product",
                            "vendor" => "lensapp",
                            "product_type" => "lensapp",
                            "price" => $antiL['antilensprice']
                            //"lens_names" => $antiL['antirxtype']
                        )

                    ));

                    if (!empty($antiL['antilens_names'])) {
                        /* call product create api */
                        $apiUrl_2 = 'https://' . $shop_name . '/admin/products.json';
                        $response2 = $myobj->CurlPostdata($apiUrl_2, $AntiLensdata, $header);

                        if (!empty($response2)) {
                            $responseAnti_L = json_decode($response2);


                            foreach ($responseAnti_L as $rowAntiL) {
                                $productAntiID = $rowAntiL->id;
                                //echo $productAntiID;

                                //update price of the product
                                $antiLensPrice = json_encode(array(
                                    "inventory_item" => array(
                                        "id" => $productAntiID,
                                        "cost" => $Apprice
                                    )

                                ));
                                $img2 = 'https://cdn.shopify.com/s/files/1/0922/6806/products/glasses-arrows_v2.png';

                                $apiUrl_price2 = 'https://' . $shop_name . '/admin/inventory_items/' . $productAntiID . '.json';
                                $price_response2 = $myobj->CurlPutdata($apiUrl_price2, $antiLensPrice, $header);

                                //save in db table addlenstb
                                $data_anti_2 = array(
                                    ['lensname' => $antiL['antilens_names'], 'lensprice' => $Apprice, 'lenstype' => $lens_title2, 'lensRX' => 'single-vision', 'lensShopifyID' => "$productAntiID", 'shopify_store' => $shop_name, 'img' => $img2]
                                );

                                DB::table('addlenstb')->insert($data_anti_2);
                                $last_anti_LID = DB::table('addlenstb')->latest('id')->first();

                                //update savecollectiontb
                                $newdata2 = array('Antilens_id' => $last_anti_LID->id);
                                DB::table('savedcollectiondata')->where('id', $SavedTbyID)->update($newdata2);
                            }
                        }
                    }
                }
            }

            $html .= '<p class="assign-headers" data-collection-id="' . $collectionID . '" data-category-id="' . $SavedTbyID . '"></p>';
            echo $html;
        } else {
            echo 'Sent';
        }
    }


    /** function for addassetfile **/
    public function addassetfile(Request $request)
    {

        $shop_name = $request->get('shops');
        $ShopUsers = new collectiontb();
        $shop_data = $ShopUsers->getShopdata($shop_name);

        $shop_access_token = $shop_data->password;

        //api header
        $header = array(
            'Content-Type: application/json',
            'X-Shopify-Access-Token:' . $shop_access_token
        );

        $myobj = new Curluse();
        $app_url = config('app.url');
        // echo $app_url;

        $urlThemes = 'https://' . $shop_name . '/admin/themes.json';
        $result = $myobj->Curldata($urlThemes, $header);
        $datas = json_decode($result);
        if (!empty($datas)) {

            foreach ($datas as $rows) {
                //echo '<pre>'; print_r($rows);
                $count = count($rows);
                echo $count;


                for ($j = 0; $j < $count; $j++) {

                    $role = $rows[$j]->role;

                    if ($role == 'main') {
                        $themeids = $rows[$j]->id;

                        echo 'hello';





                        // js file code

                        $jscode = 'var script = document.createElement(\'script\');
script.setAttribute(\'src\', \'//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js\');
document.head.appendChild(script);
			  var t = $.noConflict(true);
 var o = void 0;
function change(e, o) {
  if (t(\'[data-step="\'+ e +\'"]\').addClass("active-step").removeClass("done-step"), t(".modal-prescription .header-prescription .prev-arrow").removeClass("d-none"), t(".modal-prescription #optical--footer #prev-button").removeClass("d-none"), t(\'[data-step="\' + o + \'"]\').addClass("done-step"), t(\'[data-step="\' + o + \'"]\').removeClass("active-step"), 5 == o) {
    var n = t(".right-sph").val(),
        i = t(".left-sph").val();
    if (n < parseInt("-3.25") || i < parseInt("-3.25")) {
      var a = t("#optical--footer .product-info .product-price-footer").attr("data-lence-price");
      setTimeout((function() {
        "" == a && t("body").find(".rx-glass1").trigger("click")
      }), 300)
    }
  }
  5 == e && "0" == t(".pupillary-cyl").val() && "0.00" == t(".left-sph").val() && "0.00" == t(".right-sph").val() && "0.00" == t(".right-cyl").val() && "000" == t(".right-axis").val() && "0.00" == t(".left-cyl").val() && "000" == t(".left-axis").val() && "0" == t(".pupillary-sph").val() && 0 == t("#customCheck2").prop("checked") && "0" == t(".pupillary-sph").val() && t("#optical--footer .next-button #next-button").prop("disabled", !0), 7 == e && (0 == t("#rx-material1").is(":checked") ? t(".sucessfuly-msg").addClass("d-none") : t(".sucessfuly-msg").removeClass("d-none"), setTimeout((function() {
    t("#optical--footer .next-button").addClass("d-none"), t("#optical--footer .rx-add-to-cart").removeClass("d-none")
  }), 100))
}
t(".prescription-btn").on("click", function(e) {
  e.preventDefault(), 
    t(".prescription-step-popup").show(), 
    t("body").addClass("overflowHidden"), 
    setTimeout((function() {
    var e = t(".header-prescription").outerHeight(),
        o = t(".footer-prescription").outerHeight();
    t(".content-prescription").css("padding-bottom", o), 
      t(".content-prescription").css("padding-top", e)
  }), 100);
}),
t("body").on("click", ".modal-prescription .header-prescription .close", function(e) {
  t(".prescription-step-popup").hide(),
    t("body").removeClass("overflowHidden"), 
    t(\'[data-step="1"]\').addClass("active-step"),
    t(\'[data-step="1"]\').removeClass("done-step"),
    t(".slide-page-dots li").removeClass("done-step active-step"), 
    t(".slide-page-dots li").eq(0).addClass("active-step"),
    t(".step-wrapper").removeClass("done-step active-step"), 
    t(".step-wrapper").eq(0).addClass("active-step"), 
    t(".prev-arrow,.prev-button button").addClass("d-none")
}),
t("body").on("change", \'input[type="file"]\', function(o) {
  if (o.preventDefault(), "" != t(this).val()) {
    t(".upload-button").text("Uploading..."), 
      t(".upload-button").css("pointer-events", "none"), 
      t("#optical--footer .next-button #next-button").prop("disabled", !1);
    var n = t("#fileUploads").prop("files")[0],
        i = new FormData;
    i.append("attachment", n), t.ajax({
      url: "' . $app_url . '/uploadPrescriptionFile",
      method: "POST",
      contentType: !1,
      cache: !1,
      processData: !1,
      data: i
    }).done((function(o) {
      t(".prescription-upload").val(o.full_url), 
        t(".upload-button").text("complete!"), 
        t(".prescription-method").val("Upload File")
       // _learnq.push(["track", "Prescription"])
    }))
  }
}),
t(".shopify-product-form .tt-input-counter .minus-btn").on("click", function() {
  t(".shopify-product-form .tt-input-counter input").val(), t(".product-price").attr("data-main-pro-price")
}),
  t(".shopify-product-form .tt-input-counter .plus-btn").on("click", function() {
  var e = t(".shopify-product-form .tt-input-counter input").val(),
      o = t(".product-price").attr("data-normal-pro-price") * e;
  t(".product-price").attr("data-main-pro-price", o)
}),
t("body").on("click", ".content-prescription .select-prescription .select-options label", function() {
  var e = t(this);
  setTimeout((function() {
    if ("radio" == e.closest(".select-options").find("input").attr("type") && !0 === e.closest(".select-options").find("input").prop("checked"))
      if ("single-vision-clear-without-blue-light" == (o = e.closest(".select-options").find("input").attr("data-lens")) ? t(".warning-msg").removeClass("d-none") : t(".warning-msg").addClass("d-none"), "non-prescription" == o) {
        t(".product-addToCart-overlay").removeClass("d-none");
        var n = t(".product-main").attr("main-proVariantID"),
            i = e.closest(".select-options").find("input").attr("data-variantID"),
            a = t(".shopify-product-form .tt-input-counter input").val(),
            r = t(".random-number").val(),
            s = e.closest(".select-options").find("input").attr("data-lens-price"),
            l = t(".product-price").attr("data-main-pro-price"),
            d = "$" + ((parseInt(s) + parseInt(l)) / 100).toFixed(2);
        Shopify.queue = [], Shopify.queue.push({
          variantId: n,
          qty: a
        }), Shopify.moveAlong = function() {
          if (Shopify.queue.length) {
            var e = Shopify.queue.shift(),
                o = {
                  id: e.variantId,
                  quantity: e.qty,
                  properties: {
                    "Group ID": r,
                    "Prescription Total Price": d
                  }
                };
            t.ajax({
              type: "POST",
              url: "/cart/add.js",
              dataType: "json",
              data: o,
              async: !1,
              success: function(e) {
                Shopify.moveAlong(), t(".prescription-qty").val(), setTimeout((function() {
                  Shopify.queue = [], Shopify.queue.push({
                    variantId: i
                  }), Shopify.moveAlong = function() {
                    if (Shopify.queue.length) {
                      var e = {
                        id: Shopify.queue.shift().variantId,
                        quantity: 1,
                        properties: {
                          "Group ID": r
                        }
                      };
                      t.ajax({
                        type: "POST",
                        url: "/cart/add.js",
                        dataType: "json",
                        data: e,
                        async: !1,
                        success: function(e) {
                          Shopify.moveAlong(), setTimeout((function() {
                            window.location.href = "/cart"
                          }), 500)
                        },
                        error: function() {
                          Shopify.queue.length && Shopify.moveAlong()
                        }
                      })
                    }
                  }, Shopify.moveAlong()
                }), 1500)
              },
              error: function() {
                Shopify.queue.length && Shopify.moveAlong()
              }
            })
          }
        }, Shopify.moveAlong()
      } else {
        t("#optical--footer .next-button #next-button").prop("disabled", !1), e.closest(".modal-prescription").find("#optical--footer .footer-group .next-button #next-button").removeAttr("disabled"), e.closest(".step-wrapper").attr("data-step");
        var c = e.closest(".step-wrapper").attr("data-next-step");
        7 == c ? (t("#optical--footer .next-button").addClass("d-none"), t("#optical--footer .rx-add-to-cart").removeClass("d-none")) : 6 == c ? t("#optical--footer .next-button #next-button").prop("disabled", !0) : (t("#optical--footer .next-button").removeClass("d-none"), t("#optical--footer .rx-add-to-cart").addClass("d-none")), "reading" == o && "4" == c && c++
      }
  }), 100)
}),
  t("body").on("click", ".content-prescription .modal--doctor-prescription .select-options label", function() {
  var o = t(this);
  setTimeout((function() {
    if ("radio" == o.closest(".select-options").find("input").attr("type")) {
      var n = o.closest(".select-options").find("input").prop("checked"),
          i = o.closest(".select-options").find("input").val("checked");
      if (!0 === n && "Upload" != i) {
        t("#optical--footer .next-button #next-button").prop("disabled", !1);
        var a = o.closest(".select-options").find("[data-per-method]").attr("data-value");
        t(".prescription-method").val(a), o.closest(".modal-prescription").find("#optical--footer .footer-group .next-button #next-button").removeAttr("disabled");
        var r = o.closest(".step-wrapper").attr("data-step"),
            s = o.closest(".step-wrapper").attr("data-next-step");
        7 == s ? (t("#optical--footer .next-button").addClass("d-none"), t("#optical--footer .rx-add-to-cart").removeClass("d-none")) : 6 == s ? t("#optical--footer .next-button #next-button").prop("disabled", !0) : (t("#optical--footer .next-button").removeClass("d-none"), t("#optical--footer .rx-add-to-cart").addClass("d-none")), e(s, r)
      }
      "Upload" == i && t("#optical--footer .next-button #next-button").prop("disabled", !0)
    }
  }), 100)
}),
  t("body").on("click", ".content-prescription .modal--reading-power .select-options label", function() {
  var o = t(this);
  setTimeout((function() {
    if ("radio" == o.closest(".select-options").find("input").attr("type") && !0 === o.closest(".select-options").find("input").prop("checked")) {
      t("#optical--footer .next-button #next-button").prop("disabled", !1);
      var n = o.closest(".select-options").find("input").attr("data-reading-power");
      t(".prescription-reading_power").val(n), o.closest(".modal-prescription").find("#optical--footer .footer-group .next-button #next-button").removeAttr("disabled");
      var i = o.closest(".step-wrapper").attr("data-step"),
          a = o.closest(".step-wrapper").attr("data-next-step");
      6 == a ? (t("#optical--footer .next-button").addClass("d-none"), t("#optical--footer .rx-add-to-cart").removeClass("d-none")) : 5 == a || (t("#optical--footer .next-button").removeClass("d-none"), t("#optical--footer .rx-add-to-cart").addClass("d-none")), e(a, i)
    }
  }), 100)
}),   
  t("body").on("click", "#optical--footer .next-button #next-button", function() {
  if (t(this).closest(".modal-prescription").find(".content-prescription").find(".step-wrapper").hasClass("active-step")) {
    var n = t(".active-step").attr("data-step"),
        i = t(".active-step").attr("data-next-step");
    if ("single-vision-with-clear-anti-blue-light-blocking-technology" == o && 5 == n && (i = 7), console.log(i), 7 == i ? (t("#optical--footer .next-button").addClass("d-none"), t("#optical--footer .rx-add-to-cart").removeClass("d-none")) : 5 == i ? "0" == t(".pupillary-cyl").val() && "0.00" == t(".left-sph").val() && "0.00" == t(".right-sph").val() && "0.00" == t(".right-cyl").val() && "000" == t(".right-axis").val() && "0.00" == t(".left-cyl").val() && "000" == t(".left-axis").val() && "0" == t(".pupillary-sph").val() && 0 == t("#customCheck2").prop("checked") && t("#optical--footer .next-button #next-button").prop("disabled", !0) : (t("#optical--footer .next-button").removeClass("d-none"), t("#optical--footer .rx-add-to-cart").addClass("d-none")), "5" == n) {
      var a = "";
      t(".active-step").find("[data-title]").each((function(e, o) {
        if (t(this).attr("data-title").indexOf("right") > -1) {
          var n = t(this).val();
          a += n + ","
        }
      })), a = a.slice(0, a.length - 1), t(".prescription-od").val(a);
      var r = "";
      t(".active-step").find("[data-title]").each((function(e, o) {
        if (t(this).attr("data-title").indexOf("left") > -1) {
          var n = t(this).val();
          r += n + ","
        }
      })), r = r.slice(0, r.length - 1), t(".prescription-os").val(r);
      var s = t(\'[data-title="pupillary-sph"]\').val();
      null != s && (t(".prescription-pd").val(s), t(".prescription-pd-table").text(":" + s));
      var l = t(\'[data-title="pupillary-cyl"]\').val();
      null != l && (t(".prescription-pd2").val(l), t(".prescription-pd2-table").text(":" + l))
    }
    change(i, n);
  }
}),
  t("body").on("click", ".header-prescription .prev-arrow, #optical--footer #prev-button", function() {
  var e = t(this);
  if (e.closest(".modal-prescription").find(".content-prescription").find(".step-wrapper").hasClass("active-step")) {
    var n = t(".active-step").attr("data-step"),
        i = t(".active-step").attr("data-prev-step");
    4 == n && e.addClass("d-none"), 7 == n && (t("#optical--footer .next-button").removeClass("d-none"), t("#optical--footer .rx-add-to-cart").addClass("d-none")), "single-vision-with-clear-anti-blue-light-blocking-technology" == o && 7 == n && i--,
      function(e, o) {
      t(\'[data-step="\' + e + \'"]\').addClass("active-step"), t(\'[data-step="\' + e + \'"]\').removeClass("done-step"), t(\'[data-step="\' + o + \'"]\').removeClass("active-step"), 1 == e ? (t(".prev-arrow,.prev-button button").addClass("d-none"), t("#rx-material2,#rx-material1").is(":checked") && t("#next-button").removeAttr("disabled")) : t(".prev-arrow,.prev-button button").removeClass("d-none")
    }(i, n)
  }
}),
  t("body").on("change", ".lense-number select", function() {
  "0" == t(".pupillary-sph").val() && "0" == t(".pupillary-cyl").val() ? t("#optical--footer .next-button #next-button").prop("disabled", !0) : t("#optical--footer .next-button #next-button").prop("disabled", !1);
  var e = t(this).val();
  "pupillary-sph" == t(this).attr("data-title") && (e < parseInt("58") || e > parseInt("63") ? t(".update-desc").removeClass("d-none") : t(".update-desc").addClass("d-none"));
  var f = t(this).val();
  "pupillary-sph" == t(this).attr("data-title") && (f > parseInt("58") ? t(".update-desc.pd-info-kids").removeClass("d-none") : t(".update-desc.pd-info-kids").addClass("d-none"));
  var o = t(".right-sph").val(),
      n = t(".left-sph").val();
  o < parseInt("-3.25") || n < parseInt("-3.25") ? (t(".add-blue-glasses").removeClass("d-none"), t(".rx--lens-message").addClass("d-none"), t("[data-lens-price]").html("$50.00")) : (t(".add-blue-glasses").addClass("d-none"), t(".rx--lens-message").removeClass("d-none"), t("[data-lens-price]").html("$0.00")), "0.00" == t(".left-sph").val() && "0.00" == t(".right-sph").val() && "0.00" == t(".right-cyl").val() && "000" == t(".right-axis").val() && "0.00" == t(".left-cyl").val() && "000" == t(".left-axis").val() && "0" == t(".pupillary-sph").val() && t("#optical--footer .next-button #next-button").prop("disabled", !0)
}), 
  t("body").on("click", ".content-prescription .modal--slide-container .lens-type label", function() {
  var e = t(this);
  setTimeout((function() {
    if (!0 === e.closest(".select-options").find("input").prop("checked")) {
      t(".lensClass").removeClass("product-selected"), e.closest(".select-options").find("input").addClass("product-selected");
      var o = e.closest(".select-options").find("input").attr("data-producttitle"),
          n = e.closest(".select-options").find("input").attr("data-lens-price");
      t(".prescription-material").val(o), t(".lens-price-prop,.prescription-material-price").val(n);
      var i = e.closest(".select-options").find("input").attr("data-lens"),
          a = e.closest(".select-options").find(".lens-price").text(),
          r = t("[data-main-pro-price]").attr("data-main-pro-price"),
          s = t("#optical--footer .product-info .product-price-footer").attr("data-lence-price");
      if ("" != s) var l = parseInt(a) + parseInt(r) + parseInt(s);
      else l = parseInt(a) + parseInt(r);
      t("[data-total-price]").attr("data-total-price", l);
      var d = (l / 100).toFixed(2);
      t(".rx-product-details .product-price").attr("data-frame-price", a), t("#optical--footer .product-info .product-price-footer").attr("data-frame-price", a), t(".rx-product-details .product-price").attr("data-total-price", l), t("#optical--footer .product-info .product-price-footer").attr("data-total-price", l), t(".rx-product-details .product-price,#optical--footer .product-info .product-price-footer").text("$" + d), t(".prescription-total-price").val("$" + d), t(".product-upgrades-lens").removeClass("d-none"), t(".product-upgrades-lens .product-upgrade li").text(i + " / 1.6")
    } else e.closest(".select-options").find("input").removeClass("product-selected")
      }), 500)
}),  
  t("body").on("click", ".content-prescription .modal--slide-container .glasses-type label", function() {
  var e = t(this);
  setTimeout((function() {
    e.closest(".select-options").find("input").prop("checked");
    var o = [];
    o.push(".prescription-lens"), o.length > 0 ? (t(".product-upgrades-glass").removeClass("d-none"), t(".product-upgrades-glass .product-upgrade li").text(o)) : t(".product-upgrades-glass").addClass("d-none"), e.closest(".select-options").find("input").addClass("product-selected");
    var n = t("[data-total-price]").attr("data-total-price"),
        i = e.closest(".select-options").find(".glass-price").text(),
        a = e.closest(".select-options").find("input").attr("data-glass");
    t(".prescription-lence").val(a), t("#optical--footer .product-info .product-price-footer").attr("data-lence-price", i), t(".prescription-glass-price,.prescription-lence-price").val(i);
    var r = parseInt(i) + parseInt(n);
    t("[data-total-price]").attr("data-total-price", r);
    var s = (r / 100).toFixed(2);
    t(".rx-product-details .product-price,#optical--footer .product-info .product-price-footer").text("$" + s), t(".prescription-total-price").val("$" + s)
  }), 500)
}),
  t("body").on("click", ".two-pds .custom-control label", function() {
  var e = t(this);
  setTimeout((function() {
    !0 === e.closest(".custom-control").find("input").prop("checked") ? (t(".dist2").removeClass("d-none"), t(".no-pd .custom-control input").prop("checked", !1), t("#optical--footer .next-button #next-button").prop("disabled", !1)) : t(".dist2").addClass("d-none")
  }), 500)
}),
  t("body").on("click", ".no-pd .custom-control label", function() {
  var e = t(this);
  setTimeout((function() {
    !0 === e.closest(".custom-control").find("input").prop("checked") && (t(".two-pds .custom-control input").prop("checked", !1), t(".dist2").addClass("hide"))
  }), 500)
}),  
  t("body").on("change", "select.form-control", function(e) {
  var o = t(this),
      n = t(this).val(),
      i = o.attr("data-title");
  t(\'.product-summary td[data-title="\' + i + \'"]\').html(n)
});
 var n = t(".shopify-product-form .tt-input-counter input").val();
t(".prescription-qty").val(n), t("body").on("change", ".shopify-product-form .tt-input-counter input", function() {
  var e = t(this).val();
  t(".prescription-qty").val(e)
}),
  t("body").on("click", ".prescription-addBtn", function(e) {
  e.preventDefault(), t(".loader-btn").removeClass("d-none"), t(this).addClass("d-none"), t(this).prop("disabled", !0);
  var o = {},
      n = t(".prescription-variantId").val(),
      i = t(".prescription-qty").val();
  Shopify.queue = [], t.each(t(\'input[name*="properties"]\').serializeArray(), (function() {
    var e = this.name.replace("properties[", "").replace("]", "");
    o[e] = this.value
  })), Shopify.queue.push({
    variantId: n,
    qty: i
  }), Shopify.moveAlong = function() {
    if (Shopify.queue.length) {
      var e = {
        id: Shopify.queue.shift().variantId,
        quantity: 1,
        properties: o
      };
      t.ajax({
        type: "POST",
        url: "/cart/add.js",
        dataType: "json",
        data: e,
        success: function(e) {
          Shopify.queue = [];
          var o = t(".random-number").val();
          t(".product-selected").each((function() {
            var e = t(this).attr("data-variantid");
            Shopify.queue.push({
              variantId: e
            })
          })), Shopify.moveAlong = function() {
            if (Shopify.queue.length) {
              var e = {
                id: Shopify.queue.shift().variantId,
                quantity: 1,
                properties: {
                  "Group ID": o
                }
              };
              t.ajax({
                type: "POST",
                url: "/cart/add.js",
                dataType: "json",
                data: e,
                async: !1,
                success: function(e) {
                  Shopify.moveAlong(), window.location.href = "/cart", t(".loader-btn").addClass("d-none"), t(".prescription-addBtn").removeClass("d-none"), t(".prescription-addBtn").prop("disabled", !1)
                },
                error: function() {
                  Shopify.queue.length && Shopify.moveAlong()
                }
              })
            }
          }, Shopify.moveAlong()
        },
        error: function() {
          Shopify.queue.length && Shopify.moveAlong()
        }
      })
    }
  }, Shopify.moveAlong()
}),
  t("body").on("click", ".no-thank", function(t) {
  t.preventDefault(), e(7, 6)
}),
t("body").on("click", ".yes-please", function(o) {
  o.preventDefault(), t(".rx-material1").trigger("click"), t(".warning-msg").addClass("d-none"), t(\'[data-step="6"]\').removeClass("done-step"), t("#optical--footer .next-button").addClass("d-none"), t("#optical--footer .rx-add-to-cart").removeClass("d-none"), e(7, 6)
}),
//  t("body").on("click", ".footer-block .footer-title", function(t){
//    t.preventDefault();
// t(this).hasClass("active")
// })
$(document).ready(function(){
let isMobile = window.matchMedia("only screen and (max-width: 760px)").matches;
 if (isMobile) {
	$(".footer-sect .footer_link").hide();
	$(".footer-sect h4").click(function(){
		$(this).next(".footer_link").slideToggle("slow")
		.siblings(".footer_link:visible").slideUp("slow");
		$(this).toggleClass("active");
		$(this).siblings("h4").removeClass("active");
	});
    }	
});';


                        $paths = 'pull';
                        $path = 'pull/' . $shop_name;
                        if (is_dir('pull')) {
                            if (!is_dir('pull/' . $shop_name)) {
                                mkdir('pull/' . $shop_name);
                                $filepath = 'pull/' . $shop_name;
                            } else {
                                $filepath = 'pull/' . $shop_name;
                            }
                            $filename = $filepath . '/hook.js';
                            $jsfile = fopen($filename, "w");
                            fwrite($jsfile, $jscode);
                            fclose($jsfile);
                        }


                        $permanent_domain = $app_url . "/pull/{{shop.permanent_domain}}/hook.js";


                        //end js file

                        // all lencats
                        $i = 0;
                        $catsids = array();
                        $getdata = DB::table('lenscategory')->select('*')->get();
                        $html = '';
                        $html .= '<div class="one-row">';
                        foreach ($getdata as $cats) {
                            $i++;
                            $catsids[] = $cats->ID;
                            $cat_id = $cats->ID;
                            $cat_name = $cats->catname;
                            $cat_desc = $cats->catdesc;

                            $subCatsdata = DB::table('lenssubcats')->where('main_cat_id', $cat_id)->get();


                            $html .= '<div class="select-options prescription-type desktop-6 tablet-6 mobile-3">';

                            $html .= '<input type="radio" class="PrescriptionClass lensClass" name="prescrption-material" id="prescrption-material' . $i . '" value="' . $cat_name . '" data-value="' . $cat_name . '" data-per-type>
			   <label for="prescrption-material' . $i . '" class="prescrption-material' . $i . ' material px-md-3 py-md-5 p-2 text-center w-100">
			   <span class="right-box"></span>
			   <div class="card">
			   <h5 class="card-title text-uppercase pb-0 mb-2 font-weight-bold">' . $cat_name . '</h5>
			   <p>' . $cat_desc . '</p>
			   </div>';

                            if ($cats->hassubcats == 'true') {

                                $html .= '<div class="choose-subcats"><p class="subcat-desc">Sub-Category for Single Vision </p></div>';

                                $j = 0;
                                foreach ($subCatsdata as $subItems) {
                                    $j++;
                                    $html .= ' <div class="sub-inside-col">
			   <input type="radio" class="PrescriptionSubClass lensClass" name="prescrptionsub-material" id="prescrptionsub-material' . $j . '" value="' . $subItems->subcatname . '" data-value="' . $subItems->subcatname . '"><label for="prescrptionsub-material1" class="prescrptionsub-material1 material px-md-3 py-md-5 p-2 text-center w-100">  <span class="right-box"></span><div class="card"><h5 class="card-title text-uppercase pb-0 mb-2 font-weight-bold">' . $subItems->subcatname . '</h5></div> </label>
			   
			   </div>
               ';
                                    $j++;
                                }
                            }


                            $html .= '</label>';
                            $html .= '</div>';
                        }
                        $html .= '</div>';



                        //lensdata addlenstb
                        $lenshtml = '';
                        $lensdata = DB::table('addlenstb')->where('shopify_store', $shop_name)->get();

                        $lenshtml .= '';
                        $m = 0;
                        foreach ($lensdata as $lensItem) {
                            $m++;
                            $lensid = $lensItem->id;
                            $lensname = $lensItem->lensname;
                            $lensprice = $lensItem->lensprice;
                            $lensRX = $lensItem->lensRX;
                            $lensShopifyID = $lensItem->lensShopifyID;
                            $img = $lensItem->img;

                            $lenshtml .= '<div class="select-options lens-type desktop-6 tablet-6 mobile-3">
                  <input type="radio" class="lensClass" name="rx-material" id="rx-material' . $m . '" value="" data-lens-price="' . $lensprice . '" data-lens="" data-variantID="' . $lensShopifyID . '" data-productTitle="' . $lensname . '" data-productId="' . $lensShopifyID . '">
                  <span class="d-none lens-price">' . $lensprice . '</span>
  
                  <label for="rx-material' . $m . '" class="rx-material' . $m . ' material px-md-3 py-md-5 p-2 text-center w-100">
                    <span class="right-box"></span>
                    <div class="card">
                      <h5 class="card-title text-uppercase pb-0 mb-2 font-weight-bold">' . $lensname . '</h5>
                      <p>Near, Distance, Readers</p>
                      <div class="card-img">
                        <img src="' . $img . '">
                      </div>
                      <div class="card-body">
                        <div class="mt-0 custom-description"></div>
                        <p class="mt-1">' . $lensprice . '</p>
                      </div>
                    </div>
                  </label>
                </div>';
                        }




                        //shopify file              
                        $snippet_code = '<div data-prescription class="prescription-step-popup position-fixed w-100 h-100" style="display: none">
    {% assign pro = product %}
    <div class="modal-prescription row  container">
      <div class="header-prescription d-flex align-items-center justify-content-between fixed-top md:px-3 md:py-3 p-2 bg-white">
        <div class="prev-arrow d-none"></div>
        <div class="slide-page-dots">
          <ul class="p-0 m-0 flex items-center justify-center">
            <li class="px-2 active-step" data-step="1" data-next-step="2"><span></span></li>
            <li class="px-2" data-step="2" data-next-step="3" data-prev-step="1"><span></span></li>
            <li class="px-2" data-step="3" data-next-step="4" data-prev-step="2"><span></span></li>
            <li class="px-2" data-step="4" data-next-step="5" data-prev-step="3"><span></span></li>
            <li class="px-2" data-step="5" data-prev-step="4"><span></span></li>
            <li class="px-2" data-step="6" data-prev-step="5"><span></span></li>
          </ul>
        </div>
        <div class="close icon-f-84"></div>    
      </div>
      <div class="content-prescription flex items-center justify-center">
      <div class="w-100 step-wrapper active-step" data-step="1" data-next-step="2">
       <div class="step-wrapper-inner">
            <div class="container w-100 presc-types">
              <h4 class="section-title  desktop-12 tablet-6 mobile-3 font-weight-bold text-center mb-2">Choose Your Prescription Type</h4>
                     <div class="modal--slide-container select-prescription d-flex flex-wrap justify-content-center types-modal">	
                                 ' . $html . '
                            </div>
              </div></div>
      
      </div>
      
      
        <div class="w-100 step-wrapper" data-step="2" data-next-step="3" data-prev-step="1">
          
          <div class="step-wrapper-inner">
            <div class="container w-100">
              <h4 class="section-title  desktop-12 tablet-6 mobile-3 font-weight-bold text-center mb-2">PICK YOUR LENS</h4>
              <div class="warning-msg justify-content-center d-none">
                <div class="border d-flex align-items-center">
                  <h4 class="text-red">WARNING:</h4>
                  <p class="pl-2 text-black">Lens you have selected DOES NOT include blue light blocking technology</p>
                </div>
              </div>
              <div class="modal--slide-container select-prescription d-flex flex-wrap justify-content-center">	
              ' . $lenshtml . '
                
              </div>
            </div>
          </div>
        </div>
        
  <div class="w-100 step-wrapper" data-step="3" data-next-step="4" data-prev-step="2">
          <div class="step-wrapper-inner">
            <div class="container w-100">
              <h4 class="section-title  desktop-12 tablet-6 mobile-3 font-weight-bold text-center mb-2">Lens Thickness</h4>
              <div class="modal--slide-container lens-thickness-pre select-prescription d-flex flex-wrap justify-content-center">	
                <div class="select-options lens-thicknesspre desktop-6 tablet-6 mobile-3">
                  <input type="radio" class="lens-thiknessClass" name="lensthikness-material" id="lensthikness-material1" value=""  data-value="High Index 1.67" data-lensthikness="High Index 1.67" data-lenthick-type>
                  <label for="lensthikness-material1" class="lensthikness-material1 material px-md-3 py-md-5 p-2 text-center w-100">
                    <span class="right-box"></span>
                    <div class="card">
                      <h5 class="card-title text-uppercase pb-0 mb-2 font-weight-bold">High Index 1.67</h5>
                      <div class="card-body">
                        <div class="mt-0 custom-description"></div>
                        <p class="mt-1"></p>
                      </div>
                    </div>
                  </label>
                </div>
                <div class="select-options lens-thicknesspre  desktop-6 tablet-6 mobile-3">
                  <input type="radio" class="lens-thiknessClass" name="lensthikness-material" id="lensthikness-material2" value=""  data-value="No thickness" data-lensthikness="No thickness" data-lenthick-type>
                  <label for="lensthikness-material2" class="material px-md-3 py-md-5 p-2 text-center w-100">
                    <span class="right-box"></span>
                    <div class="card">
                      <h5 class="card-title text-uppercase pb-0 mb-2 font-weight-bold">I don’t need it</h5>
 
                      <div class="card-body">
                        <div class="mt-0 custom-description"></div>
                        <p class="mt-1"></p>
                      </div>
                    </div>
                  </label>
                </div>
                
              </div>
            </div>
          </div>
        </div>
        <div class="w-100 step-wrapper" data-step="4" data-next-step="5" data-prev-step="3">
          <div class="step-wrapper-inner">
            <div class="container w-100">
              <h4 class="section-title  desktop-12 tablet-6 mobile-3 font-weight-bold text-center mb-2">How would you like to send us your doctor\'s prescription?</h4>
              <div class="modal--slide-container modal--doctor-prescription d-flex flex-wrap">	
                <div class="select-options grid__item desktop-4 tablet-4 mobile-3">
                  <input type="radio" name="rx-method" checked id="rx-option1" data-value="Upload" data-per-method>
                  <label for="rx-option1" class="material upload  px-md-3 py-md-5 p-2 text-center w-100">
                    <span class="right-box"></span>
                    <div class="card">
                      <div class="card-img">
                        <img src="https://cdn.shopify.com/s/files/1/0922/6806/files/action-upload-alt-512.png?150900" alt="upload" >	
                      </div>
                      <div class="card-body">
                        <h5 class="card-title text-uppercase pb-0 mb-2">Upload It now</h5>
                        <p class="d-block">Upload a valid prescription that hasn’t expired (PDF, JPG or PNG)</p>
                        <form class="rx--upload-form" action="" id="upload-form" enctype="multipart/form-data" data-rxfile="" data-email="">
                          <input type="hidden" id="upload_preset" name="attachment" value="himofcxn">
                          <input class="rx--upload" type="file" name="attachment" id="fileUploads" accept="image/png, image/jpeg, image/gif, application/pdf">
                          <label for="fileUploads" class="btn btn--primary upload-button">Upload</label>
                        </form>
                      </div>
                    </div>
                  </label>
                </div>
                <div class="select-options grid__item desktop-4 tablet-4 mobile-3">
                  <input type="radio" name="rx-method" id="rx-option3" data-value="Existing Customer" data-per-method>
                  <label for="rx-option3" class="material  px-md-3 py-md-5 p-2 text-center w-100">
                    <span class="right-box"></span>
                    <div class="card">
                      <div class="card-img">
                        <img src="https://cdn.shopify.com/s/files/1/0922/6806/files/Female-Avatar-User-Woman-Customer-512.png?150900" alt="user" >	
                      </div>
                      <div class="card-body">
                        <h5 class="card-title text-uppercase pb-0 mb-2">Upload your Prescription</h5>
                        <p class="mt-0">My prescription is on file already</p>
                      </div>
                    </div>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="w-100 step-wrapper" data-step="5" data-next-step="6" data-prev-step="4">
          <div class="step-wrapper-inner">
            <div class="container-fluid w-100 lense-number">
              <h4 class="section-title  desktop-12 tablet-6 mobile-3 font-weight-bold text-center mb-2">Let\'s find your exact lens! <br /> Just enter your prescription below</h4>
              
              {% if pro.tags contains "kids" and pro.tags contains "Prescription" %}
                <div class="update-desc pd-info-kids d-none text-center p-3">
                  <p>Are you sure your PD is correct? The average kid\'s PD is between 43-58. We recommend double-checking with your doctor or <a href="#"><u>click here for a PD measurement guide!</u></a> We cannot accept returns for incorrect PD</p>
              	</div>
              {% else%}
              	<div class="update-desc d-none text-center p-3">
                  <p>Are you sure your PD is correct? The average adult woman\'s PD is between 58-63. We recommend double-checking with your doctor or <a href="#"><u>click here for a PD measurement guide!</u></a> We cannot accept returns for incorrect PD</p>
                </div>
              {% endif %}
              
              <div class="rx--lens-message rx--lens-message-included alert-message bg-light mb-3 text-center py-2">
                <div class="alert-sub-message">
                  All lenses come standard with Anti Reflective, Anti Scratch, and Hydrophobic coatings
                </div>
              </div>
              <div class="add-blue-glasses text-center d-none">
                <div class="font-weight-bold ">Your prescription REQUIRES our thinner lens add-on ($50) so that your lenses look cosmetically great too! …</div>
              </div>
              <div class="lenses_price py-3 text-center">
                <h5 class="pb-0 m-0 font-weight-bold text-black">Your lenses cost</h5>
                <p class="mt-0 text-black" data-lens-price>$0.00</p>
              </div>
              <div class="table-responsive">
                <table class="modal__table" style="margin-bottom: 50px;">
                  <thead>
                    <tr>
                      <th></th>
                      <th class="text-center">SPH<br><span>Sphere</span></th>
                      <th class="text-center">CYL<br><span>Cylinder</span></th>
                      <th class="text-center">AXIS<br>&nbsp;</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="od-right">
                      <td class="text-right text-black"><span class="font-weight-bold">OD</span><br><span>Right Eye</span></td>
                      <td class="sph">
                        <select data-title="right-sph" class="form-control right-sph thirdStep">
                          <option value="+3.00">+3.00</option>
                          <option value="+2.75">+2.75</option>
                          <option value="+2.50">+2.50</option>
                          <option value="+2.25">+2.25</option>
                          <option value="+2.00">+2.00</option>
                          <option value="+1.75">+1.75</option>
                          <option value="+1.50">+1.50</option>
                          <option value="+1.25">+1.25</option>
                          <option value="+1.00">+1.00</option>
                          <option value="+0.75">+0.75</option>
                          <option value="+0.50">+0.50</option>
                          <option value="+0.25">+0.25</option>
                          <option value="0.00" selected="selected">0.00</option>
                          <option value="00 PLANO">00 PLANO</option>
                          <option value="PL">PL</option>
                          <option value="SPH">SPH</option>
                          <option value="DS">DS</option>
                          <option value="BALANCE">BALANCE</option>
                          <option value="INFINITY">INFINITY</option>
                          <option value="-0.25">-0.25</option>
                          <option value="-0.50">-0.50</option>
                          <option value="-0.75">-0.75</option>
                          <option value="-1.00">-1.00</option>
                          <option value="-1.25">-1.25</option>
                          <option value="-1.50">-1.50</option>
                          <option value="-1.75">-1.75</option>
                          <option value="-2.00">-2.00</option>
                          <option value="-2.25">-2.25</option>
                          <option value="-2.50">-2.50</option>
                          <option value="-2.75">-2.75</option>
                          <option value="-3.00">-3.00</option>
                          <option value="-3.25">-3.25</option>
                          <option value="-3.50">-3.50</option>
                          <option value="-3.75">-3.75</option>
                          <option value="-4.00">-4.00</option>
                          <option value="-4.25">-4.25</option>
                          <option value="-4.50">-4.50</option>
                          <option value="-4.75">-4.75</option>
                          <option value="-5.00">-5.00</option>
                        </select>
                      </td>
                      <td class="cyl">
                        <select data-title="right-cyl" class="form-control right-cyl thirdStep">           
                          <option value="+2.00">+2.00</option>
                          <option value="+1.75">+1.75</option>
                          <option value="+1.50">+1.50</option>
                          <option value="+1.25">+1.25</option>
                          <option value="+1.00">+1.00</option>
                          <option value="+0.75">+0.75</option>
                          <option value="+0.50">+0.50</option>
                          <option value="+0.25">+0.25</option>
                          <option value="0.00" selected="selected">0.00</option>
                          <option value="00 PLANO">00 PLANO</option>
                          <option value="PL">PL</option>
                          <option value="SPH">SPH</option>
                          <option value="DS">DS</option>
                          <option value="BALANCE">BALANCE</option>
                          <option value="INFINITY">INFINITY</option>                
                          <option value="-0.25">-0.25</option>
                          <option value="-0.50">-0.50</option>
                          <option value="-0.75">-0.75</option>
                          <option value="-1.00">-1.00</option>
                          <option value="-1.25">-1.25</option>
                          <option value="-1.50">-1.50</option>
                          <option value="-1.75">-1.75</option>
                          <option value="-2.00">-2.00</option> 
                        </select>
                      </td>
                      <td class="axis">
                        <select data-title="right-axis" class="form-control right-axis thirdStep">
                          <option value="000" selected="selected">000</option>
                          <option value="001">001</option>
                          <option value="002">002</option>
                          <option value="003">003</option>
                          <option value="004">004</option>
                          <option value="005">005</option>
                          <option value="006">006</option>
                          <option value="007">007</option>
                          <option value="008">008</option>
                          <option value="009">009</option>
                          <option value="010">010</option>
                          <option value="011">011</option>
                          <option value="012">012</option>
                          <option value="013">013</option>
                          <option value="014">014</option>
                          <option value="015">015</option>
                          <option value="016">016</option>
                          <option value="017">017</option>
                          <option value="018">018</option>
                          <option value="019">019</option>
                          <option value="020">020</option>
                          <option value="021">021</option>
                          <option value="022">022</option>
                          <option value="023">023</option>
                          <option value="024">024</option>
                          <option value="025">025</option>
                          <option value="026">026</option>
                          <option value="027">027</option>
                          <option value="028">028</option>
                          <option value="029">029</option>
                          <option value="030">030</option>
                          <option value="031">031</option>
                          <option value="032">032</option>
                          <option value="033">033</option>
                          <option value="034">034</option>
                          <option value="035">035</option>
                          <option value="036">036</option>
                          <option value="037">037</option>
                          <option value="038">038</option>
                          <option value="039">039</option>
                          <option value="040">040</option>
                          <option value="041">041</option>
                          <option value="042">042</option>
                          <option value="043">043</option>
                          <option value="044">044</option>
                          <option value="045">045</option>
                          <option value="046">046</option>
                          <option value="047">047</option>
                          <option value="048">048</option>
                          <option value="049">049</option>
                          <option value="050">050</option>
                          <option value="051">051</option>
                          <option value="052">052</option>                                    
                          <option value="053">053</option>
                          <option value="054">054</option>
                          <option value="055">055</option>
                          <option value="056">056</option>
                          <option value="057">057</option>
                          <option value="058">058</option>
                          <option value="059">059</option>
                          <option value="060">060</option>
                          <option value="061">061</option>                                    
                          <option value="062">062</option>                                    
                          <option value="063">063</option>                                    
                          <option value="064">064</option>                                    
                          <option value="065">065</option>                                    
                          <option value="066">066</option>                                    
                          <option value="067">067</option>                                    
                          <option value="068">068</option>                                    
                          <option value="069">069</option>                                    
                          <option value="070">070</option>                                    
                          <option value="071">071</option>                                    
                          <option value="072">072</option>                                    
                          <option value="073">073</option>                                    
                          <option value="074">074</option>                                    
                          <option value="075">075</option>                                    
                          <option value="076">076</option>                                    
                          <option value="077">077</option>                                    
                          <option value="078">078</option>                                    
                          <option value="079">079</option>                                    
                          <option value="080">080</option>                                    
                          <option value="081">081</option>                                    
                          <option value="082">082</option>                                    
                          <option value="083">083</option>                                    
                          <option value="084">084</option>                                    
                          <option value="085">085</option>                                    
                          <option value="086">086</option>                                    
                          <option value="087">087</option>                                    
                          <option value="088">088</option>                                    
                          <option value="089">089</option>                                    
                          <option value="090">090</option>                                    
                          <option value="091">091</option>                                    
                          <option value="092">092</option>                                    
                          <option value="093">093</option>                                    
                          <option value="094">094</option>                                    
                          <option value="095">095</option>                                    
                          <option value="096">096</option>                                    
                          <option value="097">097</option>                                    
                          <option value="098">098</option>                                    
                          <option value="099">099</option>                                    
                          {% for i in (100..180) %}
                          <option value="{{i}}">{{i}}</option>
                          {% endfor %}
                        </select>
                      </td>
                    </tr>
                    <tr class="od-left">
                      <td class="text-right text-black"><span class="font-weight-bold">OS</span><br><span>Left Eye</span></td>
                      <td class="sph">
                        <select data-title="left-sph" class="form-control left-sph thirdStep">              
                          <option value="+3.00">+3.00</option>
                          <option value="+2.75">+2.75</option>
                          <option value="+2.50">+2.50</option>
                          <option value="+2.25">+2.25</option>
                          <option value="+2.00">+2.00</option>
                          <option value="+1.75">+1.75</option>
                          <option value="+1.50">+1.50</option>
                          <option value="+1.25">+1.25</option>
                          <option value="+1.00">+1.00</option>
                          <option value="+0.75">+0.75</option>
                          <option value="+0.50">+0.50</option>
                          <option value="+0.25">+0.25</option>
                          <option selected="selected" value="0.00">0.00</option>
                          <option value="00 PLANO">00 PLANO</option>
                          <option value="PL">PL</option>
                          <option value="SPH">SPH</option>
                          <option value="DS">DS</option>
                          <option value="BALANCE">BALANCE</option>
                          <option value="INFINITY">INFINITY</option>
                          <option value="-0.25">-0.25</option>
                          <option value="-0.50">-0.50</option>
                          <option value="-0.75">-0.75</option>
                          <option value="-1.00">-1.00</option>
                          <option value="-1.25">-1.25</option>
                          <option value="-1.50">-1.50</option>
                          <option value="-1.75">-1.75</option>
                          <option value="-2.00">-2.00</option>
                          <option value="-2.25">-2.25</option>
                          <option value="-2.50">-2.50</option>
                          <option value="-2.75">-2.75</option>
                          <option value="-3.00">-3.00</option>
                          <option value="-3.25">-3.25</option>
                          <option value="-3.50">-3.50</option>
                          <option value="-3.75">-3.75</option>
                          <option value="-4.00">-4.00</option>
                          <option value="-4.25">-4.25</option>
                          <option value="-4.50">-4.50</option>
                          <option value="-4.75">-4.75</option>
                          <option value="-5.00">-5.00</option>
                        </select>
                      </td>
                      <td class="cyl">
                        <select data-title="left-cyl" class="form-control left-cyl thirdStep"> 
                          <option value="+2.00">+2.00</option>
                          <option value="+1.75">+1.75</option>
                          <option value="+1.50">+1.50</option>
                          <option value="+1.25">+1.25</option>
                          <option value="+1.00">+1.00</option>
                          <option value="+0.75">+0.75</option>
                          <option value="+0.50">+0.50</option>
                          <option value="+0.25">+0.25</option>
                          <option value="0.00" selected="selected">0.00</option>
                          <option value="00 PLANO">00 PLANO</option>
                          <option value="PL">PL</option>
                          <option value="SPH">SPH</option>
                          <option value="DS">DS</option>
                          <option value="BALANCE">BALANCE</option>
                          <option value="INFINITY">INFINITY</option>                
                          <option value="-0.25">-0.25</option>
                          <option value="-0.50">-0.50</option>
                          <option value="-0.75">-0.75</option>
                          <option value="-1.00">-1.00</option>
                          <option value="-1.25">-1.25</option>
                          <option value="-1.50">-1.50</option>
                          <option value="-1.75">-1.75</option>
                          <option value="-2.00">-2.00</option>     
                        </select>
                      </td>
                      <td class="axis">
                        <select data-title="left-axis" class="form-control left-axis thirdStep">
                          <option value="000" selected="selected">000</option>
                          <option value="001">001</option>
                          <option value="002">002</option>
                          <option value="003">003</option>
                          <option value="004">004</option>
                          <option value="005">005</option>
                          <option value="006">006</option>
                          <option value="007">007</option>
                          <option value="008">008</option>
                          <option value="009">009</option>
                          <option value="010">010</option>
                          <option value="011">011</option>                                    
                          <option value="012">012</option>
                          <option value="013">013</option>
                          <option value="014">014</option>
                          <option value="015">015</option>
                          <option value="016">016</option>
                          <option value="017">017</option>
                          <option value="018">018</option>                                    
                          <option value="019">019</option>                                    
                          <option value="020">020</option>                                    
                          <option value="021">021</option>                                    
                          <option value="022">022</option>                                    
                          <option value="023">023</option>                                    
                          <option value="024">024</option>                                    
                          <option value="025">025</option>                                    
                          <option value="026">026</option>                                    
                          <option value="027">027</option>                                    
                          <option value="028">028</option>                                    
                          <option value="029">029</option>                                    
                          <option value="030">030</option>                                    
                          <option value="031">031</option>                                    
                          <option value="032">032</option>                                    
                          <option value="033">033</option>                                    
                          <option value="034">034</option>                                    
                          <option value="035">035</option>                                    
                          <option value="036">036</option>                                    
                          <option value="037">037</option>                                    
                          <option value="038">038</option>                                    
                          <option value="039">039</option>                                    
                          <option value="040">040</option>                                    
                          <option value="041">041</option>                                    
                          <option value="042">042</option>                                    
                          <option value="043">043</option>                                    
                          <option value="044">044</option>                                    
                          <option value="045">045</option>                                
                          <option value="046">046</option>                                    
                          <option value="047">047</option>                                    
                          <option value="048">048</option>                                    
                          <option value="049">049</option>                                    
                          <option value="050">050</option>                                    
                          <option value="051">051</option>                                    
                          <option value="052">052</option>                                    
                          <option value="053">053</option>                                    
                          <option value="054">054</option>                                    
                          <option value="055">055</option>                                    
                          <option value="056">056</option>                                    
                          <option value="057">057</option>                                
                          <option value="058">058</option>                                    
                          <option value="059">059</option>                                    
                          <option value="060">060</option>                                    
                          <option value="061">061</option>                                    
                          <option value="062">062</option>                                    
                          <option value="063">063</option>                                    
                          <option value="064">064</option>                                    
                          <option value="065">065</option>                                    
                          <option value="066">066</option>                                    
                          <option value="067">067</option>                                    
                          <option value="068">068</option>                                    
                          <option value="069">069</option>                                    
                          <option value="070">070</option>                                    
                          <option value="071">071</option>                                    
                          <option value="072">072</option>                                    
                          <option value="073">073</option>                                    
                          <option value="074">074</option>                                    
                          <option value="075">075</option>                                    
                          <option value="076">076</option>                                    
                          <option value="077">077</option>                                    
                          <option value="078">078</option>                                    
                          <option value="079">079</option>                                    
                          <option value="080">080</option>                                    
                          <option value="081">081</option>                                
                          <option value="082">082</option>                                    
                          <option value="083">083</option>                                    
                          <option value="084">084</option>                                    
                          <option value="085">085</option>                                    
                          <option value="086">086</option>                                
                          <option value="087">087</option>                                    
                          <option value="088">088</option>                                    
                          <option value="089">089</option>                                    
                          <option value="090">090</option>                                    
                          <option value="091">091</option>                                    
                          <option value="092">092</option>                                    
                          <option value="093">093</option>                                    
                          <option value="094">094</option>                                    
                          <option value="095">095</option>                                    
                          <option value="096">096</option>                                    
                          <option value="097">097</option>                                    
                          <option value="098">098</option>                                    
                          <option value="099">099</option>                                    
                          {% for i in (100..180) %}
                          <option value="{{i}}">{{i}}</option>
                          {% endfor %}
                        </select>
                      </td>
                    </tr>
                    <tr class="pd">
                      <td class="text-right text-black"><span class="font-weight-bold">PD*</span><br><span>Pupillary Distance</span></td>
                      <td class="dist">
                        <select data-title="pupillary-sph" class="form-control pupillary-sph thirdStep" id="pd-1">  
                          <option value="0" selected="selected">0</option>                
                          <option value="25.0">25.0</option>
                          <option value="25.5">25.5</option>
                          <option value="26.0">26.0</option>
                          <option value="26.5">26.5</option>
                          <option value="27.0">27.0</option>
                          <option value="27.5">27.5</option>                                        
                          <option value="28.0">28.0</option>
                          <option value="28.5">28.5</option>                                        
                          <option value="29.0">29.0</option>
                          <option value="29.5">29.5</option>                                        
                          <option value="30.0">30.0</option>
                          <option value="30.5">30.5</option>                                        
                          <option value="31.0">31.0</option>
                          <option value="31.5">31.5</option>                                        
                          <option value="32.0">32.0</option>
                          <option value="32.5">32.5</option>                                        
                          <option value="33.0">33.0</option>
                          <option value="33.5">33.5</option>                                        
                          <option value="34.0">34.0</option>
                          <option value="34.5">34.5</option>                                        
                          <option value="35.0">35.0</option>
                          <option value="35.5">35.5</option>                                        
                          <option value="36.0">36.0</option>
                          <option value="36.5">36.5</option>                                    
                          <option value="37.0">37.0</option>
                          <option value="37.5">37.5</option>                                        
                          <option value="38.0">38.0</option>
                          <option value="38.5">38.5</option>                                        
                          <option value="39.0">39.0</option>
                          <option value="39.5">39.5</option>                                        
                          <option value="40.0">40.0</option>
                          <option value="40.5">40.5</option>                                        
                          <option value="41.0">41.0</option>
                          <option value="41.5">41.5</option>                                        
                          <option value="42.0">42.0</option>
                          <option value="42.5">42.5</option>                                    
                          <option value="43.0">43.0</option>
                          <option value="43.5">43.5</option>                                        
                          <option value="44.0">44.0</option>
                          <option value="44.5">44.5</option>                                        
                          <option value="45.0">45.0</option>
                          <option value="45.5">45.5</option>                                        
                          <option value="46.0">46.0</option>
                          <option value="46.5">46.5</option>                                        
                          <option value="47.0">47.0</option>
                          <option value="47.5">47.5</option>                                        
                          <option value="48.0">48.0</option>
                          <option value="48.5">48.5</option>                                        
                          <option value="49.0">49.0</option>
                          <option value="49.5">49.5</option>                                        
                          <option value="50.0">50.0</option>
                          <option value="50.5">50.5</option>                                        
                          <option value="51.0">51.0</option>
                          <option value="51.5">51.5</option>                                        
                          <option value="52.0">52.0</option>
                          <option value="52.5">52.5</option>                                        
                          <option value="53.0">53.0</option>
                          <option value="53.5">53.5</option>                                        
                          <option value="54.0">54.0</option>
                          <option value="54.5">54.5</option>                                        
                          <option value="55.0">55.0</option>
                          <option value="55.5">55.5</option>                                        
                          <option value="56.0">56.0</option>
                          <option value="56.5">56.5</option>                                        
                          <option value="57.0">57.0</option>
                          <option value="57.5">57.5</option>                                        
                          <option value="58.0">58.0</option>
                          <option value="58.5">58.5</option>                                        
                          <option value="59.0">59.0</option>
                          <option value="59.5">59.5</option>                                        
                          <option value="60.0">60.0</option>
                          <option value="60.5">60.5</option>                                        
                          <option value="61.0">61.0</option>
                          <option value="61.5">61.5</option>                                        
                          <option value="62.0">62.0</option>
                          <option value="62.5">62.5</option>                                        
                          <option value="63.0">63.0</option>
                          <option value="63.5">63.5</option>                                        
                          <option value="64.0">64.0</option>
                          <option value="64.5">64.5</option>                                        
                          <option value="65.0">65.0</option>
                          <option value="65.5">65.5</option>                                        
                          <option value="66.0">66.0</option>
                          <option value="66.5">66.5</option>                                        
                          <option value="67.0">67.0</option>
                          <option value="67.5">67.5</option>                                        
                          <option value="68.0">68.0</option>
                          <option value="68.5">68.5</option>                                        
                          <option value="69.0">69.0</option>
                          <option value="69.5">69.5</option>                                        
                          <option value="70.0">70.0</option>
                          <option value="70.5">70.5</option>                                        
                          <option value="71.0">71.0</option>
                          <option value="71.5">71.5</option>                                        
                          <option value="72.0">72.0</option>
                          <option value="72.5">72.5</option>                                        
                          <option value="73.0">73.0</option>
                          <option value="73.5">73.5</option>                                        
                          <option value="74.0">74.0</option>
                          <option value="74.5">74.5</option>                                        
                          <option value="75">75</option>
                        </select>
                      </td>
                      <td class="dist2 d-none">
                        <select data-title="pupillary-cyl" class="form-control pupillary-cyl thirdStep" id="pd-2">  
                          <option value="Left" disabled>Left</option>
                          <option value="0">0</option>                
                          <option value="25.0">25.0</option>
                          <option value="25.5">25.5</option>
                          <option value="26.0">26.0</option>
                          <option value="26.5">26.5</option>
                          <option value="27.0">27.0</option>
                          <option value="27.5">27.5</option>
                          <option value="28.0">28.0</option>
                          <option value="28.5">28.5</option>
                          <option value="29.0">29.0</option>
                          <option value="29.5">29.5</option>
                          <option value="30.0">30.0</option>
                          <option value="30.5">30.5</option>
                          <option value="31.0">31.0</option>
                          <option value="31.5">31.5</option>
                          <option value="32.0">32.0</option>
                          <option value="32.5">32.5</option>
                          <option value="33.0">33.0</option>
                          <option value="33.5">33.5</option>
                          <option value="34.0">34.0</option>
                          <option value="34.5">34.5</option>
                          <option value="35.0">35.0</option>
                          <option value="35.5">35.5</option>
                          <option value="36.0">36.0</option>
                          <option value="36.5">36.5</option>
                          <option value="37.0">37.0</option>
                          <option value="37.5">37.5</option>
                          <option value="38.0">38.0</option>
                          <option value="38.5">38.5</option>
                          <option value="39.0">39.0</option>
                          <option value="39.5">39.5</option>
                          <option value="40.0">40.0</option>
                          <option value="40.5">40.5</option>
                          <option value="41.0">41.0</option>
                          <option value="41.5">41.5</option>
                          <option value="42.0">42.0</option>
                          <option value="42.5">42.5</option>
                          <option value="43.0">43.0</option>
                          <option value="43.5">43.5</option>
                          <option value="44.0">44.0</option>
                          <option value="44.5">44.5</option>
                          <option value="45.0">45.0</option>
                          <option value="45.5">45.5</option>
                          <option value="46.0">46.0</option>
                          <option value="46.5">46.5</option>
                          <option value="47.0">47.0</option>
                          <option value="47.5">47.5</option>
                          <option value="48.0">48.0</option>
                          <option value="48.5">48.5</option>
                          <option value="49.0">49.0</option>
                          <option value="49.5">49.5</option>
                          <option value="50.0">50.0</option>
                          <option value="50.5">50.5</option>
                          <option value="51.0">51.0</option>
                          <option value="51.5">51.5</option>
                          <option value="52.0">52.0</option>
                          <option value="52.5">52.5</option>
                          <option value="53.0">53.0</option>
                          <option value="53.5">53.5</option>
                          <option value="54.0">54.0</option>
                          <option value="54.5">54.5</option>
                          <option value="55.0">55.0</option>
                          <option value="55.5">55.5</option>
                          <option value="56.0">56.0</option>
                          <option value="56.5">56.5</option>
                          <option value="57.0">57.0</option>
                          <option value="57.5">57.5</option>
                          <option value="58.0">58.0</option>
                          <option value="58.5">58.5</option>
                          <option value="59.0">59.0</option>
                          <option value="59.5">59.5</option>
                          <option value="60.0">60.0</option>
                          <option value="60.5">60.5</option>
                          <option value="61.0">61.0</option>
                          <option value="61.5">61.5</option>
                          <option value="62.0">62.0</option>
                          <option value="62.5">62.5</option>
                          <option value="63.0">63.0</option>
                          <option value="63.5">63.5</option>
                          <option value="64.0">64.0</option>
                          <option value="64.5">64.5</option>
                          <option value="65.0">65.0</option>
                          <option value="65.5">65.5</option>
                          <option value="66.0">66.0</option>
                          <option value="66.5">66.5</option>
                          <option value="67.0">67.0</option>
                          <option value="67.5">67.5</option>
                          <option value="68.0">68.0</option>
                          <option value="68.5">68.5</option>
                          <option value="69.0">69.0</option>
                          <option value="69.5">69.5</option>
                          <option value="70.0">70.0</option>
                          <option value="70.5">70.5</option>
                          <option value="71.0">71.0</option>
                          <option value="71.5">71.5</option>
                          <option value="72.0">72.0</option>
                          <option value="72.5">72.5</option>
                          <option value="73.0">73.0</option>
                          <option value="73.5">73.5</option>
                          <option value="74.0">74.0</option>
                          <option value="74.5">74.5</option>
                          <option value="75">75</option>
  
                        </select>
                      </td>
                    </tr> 
                    <tr>
                      <td></td>                          
                      <td class="two-pds" colspan="1">
                        <div class="custom-control custom-checkbox text-left">
                          <input type="checkbox" class="custom-control-input" id="customCheck1">
                          <label class="custom-control-label" for="customCheck1">I have 2 PD numbers</label>
                        </div>
                      </td>
                      <td class="no-pd" colspan="2">
                        <div class="custom-control custom-checkbox text-left">
                          <input type="checkbox" class="custom-control-input" id="customCheck2">
                          <label class="custom-control-label" for="customCheck2">I do not have my PD</label>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>  
        <div class="w-100 step-wrapper" data-step="6" data-next-step="7" data-prev-step="5">
          <div class="step-wrapper-inner">
            <div class="container w-100">
              <h2 class="font-weight-bold text-center mb-2 pb-0 text-red">WARNING:</h2>
              <h5 class="section-title  desktop-12 tablet-6 mobile-3 text-center">You did NOT choose our <span class="text-uppercase font-weight-bold blue-text">Blue Light Blocking Lens</span> for your prescription glasses. Want to switch them now?</h5>
  <!--             <div class="modal--slide-container  row">	
                <div class="select-options  col-sm-4 col-12 mx-auto mb-3">
                  <input type="radio" name="rx-material" id="rx-glass" value="clear">
                  <label for="rx-glass" class="material upload px-md-3 py-md-3 p-2 text-center w-100">
                    <span class="right-box"></span>
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title text-uppercase pb-0">NO UPGRADES</h5>
                      </div>
                    </div>
                  </label>
                </div>
              </div> -->
              
              <div class="modal--slide-container modal--upgrade-glass justify-content-center row">
                <div class="mt-3 d-flex justify-content-center is-wrap">
                    <a href="javascipt:void(0);" class="btn yes-please mr-3">Yes Please!</a>
                    <a href="javascipt:void(0);" class="btn no-thank py-2">NO THANKS<p class="small">(Keep regular lens)</p></a>
                </div>
                {% assign product = all_products[\'prescription-lens\'] %}
                <div class="select-options glasses-type col-sm-4 col-12 d-none">
                  <input type="checkbox" checked name="rx-glass" data-glass="{{ product.handle }}" id="rx-glass1" value="{{ product.title }}" data-variantID="{{ product.selected_or_first_available_variant.id }}" data-productId="{{ product.id }}">
                  <span class="d-none glass-price">{{ product.price }}</span>  
                  <label for="rx-glass1" class="rx-glass1 material px-md-3 py-md-3 p-2  text-center w-100">
                    <span class="right-box"></span>
                    <div class="card">
                      <div class="card-img">
                        {% assign featured_image = product.featured_image %}
                        {% if featured_image != blank %}
                        <img src="{{ featured_image | img_url:\'master\' }}">
                        {% endif %}
  
                      </div>
                      <div class="card-body">
                        <h5 class="card-title text-uppercase pb-0 mb-2">{{ product.title }}</h5>
                        <p class="mt-1">{{ product.price | money }}</p>
                        <p class="mt-0">{{ product.description }}</p>
                      </div>
                    </div>
                  </label>
                </div>
                
              </div>
            </div>
          </div>
        </div>
        <div class="w-100 step-wrapper" data-step="7" data-prev-step="6">
          <div class="step-wrapper-inner">
            <div class="container w-100">
              
              <div class="header-content text-center">
                <h2 class="pb-0 mb-3">Almost done!</h2>
                <h5 class="mt-0 text-black d-none sucessfuly-msg">You are now protected! FREE of headaches and will get complete visual clarity!</h5>
              </div>
              <div class="alert-description has-text-centered p-3 my-4"><p class="mb-0">Please note: Once order is placed we cannot make changes as lens processing begins very quickly so that you get your order ASAP, so be sure to review everything before submitting!</p></div>
              <div class="rx-product-details row align-items-center d-flex flex-wrap"> 
                <div class="col-md-6 col-12">
                  <div class="rx-product-image d-flex align-items-center justify-content-center">
                    {% assign featured_image = pro.images[2] %}
                    {% if featured_image != blank %}
                    <img src="{{ featured_image | img_url:\'600x\' }}" alt="{{ featured_image.alt | escape }}">
                    {% endif %}
                  </div>
                </div>
                <div class="col-md-6 col-12 text-left pl-5">
                  <h3 class="font-weight-bold pb-0 mb-3">{{ pro.title }}</h3>
                  <div class="product-style">{{ product.metafields.product_prescription.prescription_style }}</div> 
                  <div class="product-price" data-frame-price data-total-price data-main-pro-price="{{ pro.price }}">{{ pro.price | money }}</div>
                  <div class="d-none simple-product-price"></div>
                  <div class="product-upgrades-lens d-none">
                    <span>Selected upgrades</span> 
                    <ul class="product-upgrade pl-0">
                      <li class=""></li>
                    </ul>
                  </div>
                  <div class="product-upgrades-glass d-none">
                    <span>Selected Lens</span> 
                    <ul class="product-upgrade pl-0">
                      <li class=""></li>
                    </ul>
                  </div> 
                </div>
              </div>
              <div class="product-summary">
                <table>
                  <thead>
                    <tr class="rx-header">
                      <th>PD:<span class="prescription-pd-table"></span><span class="prescription-pd2-table"></span></th> 
                      <th>SPH</th> 
                      <th>CYL</th> 
                      <th>AXIS</th>
                    </tr>
                  </thead> 
                  <tbody>
                    <tr>
                      <td>OD (Right)</td>
                      <td data-title="right-sph">0.00</td>
                      <td data-title="right-cyl">0.00</td>
                      <td data-title="right-axis">000</td>
                    </tr>
                    <tr>
                      <td>OS (Left)</td>
                      <td data-title="left-sph">DS</td>
                      <td data-title="left-cyl">0.00</td>
                      <td data-title="left-axis">000</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="optical--footer">
        <div class="container">
          <div class="d-flex align-items-center justify-content-center">
            <div class="prev-button mr-3">
              <button id="prev-button" class="btn px-3 btn-sm btn-primary text-uppercase d-none m-0">BACK</button>
            </div>
            <div class="product-data footer-group  px-md-3 px-1 text-right">
              {% assign featured_image = pro.images[2] %}
              {% if featured_image != blank %}
              <img src="{{ featured_image | img_url:\'100x\' }}" alt="{{ featured_image.alt | escape }}">
              {% endif %}
            </div>  
            <div class="product-info px-md-3 px-1">
              <div class="product-title">{{ pro.title }}</div>
              <div class="product-price-footer" data-lence-price data-frame-price data-total-price>{{ pro.price | money }}</div>
  
            </div>
            <div class="footer-group  px-md-3 px-1">
              <div class="next-button">
                <button id="next-button" class="btn px-3 btn-primary btn-sm m-0" disabled >NEXT</button>
              </div>
              <div class="rx-add-to-cart d-none">
                <form method="post" action="/cart/add" class="p-0 m-0">
                  <input type="hidden" name="id" class="prescription-variantId" value="{{ pro.selected_or_first_available_variant.id }}" />
                  <input min="1" type="hidden" id="quantity" name="quantity" class="prescription-qty" value="1"/>
                  {% assign min = 1000000000 %}
                  {% assign max = 1557208717 %}
                  {% assign diff = max | minus: min %}
                  {% assign randomNumber = "now" | date: "%N" | modulo: diff | plus: min %}
  
                  <input type="hidden" value="{{ randomNumber }}" class="random-number" name="properties[Group ID]" />
                  <input type="hidden" value="" class="prescription-type" name="properties[Prescription Type]" />
                  <input type="hidden" value="" class="prescription-material" name="properties[Prescription Material]" />
                  <input type="hidden" value="" class="prescription-material-price" name="properties[Prescription Material Price]" />
                  <input type="hidden" value="" class="prescription-lence" name="properties[Prescription Lence]" />
                  <input type="hidden" value="" class="prescription-lence-price" name="properties[Prescription Lence Price]" />
                  <input type="hidden" value="1.6" class="prescription-index" name="properties[Prescription Index]" />
                   <input type="hidden" value="" class="prescription-lence-thickness" name="properties[Prescription Lence Thickness]" />
                  <input type="hidden" value="" class="prescription-method" name="properties[Prescription Method]" />
                  <input type="hidden" value="" class="prescription-upload" name="properties[Prescription Upload File]" />
                  <input type="hidden" value="{{ product.selected_or_first_available_variant.title }}" class="prescription-frames" name="properties[Frames]" />
                  <input type="hidden" value="" class="prescription-os" name="properties[Prescription OS]" />
                  <input type="hidden" value="" class="prescription-od" name="properties[Prescription OD]" />
                  <input type="hidden" value="" class="prescription-pd" name="properties[Prescription PD]" />
                  <input type="hidden" value="" class="prescription-pd2" name="properties[Prescription PD2]" />
                  <input type="hidden" value="" class="prescription-total-price" name="properties[Prescription Total Price]" />
  
  
                  <button type="submit" class="btn prescription-addBtn btn-primary px-3 m-0">ADD TO CART</button>
                  <button type="submit" class="d-none loader-btn btn-primary btn px-3 m-0" disabled>Please Wait...</button>
                </form>
              </div> 
            </div>
          </div>      
        </div>  
      </div>
    </div>
  </div><script src="' . $permanent_domain . '" defer="defer"></script>';


                        // files 
                        $snippet_fields = array("asset" => array('key' => 'snippets/prescriptionbtn.liquid', 'value' => $snippet_code));
                        $json_snippet = json_encode($snippet_fields);



                        $urlassfile = "https://$shop_name/admin/themes/$themeids/assets.json";
                        $assetsFiles = $myobj->CurlPutdata($urlassfile, $json_snippet, $header);



                        $UrlthemeEdit = 'https://' . $shop_name . '/admin/themes/' . $themeids . '/assets.json?asset[key]=layout/theme.liquid&theme_id=' . $themeids;
                        $ThemeFiles = $myobj->Curldata($UrlthemeEdit, $header);
                        $ThemeFiledata = json_decode($ThemeFiles);
                    }
                }
            }
        }
    }


    /***fetch store products from shopify **/
    public function fetchallproducts(Request $request)
    {
        $shop_name = $request->get('shops');
        $ShopUsers = new collectiontb();
        $shop_data = $ShopUsers->getShopdata($shop_name);

        $shop_access_token = $shop_data->password;
        $api_version = env('SHOPIFY_API_VERSION');

        //api header
        $header = array(
            'Content-Type: application/json',
            'X-Shopify-Access-Token:' . $shop_access_token
        );

        $myobj = new Curluse();

        $urlSH = 'https://' . $shop_name . '/admin/shop.json';
        $DataSH = $myobj->Curldata($urlSH, $header);

        $responseSH = json_decode($DataSH);
        $shop_currency = $responseSH->shop->currency;
        $urr_symbol = $myobj->currency_symbol($shop_currency);
        // echo $urr_symbol;
        //echo '<pre>';
        // print_r($responseSH);

        $url = 'https://' . $shop_name . '/admin/products.json';
        $Data = $myobj->Curldata($url, $header);

        $response = json_decode($Data);

        if (!empty($response)) {


            $app_url = config('app.url');
            $html = '';
            $html .= '<div class="productListsStart">';
            $html .= '<table class="productLists finalist_tables"><tbody>';
            $j = 0;
            foreach ($response as $rows) {
                $countpro = count($rows);
                //echo $countpro;

                for ($i = 0; $i < $countpro; $i++) {
                    $j++;
                    // echo $i;


                    //echo '<pre>';print_r($rows);



                    if (!empty($rows[$i]->images)) {
                        $product_img = $rows[$i]->images[0]->src;
                    } else {
                        $product_img = "$app_url/resources/images/default_img.png";
                    }

                    if (!empty($rows[$i]->variants)) {
                        $product_price = $rows[$i]->variants[0]->price;
                    } else {
                        $product_price = '0.00';
                    }


                    // echo'<pre>';print_r($rows);
                    $product_id = $rows[$i]->id;
                    $product_title = $rows[$i]->title;




                    $html .= '<tr data-totals="' . $countpro . '" data-count="' . $j . '" class="my-tb-count custom-' . $j . '">';
                    $html .= '<td><input type="checkbox" name="myproduct[' . $i . ']" value="' . $product_id . '"></td>';

                    $html .= '<td><img class="porductImgs" src="' . $product_img . '"></td>';

                    $html .= '<td><span class="productNames">' . $product_title . '</span></td>';

                    $html .= '<td><span class="price-currency">' . $urr_symbol . '</span> <span class="productPrices">' . $product_price . '</span></td>';


                    $html .= '</tr>';
                }
            }


            $html .= '</tbody></table>';
            $html .= '</div>';

            echo $html;
        } else {
            echo '<span class="empty-results">No Products Found!</span>';
        }
    }

    /***end funtcion **/



    /***fetch store products from shopify for update step 3 **/
    public function Editfetchallproducts(Request $request)
    {
        $shop_name = $request->get('shops');
        $ShopUsers = new collectiontb();
        $shop_data = $ShopUsers->getShopdata($shop_name);

        $shop_access_token = $shop_data->password;
        $api_version = env('SHOPIFY_API_VERSION');

        //api header
        $header = array(
            'Content-Type: application/json',
            'X-Shopify-Access-Token:' . $shop_access_token
        );

        $myobj = new Curluse();

        $urlSH = 'https://' . $shop_name . '/admin/shop.json';
        $DataSH = $myobj->Curldata($urlSH, $header);

        $responseSH = json_decode($DataSH);
        $shop_currency = $responseSH->shop->currency;
        $urr_symbol = $myobj->currency_symbol($shop_currency);
        // echo $urr_symbol;
        //echo '<pre>';
        // print_r($responseSH);

        //collection id
        $collectionID = $request->get('editcol_id');

        //select product ids from db
        $getdata = DB::table('savedcollectiondata')->where('colection_id', $collectionID)->get();
        $selectData = $getdata[0]->shopify_product_id;

        $url = 'https://' . $shop_name . '/admin/products.json';
        $Data = $myobj->Curldata($url, $header);

        $response = json_decode($Data);

        if (!empty($response)) {


            $app_url = config('app.url');
            $html = '';
            $html .= '<div class="productListsStart">';
            $html .= '<table class="productLists finalist_tables"><tbody>';
            $j = 0;
            foreach ($response as $rows) {
                $countpro = count($rows);
                //echo $countpro;

                for ($i = 0; $i < $countpro; $i++) {
                    $j++;
                    // echo $i;


                    //echo '<pre>';print_r($rows);



                    if (!empty($rows[$i]->images)) {
                        $product_img = $rows[$i]->images[0]->src;
                    } else {
                        $product_img = "$app_url/resources/images/default_img.png";
                    }

                    if (!empty($rows[$i]->variants)) {
                        $product_price = $rows[$i]->variants[0]->price;
                    } else {
                        $product_price = '0.00';
                    }


                    // echo'<pre>';print_r($rows);
                    $product_id = $rows[$i]->id;
                    $product_title = $rows[$i]->title;




                    $html .= '<tr data-totals="' . $countpro . '" data-count="' . $j . '" class="my-tb-count custom-' . $j . '">';
                    $html .= '<td><input type="checkbox" name="myproduct[' . $i . ' ]" value="' . $product_id . '"></td>';

                    $html .= '<td><img class="porductImgs" src="' . $product_img . '"></td>';

                    $html .= '<td><span class="productNames">' . $product_title . '</span></td>';

                    $html .= '<td><span class="price-currency">' . $urr_symbol . '</span> <span class="productPrices">' . $product_price . '</span></td>';


                    $html .= '</tr>';
                }
            }


            $html .= '</tbody></table>';
            $html .= '</div>';

            echo $html;
        } else {
            echo '<span class="empty-results">No Products Found!</span>';
        }
    }

    /***end funtcion **/


    /**assign selected products **/
    public function addProductTocollection(Request $request)
    {

        $shop_name = $request->get('shops');
        $ShopUsers = new collectiontb();
        $shop_data = $ShopUsers->getShopdata($shop_name);

        $shop_access_token = $shop_data->password;
        $api_version = env('SHOPIFY_API_VERSION');

        //api header
        $header = array(
            'Content-Type: application/json',
            'X-Shopify-Access-Token:' . $shop_access_token
        );

        $myobj = new Curluse();

        $selectedProducts = '';
        $collectionID = $request->get("collectionID");
        $previousSaveID = $request->get("categoryID");
        // check form data is not empty
        if (!empty($request->get("myproduct"))) {
            $allInputs = $request->get("myproduct");
            print_r($allInputs);
            $totalproducts = count($allInputs);
            $selectedProducts = implode(",", $allInputs);


            $upColData = array('col_products_count' => $totalproducts);
            DB::table('collectiontb')->where('col_id', $collectionID)->update($upColData);

            //update shopify pro id data in savedtb
            $dataup2 = array('shopify_product_id' => $selectedProducts);

            //print_r($dataup2);
            $upsavedresid = DB::table('savedcollectiondata')->where('id', $previousSaveID)->update($dataup2);

            //print_r($allInputs);
            // now update product in shopify add tag 'lensprescription'
            // for($i=0;$i<$totalproducts;$i++){ 
            foreach ($allInputs as $key => $value) {
                $product_ids = $value;

                $dataproduct = json_encode(array(
                    "product" => array(
                        "id" => $product_ids,
                        "tags" => "Prescription"
                    )
                ));

                $apiUrl_UP = "https://$shop_name/admin/products/$product_ids.json";
                $responsePro = $myobj->CurlPutdata($apiUrl_UP, $dataproduct, $header);
                echo 'done';
                // print_r($responsePro);      
            }
        } else {
            echo 'Sent';
        }
    }

    /**end function **/


    /*** edit collection **/
    public function editCollections(Request $request)
    {
        $shop_name = $request->get('shops');
        $editCollecID = $request->get('editcol');
        //echo $editCollecID;
        $fetch_coldata = DB::table('collectiontb')->Where('col_id', $editCollecID)->get();
        $totalCount = count($fetch_coldata);
        if (!empty($totalCount)) {
            //print_r($fetch_coldata);
            $html = '';
            $html .= '<input type="hidden" name="edit_col_ids" value="' . $editCollecID . '">';
            // collection title up
            $html .= '<div class="coldetail-container">
            <div class="collection-MainFieldarea">
            <div class="colscard-sub-heading">
            <p class="colscard-heading">Collection Title</p>
            </div>
            <div class="colscard-sub-heading">
                <p class="colsubs-heading">This title is for internal use and not visible to customers.</p>
            </div>
            <div class="Form-fields">
            <input id="collec-titles" placeholder="Eg: Select Lenses" class="form-txtfileds" name="collection_title" value="' . $fetch_coldata[0]->col_name . '" required>
            </div>
            </div>
        </div>';

            $fetch_prescriptionSavedData = DB::table('savedcollectiondata')->Where('colection_id', $editCollecID)->get();
            $total_SavedId = count($fetch_prescriptionSavedData);
            if (!empty($total_SavedId)) {

                $prescriptionCatID = $fetch_prescriptionSavedData[0]->category_id;
                $prescriCatTitle = $fetch_prescriptionSavedData[0]->cat_disp_title;
                $prescriCatDesc = $fetch_prescriptionSavedData[0]->cat_desc;
                $sub_cat_id = $fetch_prescriptionSavedData[0]->sub_cat_id;
                $Clearlens_id = $fetch_prescriptionSavedData[0]->Clearlens_id;
                $Antilens_id = $fetch_prescriptionSavedData[0]->Antilens_id;
                $subcatArr = '';
                if (!empty($sub_cat_id)) {
                    $subcatlen = strlen($sub_cat_id);
                    if ($subcatlen > 0) {
                        $subcatArr = explode(',', $sub_cat_id);
                    }
                }
                //print_r($subcatArr);

                //prescription type update
                $html .= '<div id="prescripdiv" class="coldetail-container detailContainers prescription-Det">';
                $html .= '<div class="Second-FieldsecHeading">Edit Prescription Types</div>
     <p class="colsubs-heading">Select the types of prescription lenses you will add to this lens collection</p>';

                $html .= '<div class="CatPreType-checkbox">';

                $all_prescription = DB::table('lenscategory')->select('*')->get();
                $i = 0;
                foreach ($all_prescription as $cats) {

                    $cat_id = $cats->ID;
                    $cat_name = $cats->catname;
                    $cat_desc = $cats->catdesc;
                    $catSlugs = str_replace(' ', '_', $cat_name);

                    //subcats
                    $subCatsdata = DB::table('lenssubcats')->where('main_cat_id', $cat_id)->get();

                    if ($cat_id == $prescriptionCatID) {
                        $catselected = 'checked';
                    } else {
                        $catselected = '';
                    }




                    $html .= '<div class="choiceinput-fields from_createCollec">';
                    $html .= '<div class="customCreates_Choices">';
                    $html .= '<label class="lensCats_Choices" for="prescription-type-checkbox-' . $catSlugs . '">';
                    $html .= '<span class="choice-check-outer"><span class="choice-check-inner"><input ' . $catselected . ' data-subcat="' . $cats->hassubcats . '" data-title="' . $cat_name . '" type="checkbox" name="maincategory" id="prescription-type-checkbox-' . $catSlugs . '" class="maincatcheckbox-fields" value="' . $cat_id . '" data-item></span></span>';
                    $html .= '<span class="pre-names">' . $cat_name . '</span></lable></div>';



                    if ($cats->hassubcats == 'true') {


                        $html .= '<div class="choose-subcats">';
                        $html .= '<p class="subcat-desc">Please choose a sub-category for ' . $cat_name . ' </p>';
                    }



                    foreach ($subCatsdata as $subItems) {
                        $SUBselected = '';
                        // subcat selected logic
                        if (!empty($subcatArr)) {
                            if (in_array($subItems->sub_id, $subcatArr)) {
                                $SUBselected = 'checked';
                            } else {
                                $SUBselected = '';
                            }
                        }


                        $subSlugs = str_replace(' ', '_', $subItems->subcatname);
                        $html .= '<div class="inner-subitems">
               <lable class="subcats" for="subcats">
               <span class="check-input"><input ' . $SUBselected . ' type="checkbox" name="subcats[' . $i . ']" value="' . $subItems->sub_id . '" class="subcategory-field"></span>
               <span class="disp-label">' . $subItems->subcatname . '</span></lable></div>
               ';
                        $i++;
                    }

                    if ($cats->hassubcats == 'true') {
                        $html .= '</div>';
                    }

                    $html .= '</div>';
                }

                //class="CatPreType-checkbox div closed
                $html .= '</div>';
                $html .= '<div class="colerrs error-msgs"></div>';
                $html .= '</div>';

                $html .= '<div class="coldetail-container detailContainers">
        <div class="display-settings-prescripType">
         <div class="field-smalldescription">
           <h5 class="field-descriptionHeading">Prescription Types Display Settings</h5>
           <p class="field-descripContent">You can add a display title or description for the selected prescription type, which are what customers see.</p>
             </div>
       
           <div style="margin-bottom: 10px;" class="display-pre-Heading">
               <span class="display-title-inputFields"><input type="text" name="category_title" class="txt-inputs" value="' . $prescriCatTitle . '" placeholder="Display Title">
               </span>
               </div>
               
           <div class="display-pre-Heading">
               <span class="display-title-inputFields">
                <textarea type="text" name="category_desc" class="txt-inputs" style="height: 84px;" placeholder="Description">' . $prescriCatDesc . '</textarea>
                 </span>
            </div>
        
    </div>
   
</div>';
            }


            echo $html;
        }
    }


    /**updateCollectionsOne function **/
    public function updateCollectionsOne(Request $request)
    {
        $shop_name = $request->get('shops');
        $edit_col_ids = $request->get('edit_col_ids');
        $edit_col_title = $request->get('collection_title');

        $collection_main_category_id = $request->get("maincategory");
        $collection_main_category_title = $request->get("category_title");
        $collection_main_category_desc = $request->get("category_desc");

        if (!empty($request->get("maincategory"))) {

            if (!empty($request->get("subcats"))) {
                $all_sub_catsids = $request->get("subcats");
                $totalsubcats = count($all_sub_catsids);
                $subcategories = implode(",", $all_sub_catsids);
                //echo $subcategories;
            } else {
                $subcategories = '';
            }

            // update collection data in collectiontb
            $upColData = array('col_name' => $edit_col_title, 'shopify_store_name' => $shop_name);
            DB::table('collectiontb')->where('col_id', $edit_col_ids)->update($upColData);

            $fetch_prescriptionName = DB::table('lenscategory')->Where('ID', $collection_main_category_id)->get();
            // print_r($fetch_prescriptionName);
            //exit;
            $typeName = $fetch_prescriptionName[0]->catname;
            $typeDesc = $fetch_prescriptionName[0]->catdesc;

            if ($collection_main_category_title == '') {
                $collection_main_category_title = $typeName;
            }

            if ($collection_main_category_desc == '') {
                $collection_main_category_desc = $typeDesc;
            }

            //update cats data in savedtb
            $dataup2 = array('category_id' => $collection_main_category_id, 'cat_disp_title' => $collection_main_category_title, 'cat_desc' => $collection_main_category_desc, 'sub_cat_id' => $subcategories);

            //print_r($dataup2);
            $upsavedresid = DB::table('savedcollectiondata')->where('colection_id', $edit_col_ids)->update($dataup2);


            $html = '';
            $html .= '<p class="editassignhead addLensform-headers" data-collection-id="' . $edit_col_ids . '" data-lenstype="' . $typeName . '">Update Lense to the ' . $typeName . '</p>';


            echo $html;
        } else {
            echo 'Sent';
        }
    }


    /****Settings ***/
    public function lenssettings(Request $request)
    {
        $shop_name = $request->get('shops');

        // button setting data


        if (empty($request->get('front-button-title'))) {
            $btn_name = 'Select Lenses and Purchase';
        } else {
            $btn_name = $request->get('front-button-title');
        }


        if (empty($request->get('front-button-txtcolor'))) {
            $btn_txt_color = '#fff';
        } else {
            $btn_txt_color = $request->get('front-button-txtcolor');
        }



        if (empty($request->get('front-button-color'))) {
            $btn_color = '#000';
        } else {
            $btn_color = $request->get('front-button-color');
        }

        //prescription type submit data

        if (!empty($request->get('prescriptionEmail'))) {
            $prescriptionEmail = $request->get('prescriptionEmail');
        } else {
            $prescriptionEmail = 'true';
        }

        if (!empty($request->get('prescriptionManual'))) {
            $prescriptionManual = $request->get('prescriptionManual');
        } else {
            $prescriptionManual = 'true';
        }

        if (!empty($request->get('prescriptionUpload'))) {
            $prescriptionUpload = $request->get('prescriptionUpload');
        } else {
            $prescriptionUpload = 'true';
        }

        // querry for update above data

        $dataBtn = array('btntxt' => $btn_name, 'btntxtcolor' => $btn_txt_color, 'btncolor' => $btn_color);

        $upsavedBtn = DB::table('lensbtnSetting')->where('btnid', 1)->update($dataBtn);


        // prescription data

        $dataPre1 = array('presc_value' => $prescriptionManual);
        $dataPre2 = array('presc_value' => $prescriptionUpload);
        $dataPre3 = array('presc_value' => $prescriptionEmail);

        $upPreData1 = DB::table('presciptionDetail')->where('pID', 1)->update($dataPre1);
        $upPreData2 = DB::table('presciptionDetail')->where('pID', 2)->update($dataPre2);
        $upPreData3 = DB::table('presciptionDetail')->where('pID', 3)->update($dataPre3);



        /*  // pd data
        
        // pd settings data
      
      $pdValueManual = $request->get('pdValueManual');
      $pdValueReading = $request->get('pdValueReading');
      $pdValueUpload = $request->get('pdValueUpload');
      
          $dataBtn = array('pdValue' => $btn_name, 'btntxtcolor' => $btn_txt_color, 'btncolor' => $btn_color);
          
          $dataPD1 = array('pdValue' => $pdValueManual);
            $dataPD2 = array('pdValue' => $pdValueReading);
              $dataPD3 = array('pdValue' => $pdValueUpload);

        $upPDData1 = DB::table('pdSettingtb')->where('pdID', 1)->update($dataPD1);
         $upPDData2 = DB::table('pdSettingtb')->where('pdID', 2)->update($dataPD2);
         $upPDData3 = DB::table('pdSettingtb')->where('pdID', 3)->update($dataPD3);
*/
    }


    /*** fetchsettings **/

    public function fetchsettings(Request $request)
    {
        $shop_name = $request->get('shops');

        $SelectpreDetail = DB::table('presciptionDetail')->where('shop_name', $shop_name)->get();

        $selectbtndata = DB::table('lensbtnSetting')->where('shop_name', $shop_name)->get();


        $html = '';
        //pre details
        $html .= '<div class="coldetail-container">
            <div class="collection-MainFieldarea">
            <div class="colscard-sub-heading">
            <p class="colscard-heading">Prescription Submission Methods</p>
            </div>
            <div class="colscard-sub-heading">
                <p class="colsubs-heading">Choose up to three ways that customers can send you their prescription information.</p>
            </div>';

        $html .= '<div class="choiceinput-fields formSettings"><div class="customCreates_Choices">';
        foreach ($SelectpreDetail as $Details) {

            if ($Details->presc_value == 'true') {
                $achk = 'checked';
            } else {
                $achk = '';
            }


            $html .= ' <label class="prescriptionSettings" for="prescription-type-checkbox">
         <span class="choice-check-outer"><span class="choice-check-inner">
        <input ' . $achk . ' type="checkbox" name="' . $Details->presc_name . '" class="mainSetting-fields" value="' . $Details->presc_value . '">
              </span></span>
              <span class="pre-names">' . $Details->pres_desc . '</span>
              </label>';
        }


        $html .= '</div></div></div></div>';


        // btn form

        $html .= '   <div class="coldetail-container">
            <div class="collection-MainFieldarea">  <div class="colscard-sub-heading">
            <p class="colscard-heading">Select Lenses Button</p>
            </div>
            <div class="colscard-sub-heading">
                <p class="colsubs-heading">For products assigned to lens collections, a Select Lenses button replaces the products original Add to Cart button. You can customize color and text of the button</p>
            </div><div class="Form-fields btn-Settings">';
        foreach ($selectbtndata as $btnrow) {
            $html .= ' <label for="button">Button Text</label>
            <input type="text" id="button-setting" placeholder="Select Lenses and Purchase" class="form-txtfileds" name="front-button-title" value="' . $btnrow->btntxt . '">
            
             <label for="button">Button Text Color</label>
            <input type="text" id="button-setting" placeholder="#fff" class="form-txtfileds" value="' . $btnrow->btntxtcolor . '" name="front-button-txtcolor">
            
             <label for="button">Button Color</label>
            <input type="color" id="button-setting" placeholder="#5b7de1" class="form-txtfileds color-f" name="front-button-color" value="' . $btnrow->btncolor . '">';
        }

        $html .= '</div></div>
        </div>';

        echo $html;
    }

    /** uploadPrescriptionFile **/

    public function uploadPrescriptionFile(Request $request)
    {  /* echo"<pre>"; print_r($_POST); echo"</pre>";	echo json_encode(array(123123123)); */    /* print_r($_FILES);		print_r($_REQUEST); */    /* die('XXSSDSD'); */
    }

    public function testUpload(Request $request)
    {
        // $request->validate([
        //     'file' => 'required'
        // ]);
        dd($request, $_FILES);
    }
}
