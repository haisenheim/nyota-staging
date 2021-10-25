<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Gallery;
use App\Models\ProductAttributeType;
use App\Models\ProductAttribute;
use App\Models\ProductVariation;
use App\Models\User;
use Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Spatie\Permission\Traits\HasRoles;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Image;


class ProductImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if(!empty($row['attribute_type'])){
          $attribute_types = explode(", ",$row['attribute_type']);
          $attr_type_array = array();
          foreach ($attribute_types as $a_tkey => $a_tvalue) {
              $diff_types = explode("|",$a_tvalue);
              $test = array();
              foreach ($diff_types as $key => $value) {
                  $test[] = $value;
              }
              $attr_type_array[] = $test;
          }
          $attributearray = array();
          $attributes = explode(", ",$row['attribute']);
          foreach ($attributes as $akey => $avalue) {
              $diff_attrs = explode("|",$avalue);
              $single_attribute = array();
              foreach ($diff_attrs as $attrkey => $attrvalue) {
               // $type_id = ProductAttributeType::where('name',trim($attrvalue))->where('status',0)->first();
               //    $type_ids[] = $type_id->id;
                  $single_attribute[] = $attrvalue;
              }
              $attributearray[] = $single_attribute;
          }
          $prices = explode(", ",$row['price']);
          $saleprice = explode(", ",$row['sale_price']);
          $quantity = explode(", ", $row['quantity']);
        }

        $i = 0;
        $j = 0;

        $type_ids = array();
        $finalarray = array();
        foreach ($attr_type_array as $key => $value) {
          foreach ($value as $jkey => $jvalue) {
           $dat = ProductAttributeType::where('name',trim($jvalue))->where('status',0)->first();
            if(!empty($dat)){
              $att_type_id = $dat['id'];
            }else{
              $att_type = ProductAttributeType::create([
                'name' => trim($jvalue),
                'language_id'   => 1,
              ]);
              $att_type_id = $att_type->id;
            }
            $att = ProductAttribute::where('name',trim($attributearray[$key][$jkey]))->where('status',0)->where('type_id',$att_type_id)->first();
            if(!empty($att)){
              $att_id = $att['id'];
            }else{
              $attribute = ProductAttribute::create([
                'name'            => trim($attributearray[$key][$jkey]),
                'language_id'     => 1,
                'type_id'         => $att_type_id,
              ]);
              $att_id = $attribute->id;
            }


            if($j < count($test)){
              $type_ids[] = $att_type_id;
            }



            //$finalarray[$i][$jvalue] = $attributearray[$key][$jkey];
            $finalarray[$i]['a_'.$att_type_id] = $att_id;
           $j++;
          }
          $finalarray[$i]['rprice'] = $prices[$key];
          $finalarray[$i]['sprice'] = $saleprice[$key];
          $finalarray[$i]['quantity'] = $quantity[$key];
          $i++;
          
        }
        $attribute_type_ids = '';
        if(!empty($type_ids)){
          $attribute_type_ids = implode(', ', $type_ids);
        }
        if(!empty($finalarray)){
          $json_data = json_encode($finalarray);
        }else{
          $json_data = '';
        }
        
        if(!empty($row['category'])){
          $category = Category::where('name',$row['category'])->where('status',0)->first();
          if(!empty($category)){
            $category_id = $category->id;
          }else{
            $category = Category::create([
            'name'             => $row['category'],
            'slug'             => str_slug($row['category'], '_'),
            'parent_id'        => 0,
            'is_active'        => 0,
            'language_id'      => 1, 
            ]);
            $category_id = $category->id;
          }
        }
        if(!empty($row['child_category'])){
          $sub_category = Category::where('parent_id',$category_id)->where('status',0)->where('name',$row['child_category'])->first();
          if(!empty($sub_category)){
            $sub_category_id = $sub_category->id;
          }else{
            $sub_category = Category::create([
            'name'             => $row['child_category'],
            'slug'             => str_slug($row['child_category'], '_'),
            'parent_id'        => $category_id,
            'is_active'        => 0,
            'language_id'      => 1, 
            ]);
            $sub_category_id = $sub_category->id;
          }
        }
        if(Auth::user()->isvendor()){
          $vendor_user = Auth::user();
          $user_id = $vendor_user->id;
        }else{
          if(!empty($row['user'])){
            $user = User::join('role_user','role_user.user_id','=','users.id')->select('users.id')->where('role_user.role_id','=',3)->where('users.first_name',$row['user'])->first();
           
            if(!empty($user)){
              $user_id = $user->id;
            }
            else{
              $user_id = '';
            }
            //  print_r($user_id);
            // die;
          }
        }
        
        $product =  Product::create([
            'category_id'       => $category_id,
            'child_category_id' => $sub_category_id,
            'user_id'           => $user_id,
            'name'              => $row['name'],
            'sku'               => $row['sku'],
            'short_description' => $row['short_description'],
            'full_description'  => $row['full_description'],
            'attribute_type_id' => $attribute_type_ids,
            'attribute'         => $json_data,
            'language_id'       => 1,
        ]);
        if(!empty($finalarray)){
          foreach ($finalarray as $pfkey => $pfvalue) {
            $insert = array();
            $insert['product_id'] = $product->id;
            $insert['sale_price'] = $pfvalue['sprice'];
            $insert['regular_price'] = $pfvalue['rprice'];
            $insert['quantity']   = $pfvalue['quantity'];
            $insert['attribute'] =  json_encode($pfvalue);
            $product_variation = ProductVariation::create($insert);
          }
        }
        $images = explode(", ",$row['image']);
        foreach ($images as $key => $image) {
          $path = $image;
          $filename = uniqid() . '_' .basename($path);
          Image::make($path)->save(storage_path('tmp/uploads/' . $filename));
          $Gallery = Gallery::create([
            'image' => $filename, 
            'module_type' => 0, 
            'module_id' => $product->id,
          ]);
        }
        return $product;
    }

     

    public function rules(): array
    {

      // if(!empty('image')){
      //   $image_ext = explode(',', 'image');
      //   print_r($image_ext);
      //   die;
      //   if($image_ext[1] == 'jpg'){
      //     $extention = 'mimes:jpg';
      //   }
      // }
        return [
            'name'           => 'required|max:150',
            'category'       => 'required',
            'child_category' => 'required',
            'sku'            => 'required|unique:product',
            'image'          => 'import_img',
            'sale_price'     => 'required',
            'attribute_type' => 'required',
            'attribute'      => 'required',
            'price'          =>'required_with:sale_price|greater_than_field:sale_price',
        ];
    }
   
    public function image()
    {
        return explode(', ', $this->get('image'));
    }
    /**
      * @return array
   */
  public function customValidationMessages()
  {
      return [
        'image.import_img' => 'The image type must be jpg, jpeg, png',
        'price.greater_than_field'      => 'The price must be at least sale price.',
      ];
  }



    // public function boot()
    // {
    //   Validator::extend('image', function ($attribute, $value, $parameters, $validator)
    //   {
    //       // put keywords into array
    //       $images = explode(',', $value);

    //       foreach($images as $image)
    //       {
    //          $extention = explode('.', $image);
    //           if($extention == 'jpg')
    //           {
    //               return false;
    //           }
    //       }

    //       return true;
    //   }
    // }
}
